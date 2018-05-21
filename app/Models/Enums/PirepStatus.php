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
        PirepStatus::INITIATED       => 'pireps.status.initialized',
        PirepStatus::SCHEDULED       => 'pireps.status.scheduled',
        PirepStatus::BOARDING        => 'pireps.status.boarding',
        PirepStatus::RDY_START       => 'pireps.status.ready_start',
        PirepStatus::PUSHBACK_TOW    => 'pireps.status.push_tow',
        PirepStatus::DEPARTED        => 'pireps.status.departed',
        PirepStatus::RDY_DEICE       => 'pireps.status.ready_deice',
        PirepStatus::STRT_DEICE      => 'pireps.status.deicing',
        PirepStatus::GRND_RTRN       => 'pireps.status.ground_ret',
        PirepStatus::TAXI            => 'pireps.status.taxi',
        PirepStatus::TAKEOFF         => 'pireps.status.takeoff',
        PirepStatus::INIT_CLIM       => 'pireps.status.initial_clb',
        PirepStatus::AIRBORNE        => 'pireps.status.enroute',
        PirepStatus::ENROUTE         => 'pireps.status.enroute',
        PirepStatus::DIVERTED        => 'pireps.status.diverted',
        PirepStatus::APPROACH        => 'pireps.status.approach',
        PirepStatus::APPROACH_ICAO   => 'pireps.status.approach',
        PirepStatus::ON_FINAL        => 'pireps.status.final_appr',
        PirepStatus::LANDING         => 'pireps.status.landing',
        PirepStatus::LANDED          => 'pireps.status.landed',
        PirepStatus::ARRIVED         => 'pireps.status.arrived',
        PirepStatus::CANCELLED       => 'pireps.status.cancelled',
        PirepStatus::EMERG_DECENT    => 'pireps.status.emerg_decent',
    ];
}
