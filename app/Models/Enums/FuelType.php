<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class FuelType
 * @package App\Models\Enums
 */
class FuelType extends Enum
{
    public const LOW_LEAD = 0;
    public const JET_A    = 1;
    public const MOGAS    = 2;

    public static $labels = [
        FuelType::LOW_LEAD => '100LL',
        FuelType::JET_A    => 'JET A',
        FuelType::MOGAS    => 'MOGAS',
    ];
}
