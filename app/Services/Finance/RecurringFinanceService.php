<?php

namespace App\Services\Finance;

use App\Contracts\Service;
use App\Models\Airline;
use App\Models\Enums\ExpenseType;
use App\Models\Expense;
use App\Models\Journal;
use App\Models\JournalTransaction;
use App\Services\FinanceService;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Process all of the daily expenses and charge them
 */
class RecurringFinanceService extends Service
{
    public function __construct(
        private readonly FinanceService $financeSvc
    ) {
    }

    /**
     * Determine the journal to charge to, otherwise, it's charged
     * to every airline journal
     *
     * @param Expense $expense
     *
     * @return Journal[]
     */
    protected function findJournals(Expense $expense)
    {
        \DB::enableQueryLog();
        if ($expense->airline_id) {
            /** @var Airline $airline */
            $journal = Journal::where([
                'morphed_type' => Airline::class,
                'morphed_id'   => $expense->airline_id])
                ->get();

            return $journal;
        }
        $airline_ids = [];
        $airlines = Airline::get(['id', 'icao']);
        foreach ($airlines as $airline) {
            $airline_ids[] = $airline->id;
        }

        $journals = Journal::where(['morphed_type' => Airline::class])
                ->whereIn('morphed_id', $airline_ids)
                ->get();

        return $journals;
    }

    /**
     * Get the name of the transaction group from the expense
     *
     * @param Expense $expense
     *
     * @return array
     */
    protected function getMemoAndGroup(Expense $expense): array
    {
        $klass = 'Expense';
        if ($expense->ref_model) {
            $ref = explode('\\', $expense->ref_model);
            $klass = end($ref);
            $obj = $expense->getReferencedObject();
        }

        if (empty($obj)) {
            return [null, null, null];
        }

        if ($klass === 'Airport') {
            $memo = "Airport Expense: {$expense->name} ({$expense->ref_model_id})";
            $transaction_group = "Airport: {$expense->ref_model_id}";
        } elseif ($klass === 'Subfleet') {
            $memo = "Subfleet Expense: {$expense->name}";
            $transaction_group = "Subfleet: {$expense->name}";
        } elseif ($klass === 'Aircraft') {
            $memo = "Aircraft Expense: {$expense->name} ({$obj->name})";
            $transaction_group = "Aircraft: {$expense->name} ({$obj->name}-{$obj->registration})";
        } else {
            $memo = "Expense: {$expense->name}";
            $transaction_group = "Expense: {$expense->name}";
        }

        return [$obj, $memo, $transaction_group];
    }

    /**
     * Run all of the daily expense/financials
     *
     * @param string $type
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function processExpenses(string $type = ExpenseType::DAILY): void
    {
        $expenses = Expense::where(['type' => $type])->get();

        $tag = 'expense_recurring';
        if ($type === ExpenseType::DAILY) {
            $tag = 'expenses_daily';
        } elseif ($type === ExpenseType::MONTHLY) {
            $tag = 'expenses_monthly';
        }

        /**
         * @var $expenses Expense[]
         */
        foreach ($expenses as $expense) {
            // Apply the expenses to the appropriate journals
            $journals = $this->findJournals($expense);
            foreach ($journals as $journal) {
                $amount = $expense->amount;

                // Has this expense already been charged? Check
                // against this specific journal, on today
                $w = [
                    'journal_id'   => $journal->id,
                    'ref_model'    => Expense::class,
                    'ref_model_id' => $expense->id,
                ];

                $ref = explode('\\', $expense->ref_model);
                $type = end($ref);

                $found = JournalTransaction::where($w)
                    ->whereDate('post_date', '=', Carbon::now('UTC')->toDateString())
                    ->count(['id']);

                if ($found > 0) {
                    Log::info('Expense "'.$expense->name.'" already charged for today, skipping');
                    continue;
                }

                [$obj, $memo, $ta_group] = $this->getMemoAndGroup($expense);
                if (empty($memo) || empty($ta_group)) {
                    continue;
                }

                // Determine if this object actually belongs to this airline or not

                $this->financeSvc->debitFromJournal(
                    $journal,
                    Money::createFromAmount($amount),
                    $expense,
                    $memo,
                    $ta_group,
                    $tag
                );

                Log::info('Expense memo: "'.$memo.'"; group: "'.$ta_group.'" charged!');
            }
        }
    }
}
