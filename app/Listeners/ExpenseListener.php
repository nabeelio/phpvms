<?php

namespace App\Listeners;

use App\Events\Expenses;
use App\Models\Enums\ExpenseType;
use App\Models\Expense;

class ExpenseListener
{
    /**
     * Return a list of additional expenses
     * @param Expenses $event
     * @return array
     */
    public function handle(Expenses $event)
    {
        return [
            new Expense([
                'type' => ExpenseType::FLIGHT,
                'amount' => 15000  # $150
            ]),
        ];
    }
}
