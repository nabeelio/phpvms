<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class AircraftState
 */
class AircraftStatus extends Enum
{
    public const ACTIVE = 'A';
    public const STORED = 'S';
    public const RETIRED = 'R';
    public const SCRAPPED = 'C';
    public const WRITTEN_OFF = 'W';

    public static $labels = [
        self::ACTIVE      => 'aircraft.status.active',
        self::STORED      => 'aircraft.status.stored',
        self::RETIRED     => 'aircraft.status.retired',
        self::SCRAPPED    => 'aircraft.status.scrapped',
        self::WRITTEN_OFF => 'aircraft.status.written',
    ];
}
