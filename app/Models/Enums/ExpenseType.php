<?php

namespace App\Models\Enums;

use App\Contracts\Enum;

/**
 * Class ExpenseType
 */
class ExpenseType extends Enum
{
    public const FLIGHT = 'F';
    public const DAILY = 'D';
    public const MONTHLY = 'M';

    protected static $labels = [
        self::FLIGHT  => 'expenses.type.flight',
        self::DAILY   => 'expenses.type.daily',
        self::MONTHLY => 'expenses.type.monthly',
    ];

    protected static $codes = [
        self::FLIGHT  => 'F',
        self::DAILY   => 'D',
        self::MONTHLY => 'M',
    ];
}
