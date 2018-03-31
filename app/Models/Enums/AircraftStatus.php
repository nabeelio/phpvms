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
        AircraftStatus::ACTIVE      => 'Active',
        AircraftStatus::STORED      => 'Stored',
        AircraftStatus::RETIRED     => 'Retired',
        AircraftStatus::SCRAPPED    => 'Scrapped',
        AircraftStatus::WRITTEN_OFF => 'Written Off',
    ];
}
