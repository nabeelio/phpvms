<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Tied to the ACARS statuses/states.
 * Corresponds to values from AIDX
 * @package App\Models\Enums
 */
class PirepStatus extends Enum
{
    public const INITIATED  = 'INI';
    public const SCHEDULED  = 'SCH';
    public const BOARDING   = 'BST';
    public const RDY_START  = 'RDT';
    public const DEPARTED   = 'OFB'; // Off block
    public const RDY_DEICE  = 'DIR';
    public const STRT_DEICE = 'DIC';
    public const GRND_RTRN  = 'GRT';
    public const AIRBORNE   = 'TKO';
    public const DIVERTED   = 'DV';
    public const APPROACH   = 'TEN';
    public const ON_FINAL   = 'FIN';
    public const LANDED     = 'LAN';
    public const ARRIVED    = 'ONB'; // On block
    public const CANCELLED  = 'DX';

    protected static $labels = [
        PirepStatus::INITIATED  => 'system.pireps.status.initialized',
        PirepStatus::SCHEDULED  => 'system.pireps.status.scheduled',
        PirepStatus::BOARDING   => 'system.pireps.status.boarding',
        PirepStatus::RDY_START  => 'system.pireps.status.ready_start',
        PirepStatus::DEPARTED   => 'system.pireps.status.departed',
        PirepStatus::RDY_DEICE  => 'system.pireps.status.ready_deice',
        PirepStatus::STRT_DEICE => 'system.pireps.status.deicing',
        PirepStatus::GRND_RTRN  => 'system.pireps.status.ground_ret',
        PirepStatus::AIRBORNE   => 'system.pireps.status.enroute',
        PirepStatus::DIVERTED   => 'system.pireps.status.diverted',
        PirepStatus::APPROACH   => 'system.pireps.status.approach',
        PirepStatus::ON_FINAL   => 'system.pireps.status.final_appr',
        PirepStatus::LANDED     => 'system.pireps.status.landed',
        PirepStatus::ARRIVED    => 'system.pireps.status.arrived',
        PirepStatus::CANCELLED  => 'system.pireps.status.cancelled',
    ];
}
