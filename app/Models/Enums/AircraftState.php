<?php

namespace App\Models\Enums;

/**
 * Class AircraftState
 * @package App\Models\Enums
 */
class AircraftState extends EnumBase
{
    const PARKED = 0;
    const IN_USE = 1;
    const IN_AIR = 2;

    public static $labels = [
        AircraftState::PARKED    => 'On Ground',
        AircraftState::IN_USE    => 'In Use',
        AircraftState::IN_AIR    => 'In Air',
    ];
}
