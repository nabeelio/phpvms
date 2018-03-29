<?php

namespace App\Services\Finance;

use App\Events\Expenses as ExpensesEvent;
use App\Interfaces\Service;
use App\Models\Enums\ExpenseType;
use App\Models\Enums\PirepSource;
use App\Models\Expense;
use App\Models\Pirep;
use App\Repositories\ExpenseRepository;
use App\Repositories\JournalRepository;
use App\Services\FareService;
use App\Services\PirepService;
use App\Support\Math;
use App\Support\Money;
use Log;

/**
 * Class FinanceService
 * @package App\Services
 *
 */
class PirepFinanceService extends Service
{
    private $expenseRepo,
            $fareSvc,
            $journalRepo,
            $pirepSvc;

    /**
     * FinanceService constructor.
     * @param ExpenseRepository $expenseRepo
     * @param FareService       $fareSvc
     * @param JournalRepository $journalRepo
     * @param PirepService      $pirepSvc
     */
    public function __construct(
        ExpenseRepository $expenseRepo,
        FareService $fareSvc,
        JournalRepository $journalRepo,
        PirepService $pirepSvc
    )
    {
        $this->expenseRepo = $expenseRepo;
        $this->fareSvc = $fareSvc;
        $this->journalRepo = $journalRepo;
        $this->pirepSvc = $pirepSvc;
    }

    /**
     * Process all of the finances for a pilot report. This is called
     * from a listener (FinanceEvents)
     * @param Pirep $pirep
     * @return mixed
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Exception
     */
    public function processFinancesForPirep(Pirep $pirep)
    {
        if (!$pirep->airline->journal) {
            $pirep->airline->journal = $pirep->airline->initJournal(config('phpvms.currency'));
        }

        if (!$pirep->user->journal) {
            $pirep->user->journal = $pirep->user->initJournal(config('phpvms.currency'));
        }

        # Clean out the expenses first
        $this->deleteFinancesForPirep($pirep);

        Log::info('Finance: Starting PIREP pay for '.$pirep->id);

        # Now start and pay from scratch
        $this->payFaresForPirep($pirep);
        $this->payExpensesForSubfleet($pirep);
        $this->payExpensesForPirep($pirep);
        $this->payExpensesEventsForPirep($pirep);
        $this->payGroundHandlingForPirep($pirep);
        $this->payPilotForPirep($pirep);

        $pirep->airline->journal->refresh();
        $pirep->user->journal->refresh();

        // Recalculate balances...
        $this->journalRepo->recalculateBalance($pirep->airline->journal);
        $this->journalRepo->recalculateBalance($pirep->user->journal);

        return $pirep;
    }

    /**
     * @param Pirep $pirep
     */
    public function deleteFinancesForPirep(Pirep $pirep): void
    {
        $this->journalRepo->deleteAllForObject($pirep);
    }

