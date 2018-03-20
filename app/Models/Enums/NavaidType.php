<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class NavaidType
 * Types based on/compatible with OpenFMC
 * https://github.com/skiselkov/openfmc/blob/master/airac.h
 * @package App\Models\Enums
 */
class NavaidType extends Enum
{
    public const VOR          = 1 << 0;
    public const VOR_DME      = 1 << 1;
    public const LOC          = 1 << 4;
    public const LOC_DME      = 1 << 5;
    public const NDB          = 1 << 6;
    public const TACAN        = 1 << 7;
    public const UNKNOWN      = 1 << 8;
    public const INNER_MARKER = 1 << 9;
    public const OUTER_MARKER = 1 << 10;
    public const FIX          = 1 << 11;
    public const ANY_VOR      = NavaidType::VOR | NavaidType::VOR_DME;
    public const ANY_LOC      = NavaidType::LOC | NavaidType::LOC_DME;
    public const ANY          = (NavaidType::UNKNOWN << 1) - 1;

    /**
     * Names and titles
     * @var array
     */
    public static $labels = [
        NavaidType::VOR     => 'VOR',
        NavaidType::VOR_DME => 'VOR DME',
        NavaidType::LOC     => 'Localizer',
        NavaidType::LOC_DME => 'Localizer DME',
        NavaidType::NDB     => 'Non-directional Beacon',
        NavaidType::TACAN   => 'TACAN',
        NavaidType::UNKNOWN => 'Unknown',
        NavaidType::ANY_VOR => 'VOR',
        NavaidType::ANY_LOC => 'Localizer',
    ];

    public static $icons = [
        NavaidType::VOR     => 'VOR',
        NavaidType::VOR_DME => 'VOR DME',
        NavaidType::LOC     => 'Localizer',
        NavaidType::LOC_DME => 'Localizer DME',
        NavaidType::NDB     => 'Non-directional Beacon',
        NavaidType::TACAN   => 'TACAN',
        NavaidType::UNKNOWN => 'Unknown',
        NavaidType::ANY_VOR => 'VOR',
        NavaidType::ANY_LOC => 'Localizer',
    ];
}
