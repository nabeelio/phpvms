<?php

namespace App\Services;

use App\Contracts\Service;
use App\Models\Airline;
use App\Models\JournalTransaction;
use App\Support\Money;

class FinanceService extends Service
{
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
