<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class ExpenseType
 * @package App\Models\Enums
 */
class ExpenseType extends Enum
{
    public const FLIGHT  = 'F';
    public const DAILY   = 'D';
    public const MONTHLY = 'M';

    protected static $labels = [
        ExpenseType::FLIGHT  => 'system.expenses.type.flight',
        ExpenseType::DAILY   => 'system.expenses.type.daily',
        ExpenseType::MONTHLY => 'system.expenses.type.monthly',
    ];

    protected static $codes = [
        ExpenseType::FLIGHT  => 'F',
        ExpenseType::DAILY   =>'D',
        ExpenseType::MONTHLY => 'M',
    ];
}
