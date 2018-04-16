<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Tied to the ACARS statuses/states.
 * Corresponds to values from AIDX and ICAO ADREP
 * https://www.skybrary.aero/index.php/Flight_Phase_Taxonomy
 * @package App\Models\Enums
 */
class PirepStatus extends Enum
{
    public const INITIATED     = 'INI';
    public const SCHEDULED     = 'SCH';
    public const BOARDING      = 'BST';
    public const RDY_START     = 'RDT';
    public const PUSHBACK_TOW  = 'PBT';
    public const DEPARTED      = 'OFB'; // Off block
    public const RDY_DEICE     = 'DIR';
    public const STRT_DEICE    = 'DIC';
    public const GRND_RTRN     = 'GRT'; // Ground return
    public const TAXI          = 'TXI'; // Taxi
    public const TAKEOFF       = 'TOF';
    public const INIT_CLIM     = 'ICL';
    public const AIRBORNE      = 'TKO';
    public const ENROUTE       = 'ENR';
    public const DIVERTED      = 'DV';
    public const APPROACH      = 'TEN';
    public const APPROACH_ICAO = 'APR';
    public const ON_FINAL      = 'FIN';
    public const LANDING       = 'LDG';
    public const LANDED        = 'LAN';
    public const ARRIVED       = 'ONB'; // On block
    public const CANCELLED     = 'DX';
    public const EMERG_DECENT  = 'EMG';

    protected static $labels = [
        PirepStatus::INITIATED       => 'system.pireps.status.initialized',
        PirepStatus::SCHEDULED       => 'system.pireps.status.scheduled',
        PirepStatus::BOARDING        => 'system.pireps.status.boarding',
        PirepStatus::RDY_START       => 'system.pireps.status.ready_start',
        PirepStatus::PUSHBACK_TOW    => 'system.pireps.status.push_tow',
        PirepStatus::DEPARTED        => 'system.pireps.status.departed',
        PirepStatus::RDY_DEICE       => 'system.pireps.status.ready_deice',
        PirepStatus::STRT_DEICE      => 'system.pireps.status.deicing',
        PirepStatus::GRND_RTRN       => 'system.pireps.status.ground_ret',
        PirepStatus::TAXI            => 'system.pireps.status.taxi',
        PirepStatus::TAKEOFF         => 'system.pireps.status.takeoff',
        PirepStatus::INIT_CLIM       => 'system.pireps.status.initial_clb',
        PirepStatus::AIRBORNE        => 'system.pireps.status.enroute',
        PirepStatus::ENROUTE         => 'system.pireps.status.enroute',
        PirepStatus::DIVERTED        => 'system.pireps.status.diverted',
        PirepStatus::APPROACH        => 'system.pireps.status.approach',
        PirepStatus::APPROACH_ICAO   => 'system.pireps.status.approach',
        PirepStatus::ON_FINAL        => 'system.pireps.status.final_appr',
        PirepStatus::LANDING         => 'system.pireps.status.landing',
        PirepStatus::LANDED          => 'system.pireps.status.landed',
        PirepStatus::ARRIVED         => 'system.pireps.status.arrived',
        PirepStatus::CANCELLED       => 'system.pireps.status.cancelled',
        PirepStatus::EMERG_DECENT    => 'system.pireps.status.emerg_decent',
    ];
}
