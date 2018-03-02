<?php

namespace App\Services;

use App\Events\Expenses as ExpensesEvent;
use App\Models\Enums\ExpenseType;
use App\Models\Enums\PirepSource;
use App\Models\Expense;
use App\Models\Pirep;
use App\Repositories\ExpenseRepository;
use App\Repositories\JournalRepository;
use App\Support\Math;
use App\Support\Money;
use Log;

/**
 * Class FinanceService
 * @package App\Services
 *
 */
class FinanceService extends BaseService
{
    private $expenseRepo,
            $fareSvc,
            $journalRepo,
            $pirepSvc;

    /**
     * FinanceService constructor.
     * @param ExpenseRepository $expenseRepo
     * @param FareService $fareSvc
     * @param JournalRepository $journalRepo
     * @param PIREPService $pirepSvc
     */
    public function __construct(
        ExpenseRepository $expenseRepo,
        FareService $fareSvc,
        JournalRepository $journalRepo,
        PIREPService $pirepSvc
    ) {
        $this->expenseRepo = $expenseRepo;
        $this->fareSvc = $fareSvc;
        $this->journalRepo = $journalRepo;
        $this->pirepSvc = $pirepSvc;
    }

    /**
     * Determine from the base rate, if we want to return the overridden rate
     * or if the overridden rate is a percentage, then return that amount
     * @param $base_rate
     * @param $override_rate
     * @return float|null
     */
    public function applyAmountOrPercent($base_rate, $override_rate=null): ?float
    {
        if (!$override_rate) {
            return $base_rate;
        }

        # Not a percentage override
        if (substr_count($override_rate, '%') === 0) {
            return $override_rate;
        }

        return Math::addPercent($base_rate, $override_rate);
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
        if(!$pirep->airline->journal) {
            $pirep->airline->journal = $pirep->airline->initJournal(config('phpvms.currency'));
        }

        if (!$pirep->user->journal) {
            $pirep->user->journal = $pirep->user->initJournal(config('phpvms.currency'));
        }

        $this->payFaresForPirep($pirep);
        $this->payExpensesForPirep($pirep);
        $this->payGroundHandlingForPirep($pirep);
        $this->payPilotForPirep($pirep);

        $pirep->airline->journal->refresh();
        $pirep->user->journal->refresh();

        return $pirep;
    }

