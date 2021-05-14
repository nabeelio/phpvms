<?php

namespace App\Listeners;

use App\Contracts\Listener;
use App\Events\Expenses;
use App\Models\Enums\ExpenseType;
use App\Models\Expense;

class ExpenseListener extends Listener
{
    /**
     * Return a list of additional expenses
     *
     * @param Expenses $event
     *
     * @return mixed
     */
    public function handle(Expenses $event)
    {
        $expenses = [];

        // This is an example of an expense to return
        // You have the pirep on $event->pirep, and any associated data
        // The transaction group is how it will show as a line item
        /*$expenses[] = new Expense([
            'type' => ExpenseType::FLIGHT,
            'amount' => 150,  # $150
            'transaction_group' => '',
            'charge_to_user' => true|false
        ]);*/

        return $expenses;
    }
}