    /**
     * Collect all of the fares and then post each fare class's profit and
     * the costs for each seat and post it to the journal
     * @param $pirep
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function payFaresForPirep($pirep): void
    {
        $fares = $this->getReconciledFaresForPirep($pirep);

        /** @var \App\Models\Fare $fare */
        foreach ($fares as $fare) {
            Log::info('Finance: PIREP: '.$pirep->id.', Fare:', $fare->toArray());

            $credit = Money::createFromAmount($fare->count * $fare->price);
            $debit = Money::createFromAmount($fare->count * $fare->cost);

            Log::info('Finance: Calculate: C='.$credit->toAmount().', D='.$debit->toAmount());

            $this->journalRepo->post(
                $pirep->airline->journal,
                $credit,
                $debit,
                $pirep,
                'Fares '.$fare->code.$fare->count
                .'; price: '.$fare->price.', cost: '.$fare->cost,
                null,
                'Fares',
                'fare'
            );
        }
    }

    /**
     * Calculate what the cost is for the operating an aircraft
     * in this subfleet, as-per the block time
     * @param Pirep $pirep
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function payExpensesForSubfleet(Pirep $pirep): void
    {
        $sf = $pirep->aircraft->subfleet;

        # Haven't entered a cost
        if (!filled($sf->cost_block_hour)) {
            return;
        }

        # Convert to cost per-minute
        $cost_per_min = round($sf->cost_block_hour / 60, 2);

        # Time to use - use the block time if it's there, actual
        # flight time if that hasn't been used
        $block_time = $pirep->block_time;
        if(!filled($block_time)) {
            Log::info('Finance: No block time, using PIREP flight time');
            $block_time = $pirep->flight_time;
        }

        $debit = Money::createFromAmount($cost_per_min * $block_time);
        Log::info('Finance: Subfleet Block Hourly, D='.$debit->getAmount());

        $this->journalRepo->post(
            $pirep->airline->journal,
            null,
            $debit,
            $pirep,
            'Subfleet '.$sf->type.': Block Time Cost',
            null,
            'Subfleet '.$sf->type,
            'subfleet'
        );
    }

    /**
     * Collect all of the expenses and apply those to the journal
     * @param Pirep $pirep
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function payExpensesForPirep(Pirep $pirep): void
    {
        $expenses = $this->expenseRepo->getAllForType(
            ExpenseType::FLIGHT,
            $pirep->airline_id
        );

        /**
         * Go through the expenses and apply a mulitplier if present
         */
        $expenses->map(function ($expense, $i) use ($pirep) {
            /*if ($expense->multiplier) {
                # TODO: Modify the amount
            }*/

            Log::info('Finance: PIREP: '.$pirep->id.', expense:', $expense->toArray());

            # Get the transaction group name from the ref_class name
            # This way it can be more dynamic and don't have to add special
            # tables or specific expense calls to accomodate all of these
            $klass = 'Expense';
            if ($expense->ref_class) {
                $ref = explode('\\', $expense->ref_class);
                $klass = end($ref);
            }

            # Form the memo, with some specific ones depending on the group
            if ($klass === 'Airport') {
                $memo = "Airport Expense: {$expense->name} ({$expense->ref_class_id})";
                $transaction_group = "Airport: {$expense->ref_class_id}";
            } elseif ($klass === 'Subfleet') {
                $memo = "Subfleet Expense: {$expense->name} ({$pirep->aircraft->subfleet->name})";
                $transaction_group = "Subfleet: {$expense->name} ({$pirep->aircraft->subfleet->name})";
            } elseif ($klass === 'Aircraft') {
                $memo = "Aircraft Expense: {$expense->name} ({$pirep->aircraft->name})";
                $transaction_group = "Aircraft: {$expense->name} "
                    ."({$pirep->aircraft->name}-{$pirep->aircraft->registration})";
            } else {
                $memo = "Expense: {$expense->name}";
                $transaction_group = "Expense: {$expense->name}";
            }

            $debit = Money::createFromAmount($expense->amount);

            # If the expense is marked to charge it to a user (only applicable to Flight)
            # then change the journal to the user's to debit there
            $journal = $pirep->airline->journal;
            if ($expense->charge_to_user) {
                $journal = $pirep->user->journal;
            }

            $this->journalRepo->post(
                $journal,
                null,
                $debit,
                $pirep,
                $memo,
                null,
                $transaction_group,
                strtolower($klass)
            );
        });
    }

    /**
     * Collect all of the expenses from the listeners and apply those to the journal
     * @param Pirep $pirep
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function payExpensesEventsForPirep(Pirep $pirep): void
    {
        /**
         * Throw an event and collect any expenses returned from it
         */
        $gathered_expenses = event(new ExpensesEvent($pirep));
        if (!\is_array($gathered_expenses)) {
            return;
        }

        foreach ($gathered_expenses as $event_expense) {
            if (!\is_array($event_expense)) {
                continue;
            }

            foreach ($event_expense as $expense) {
                # Make sure it's of type expense Model
                if (!($expense instanceof Expense)) {
                    continue;
                }

                Log::info('Finance: Expense from listener, N="'
                    .$expense->name.'", A='.$expense->amount);

                # If an airline_id is filled, then see if it matches
                if(filled($expense->airline_id) && $expense->airline_id !== $pirep->airline_id) {
                    Log::info('Finance: Expense has an airline ID and it doesn\'t match, skipping');
                    continue;
                }

                $debit = Money::createFromAmount($expense->amount);

                $this->journalRepo->post(
                    $pirep->airline->journal,
                    null,
                    $debit,
                    $pirep,
                    'Expense: '.$expense->name,
                    null,
                    $expense->transaction_group ?? 'Expenses',
                    'expense'
                );
            }
        }
    }

    /**
     * Collect and apply the ground handling cost
     * @param Pirep $pirep
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function payGroundHandlingForPirep(Pirep $pirep): void
    {
        $ground_handling_cost = $this->getGroundHandlingCost($pirep);
        Log::info('Finance: PIREP: '.$pirep->id.'; ground handling: '.$ground_handling_cost);
        $this->journalRepo->post(
            $pirep->airline->journal,
            null,
            Money::createFromAmount($ground_handling_cost),
            $pirep,
            'Ground Handling',
            null,
            'Ground Handling',
            'ground_handling'
        );
    }

    /**
     * Figure out what the pilot pay is. Debit it from the airline journal
     * But also reference the PIREP
     * @param Pirep $pirep
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function payPilotForPirep(Pirep $pirep): void
    {
        $pilot_pay = $this->getPilotPay($pirep);
        $pilot_pay_rate = $this->getPilotPayRateForPirep($pirep);
        $memo = 'Pilot Payment @ '.$pilot_pay_rate;

        Log::info('Finance: PIREP: '.$pirep->id
            .'; pilot pay: '.$pilot_pay_rate.', total: '.$pilot_pay);

        $this->journalRepo->post(
            $pirep->airline->journal,
            null,
            $pilot_pay,
            $pirep,
            $memo,
            null,
            'Pilot Pay',
            'pilot_pay'
        );

        $this->journalRepo->post(
            $pirep->user->journal,
            $pilot_pay,
            null,
            $pirep,
            $memo,
            null,
            'Pilot Pay',
            'pilot_pay'
        );
    }

    /**
     * Return all of the fares for the PIREP. Reconcile the list;
     * Get the fares that have been filled out for the PIREP, and
     * then get the fares for the flight and subfleet. Then merge
     * them together, and return the final list of:
     *      count       = number of pax
     *      price       = how much each pax unit paid
     *      capacity    = max number of pax units
     *
     * If count > capacity, count will be adjusted to capacity
     * @param $pirep
     * @return \Illuminate\Support\Collection
     */
    public function getReconciledFaresForPirep($pirep)
    {
        # Collect all of the fares and prices
        $flight_fares = $this->fareSvc->getForPirep($pirep);
        Log::info('Finance: PIREP: '.$pirep->id.', flight fares: ', $flight_fares->toArray());

        $all_fares = $this->fareSvc->getAllFares($pirep->flight, $pirep->aircraft->subfleet);

        $fares = $all_fares->map(function ($fare, $i) use ($flight_fares, $pirep) {
            $fare_count = $flight_fares
                ->where('fare_id', $fare->id)
                ->first();

            if ($fare_count) {
                Log::info('Finance: PIREP: '.$pirep->id.', fare count: '.$fare_count);

                # If the count is greater than capacity, then just set it
                # to the maximum amount
                if ($fare_count->count > $fare->capacity) {
                    $fare->count = $fare->capacity;
                } else {
                    $fare->count = $fare_count->count;
                }
            } else {
                Log::info('Finance: PIREP: '.$pirep->id.', no fare count found', $fare->toArray());
            }

            return $fare;
        });

        return $fares;
    }

    /**
     * Return the costs for the ground handling, with the multiplier
     * being applied from the subfleet
     * @param Pirep $pirep
     * @return float|null
     */
    public function getGroundHandlingCost(Pirep $pirep)
    {
        if (filled($pirep->aircraft->subfleet->ground_handling_multiplier)) {
            // force into percent mode
            $multiplier = $pirep->aircraft->subfleet->ground_handling_multiplier.'%';

            return Math::applyAmountOrPercent(
                $pirep->arr_airport->ground_handling_cost,
                $multiplier
            );
        }

        return $pirep->arr_airport->ground_handling_cost;
    }

    /**
     * Return the pilot's hourly pay for the given PIREP
     * @param Pirep $pirep
     * @return float
     * @throws \InvalidArgumentException
     */
    public function getPilotPayRateForPirep(Pirep $pirep)
    {
        # Get the base rate for the rank
        $rank = $pirep->user->rank;
        $subfleet_id = $pirep->aircraft->subfleet_id;

        # find the right subfleet
        $override_rate = $rank->subfleets()
            ->where('subfleet_id', $subfleet_id)
            ->first();

        if ($override_rate) {
            $override_rate = $override_rate->pivot;
        }

        if ($pirep->source === PirepSource::ACARS) {
            Log::debug('Source is ACARS');
            $base_rate = $rank->acars_base_pay_rate;

            if ($override_rate) {
                $override_rate = $override_rate->acars_pay;
            }
        } else {
            Log::debug('Source is Manual');
            $base_rate = $rank->manual_base_pay_rate;

            if ($override_rate) {
                $override_rate = $override_rate->manual_pay;
            }
        }

        Log::debug('pilot pay: base rate='.$base_rate.', override='.$override_rate);

        return Math::applyAmountOrPercent(
            $base_rate,
            $override_rate
        );
    }

    /**
     * Get the user's payment amount for a PIREP
     * @param Pirep $pirep
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getPilotPay(Pirep $pirep)
    {
        $pilot_rate = $this->getPilotPayRateForPirep($pirep) / 60;
        $payment = round($pirep->flight_time * $pilot_rate, 2);

        Log::info('Pilot Payment: rate='.$pilot_rate);
        $payment = Money::convertToSubunit($payment);

        return new Money($payment);
    }
}
