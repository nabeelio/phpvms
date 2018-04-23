<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class AircraftState
 * @package App\Models\Enums
 */
class AircraftStatus extends Enum
{
    public const ACTIVE      = 'A';
    public const STORED      = 'S';
    public const RETIRED     = 'R';
    public const SCRAPPED    = 'C';
    public const WRITTEN_OFF = 'W';

    public static $labels = [
        AircraftStatus::ACTIVE      => 'system.aircraft.status.active',
        AircraftStatus::STORED      => 'system.aircraft.status.stored',
        AircraftStatus::RETIRED     => 'system.aircraft.status.retired',
        AircraftStatus::SCRAPPED    => 'system.aircraft.status.scrapped',
        AircraftStatus::WRITTEN_OFF => 'system.aircraft.status.written',
    ];
}
