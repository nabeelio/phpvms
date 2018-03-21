<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class FlightType
 * @package App\Models\Enums
 */
class FlightType extends Enum
{
    public const PASSENGER = 0;
    public const CARGO     = 1;
    public const CHARTER   = 2;

    protected static $labels = [
        FlightType::PASSENGER => 'Passenger',
        FlightType::CARGO     => 'Cargo',
        FlightType::CHARTER   => 'Charter',
    ];

    /**
     * Return value from P, C or H
     * @param $code
     * @return int
     */
    public static function getFromCode($code): int
    {
        if(is_numeric($code)) {
            return (int) $code;
        }

        $code = strtolower($code);
        if($code === 'p') {
            return self::PASSENGER;
        }

        if ($code === 'c') {
            return self::CARGO;
        }

        if($code === 'h') {
            return self::CHARTER;
        }

        return self::PASSENGER;
    }
}
