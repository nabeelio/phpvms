<?php

namespace App\Models\Enums;

/**
 * Class AircraftState
 * @package App\Models\Enums
 */
class AircraftState extends Enum
{
    public const PARKED = 0;
    public const IN_USE = 1;
    public const IN_AIR = 2;

    public static $labels = [
        AircraftState::PARKED    => 'On Ground',
        AircraftState::IN_USE    => 'In Use',
        AircraftState::IN_AIR    => 'In Air',
    ];
}
