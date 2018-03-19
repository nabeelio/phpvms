<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class FlightType
 * @package App\Models\Enums
 */
class FlightType extends Enum {

    public const PASSENGER   = 0;
    public const CARGO       = 1;
    public const CHARTER     = 2;

    protected static $labels = [
        FlightType::PASSENGER    => 'Passenger',
        FlightType::CARGO        => 'Cargo',
        FlightType::CHARTER      => 'Charter',
    ];
}
