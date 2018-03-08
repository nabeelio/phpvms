<?php

namespace App\Models\Enums;

/**
 * Class AircraftState
 * @package App\Models\Enums
 */
class AircraftStatus extends Enum
{
    public const STORED = 0;
    public const ACTIVE = 1;
    public const RETIRED = 2;
    public const SCRAPPED = 3;
    public const WRITTEN_OFF = 4;

    public static $labels = [
        AircraftStatus::STORED => 'Stored',
        AircraftStatus::ACTIVE => 'Active',
        AircraftStatus::RETIRED => 'Retired',
        AircraftStatus::SCRAPPED => 'Scrapped',
        AircraftStatus::WRITTEN_OFF => 'Written Off',
    ];
}
