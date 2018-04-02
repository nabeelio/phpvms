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
        PirepStatus::INITIATED  => 'Initiated',
        PirepStatus::SCHEDULED  => 'Scheduled',
        PirepStatus::BOARDING   => 'Boarding',
        PirepStatus::RDY_START  => 'Ready for start',
        PirepStatus::DEPARTED   => 'Departed',
        PirepStatus::RDY_DEICE  => 'Ready for de-icing',
        PirepStatus::STRT_DEICE => 'De-icing in progress',
        PirepStatus::GRND_RTRN  => 'Ground return',
        PirepStatus::AIRBORNE   => 'Enroute',
        PirepStatus::DIVERTED   => 'Diverted',
        PirepStatus::APPROACH   => 'Approach',
        PirepStatus::ON_FINAL   => 'Final approach',
        PirepStatus::LANDED     => 'Landed',
        PirepStatus::ARRIVED    => 'Arrived',
        PirepStatus::CANCELLED  => 'Cancelled',
    ];
}