    /**
     * Collect all of the fares and then post each fare class's profit and
     * the costs for each seat and post it to the journal
     * @param $pirep
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function payFaresForPirep($pirep): void
    {
        $fares = $this->getReconciledFaresForPirep($pirep);
        foreach ($fares as $fare) {

            Log::info('Finance: PIREP: ' . $pirep->id . ', fare:', $fare->toArray());

            $credit = Money::createFromAmount($fare->count * $fare->price);
            $debit = Money::createFromAmount($fare->count * $fare->cost);

            $this->journalRepo->post(
                $pirep->airline->journal,
                $credit,
                $debit,
                $pirep,
                'Fares ' . $fare->code . $fare->count
                . '; price:' . $fare->price . ', cost: ' . $fare->cost,
                null,
                'fares'
            );
        }
    }

    /**
     * Collect all of the expenses and apply those to the journal
     * @param Pirep $pirep
     * @throws \InvalidArgumentException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function payExpensesForPirep(Pirep $pirep): void
    {
        $expenses = $this->getExpenses($pirep);
        foreach ($expenses as $expense) {

            Log::info('Finance: PIREP: ' . $pirep->id . ', expense:', $expense->toArray());

            $debit = Money::createFromAmount($expense->amount);
            $this->journalRepo->post(
                $pirep->airline->journal,
                null,
                $debit,
                $pirep,
                'Expense: ' . $expense->name,
                null,
                'expenses'
            );
        }
    }

    /**
     * Collect and apply the ground handling cost
     * @param Pirep $pirep
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function payGroundHandlingForPirep(Pirep $pirep)
    {
        $ground_handling_cost = $this->getGroundHandlingCost($pirep);
        $this->journalRepo->post(
            $pirep->airline->journal,
            null,
            Money::createFromAmount($ground_handling_cost),
            $pirep,
            'Ground handling',
            null,
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
    public function payPilotForPirep(Pirep $pirep)
    {
        $pilot_pay = $this->getPilotPay($pirep);
        $pilot_pay_rate = $this->getPilotPayRateForPirep($pirep);
        $memo = 'Pilot Payment @ ' . $pilot_pay_rate;

        $this->journalRepo->post(
            $pirep->airline->journal,
            null,
            $pilot_pay,
            $pirep,
            $memo,
            null,
            'pilot_pay'
        );

        $this->journalRepo->post(
            $pirep->user->journal,
            $pilot_pay,
            null,
            $pirep,
            $memo,
            null,
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
        $flight = $this->pirepSvc->findFlight($pirep);

        # Collect all of the fares and prices
        $flight_fares = $this->fareSvc->getForPirep($pirep);
        $all_fares = $this->fareSvc->getAllFares($flight, $pirep->aircraft->subfleet);

        $fares = $all_fares->map(function($fare, $i) use ($flight_fares) {

            $fare_count = $flight_fares->whereStrict('id', $fare->id)->first();

            if($fare_count) {
                # If the count is greater than capacity, then just set it
                # to the maximum amount
                if($fare_count->count > $fare->capacity) {
                    $fare->count = $fare->capacity;
                } else {
                    $fare->count = $fare_count->count;
                }
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
        if(filled($pirep->aircraft->subfleet->ground_handling_multiplier)) {
            // force into percent mode
            $multiplier = $pirep->aircraft->subfleet->ground_handling_multiplier.'%';
            return $this->applyAmountOrPercent(
                $pirep->arr_airport->ground_handling_cost,
                $multiplier
            );
        }

        return $pirep->arr_airport->ground_handling_cost;
    }

    /**
     * Send out an event called ExpensesEvent, which picks up any
     * event listeners and check if they return a list of additional
     * Expense model objects.
     * @param Pirep $pirep
     * @return mixed
     */
    public function getExpenses(Pirep $pirep)
    {
        $event_expenses = [];

        $expenses = $this->expenseRepo
            ->getAllForType(ExpenseType::FLIGHT, $pirep->airline_id);

        /**
         * Go through the expenses and apply a mulitplier if present
         */
        $expenses = $expenses->map(function($expense, $i) use ($pirep) {
            if(!$expense->multiplier) {
                return $expense;
            }

            // TODO Apply the multiplier from the subfleet
            return $expense;
        });

        $gathered_expenses = event(new ExpensesEvent($pirep));
        if (!\is_array($gathered_expenses)) {
            return $expenses;
        }

        foreach ($gathered_expenses as $event_expense) {
            if (!\is_array($event_expense)) {
                continue;
            }

            foreach($event_expense as $expense) {
                # Make sure it's of type expense Model
                if(!($expense instanceof Expense)) {
                    continue;
                }

                # If an airline_id is filled, then see if it matches
                if($expense->airline_id !== $pirep->airline_id) {
                    continue;
                }

                $event_expenses[] = $expense;
            }
        }

        $expenses = $expenses->concat($event_expenses);

        return $expenses;
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
            ->first()
            ->pivot;

        if($pirep->source === PirepSource::ACARS) {
            Log::debug('Source is ACARS');
            $base_rate = $rank->acars_base_pay_rate;
            $override_rate = $override_rate->acars_pay;
        } else {
            Log::debug('Source is Manual');
            $base_rate = $rank->manual_base_pay_rate;
            $override_rate = $override_rate->manual_pay;
        }

        Log::debug('pilot pay: base rate=' . $base_rate . ', override=' . $override_rate);
        return $this->applyAmountOrPercent(
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
