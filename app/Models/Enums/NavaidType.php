<?php
/**
 * The types of navaids
 */

namespace App\Models\Enums;

/**
 * Class NavaidType
 * Types based on/compatible with OpenFMC
 * https://github.com/skiselkov/openfmc/blob/master/airac.h
 * @package App\Models\Enums
 */
class NavaidType extends EnumBase
{
    const VOR           = 1 << 0;
    const VOR_DME       = 1 << 1;
    const LOC           = 1 << 4;
    const LOC_DME       = 1 << 5;
    const NDB           = 1 << 6;
    const TACAN         = 1 << 7;
    const UNKNOWN       = 1 << 8;
    const INNER_MARKER  = 1 << 9;
    const OUTER_MARKER  = 1 << 10;
    const FIX           = 1 << 11;
    const ANY_VOR   = NavaidType::VOR | NavaidType::VOR_DME;
    const ANY_LOC   = NavaidType::LOC | NavaidType::LOC_DME;
    const ANY       = (NavaidType::UNKNOWN << 1) - 1;

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
}
