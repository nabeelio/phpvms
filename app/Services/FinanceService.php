<?php

namespace App\Services;

use App\Contracts\Service;
use App\Models\Airline;
use App\Models\Journal;
use App\Models\JournalTransaction;
use App\Repositories\JournalRepository;
use App\Support\Money;

class FinanceService extends Service
{
    private $journalRepo;

    public function __construct(JournalRepository $journalRepo)
    {
        $this->journalRepo = $journalRepo;
    }

    /**
     * Credit some amount to a given journal
     * E.g, some amount for expenses or ground handling fees, etc. Example, to pay a user a dollar
     * for a pirep:
     *
     * creditToJournal($user->journal, new Money(1000), $pirep, 'Payment', 'pirep', 'payment');
     *
     * @param \App\Models\Journal                 $journal
     * @param Money                               $amount
     * @param \Illuminate\Database\Eloquent\Model $reference
     * @param string                              $memo
     * @param string                              $transaction_group
     * @param string|array                        $tag
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @return mixed
     */
    public function creditToJournal(
        Journal $journal,
        Money $amount,
        $reference,
        $memo,
        $transaction_group,
        $tag
    ) {
        return $this->journalRepo->post(
            $journal,
            $amount,
            null,
            $reference,
            $memo,
            null,
            $transaction_group,
            $tag
        );
    }

    /**
     * Charge some expense for a given PIREP to the airline its file against
     * E.g, some amount for expenses or ground handling fees, etc.
     *
     * @param \App\Models\Journal                 $journal
     * @param Money                               $amount
     * @param \Illuminate\Database\Eloquent\Model $reference
     * @param string                              $memo
     * @param string                              $transaction_group
     * @param string|array                        $tag
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @return mixed
     */
    public function debitFromJournal(
        Journal $journal, Money $amount, $reference, $memo, $transaction_group, $tag
    ) {
        return $this->journalRepo->post(
            $journal,
            null,
            $amount,
            $reference,
            $memo,
            null,
            $transaction_group,
            $tag
        );
    }

    /**
     * Get all of the transactions for an airline between two given dates. Returns an array
     * with `credits`, `debits` and `transactions` fields, where transactions contains the
     * grouped transactions (e.g, "Fares" and "Ground Handling", etc)
     *
     * @param Airline $airline
     * @param string  $start_date YYYY-MM-DD
     * @param string  $end_date   YYYY-MM-DD
     *
     * @return array
     */
    public function getAirlineTransactionsBetween($airline, $start_date, $end_date)
    {
        // Return all the transactions, grouped by the transaction group
        $transactions = JournalTransaction::groupBy('transaction_group', 'currency')
            ->selectRaw('transaction_group, 
                         currency, 
                         SUM(credit) as sum_credits, 
                         SUM(debit) as sum_debits')
            ->where(['journal_id' => $airline->journal->id])
            ->whereBetween('created_at', [$start_date, $end_date], 'AND')
            ->orderBy('sum_credits', 'desc')
            ->orderBy('sum_debits', 'desc')
            ->orderBy('transaction_group', 'asc')
            ->get();

        // Summate it so we can show it on the footer of the table
        $sum_all_credits = 0;
        $sum_all_debits = 0;
        foreach ($transactions as $ta) {
            $sum_all_credits += $ta->sum_credits ?? 0;
            $sum_all_debits += $ta->sum_debits ?? 0;
        }

        return [
            'airline'      => $airline,
            'credits'      => new Money($sum_all_credits),
            'debits'       => new Money($sum_all_debits),
            'transactions' => $transactions,
        ];
    }
}
