<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class ExpenseType
 * @package App\Models\Enums
 */
class ExpenseType extends Enum
{
    public const FLIGHT  = 0;
    public const DAILY   = 1;
    public const MONTHLY = 2;

    protected static $labels = [
        ExpenseType::FLIGHT  => 'Flight',
        ExpenseType::DAILY   => 'Daily',
        ExpenseType::MONTHLY => 'Monthly',
    ];

    protected static $codes = [
        ExpenseType::FLIGHT  => 'F',
        ExpenseType::DAILY   =>'D',
        ExpenseType::MONTHLY => 'M',
    ];
}
