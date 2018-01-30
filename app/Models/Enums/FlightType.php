<?php

namespace App\Models\Enums;


class FlightType extends EnumBase {

    const PASSENGER   = 0;
    const CARGO       = 1;
    const CHARTER     = 2;

    protected static $labels = [
        FlightType::PASSENGER    => 'Passenger',
        FlightType::CARGO        => 'Cargo',
        FlightType::CHARTER      => 'Charter',
    ];
}
