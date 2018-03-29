<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class FlightType
 * @package App\Models\Enums
 */
class FlightType extends Enum
{
    public const SCHED_PAX          = 'J';
    public const SCHED_CARGO        = 'F';
    public const CHARTER_PAX_ONLY   = 'C';
    public const ADDITIONAL_CARGO   = 'A';
    public const VIP                = 'E';
    public const ADDTL_PAX          = 'G';
    public const CHARTER_CARGO_MAIL = 'H';
    public const AMBULANCE          = 'I';
    public const TRAINING           = 'K';
    public const MAIL_SERVICE       = 'M';
    public const CHARTER_SPECIAL    = 'O';
    public const POSITIONING        = 'P';
    public const TECHNICAL_TEST     = 'T';
    public const MILITARY           = 'W';
    public const TECHNICAL_STOP     = 'X';

    protected static $labels = [
        FlightType::SCHED_PAX          => 'Passenger - Scheduled',
        FlightType::SCHED_CARGO        => 'Cargo - Scheduled',
        FlightType::CHARTER_PAX_ONLY   => 'Charter - Passenger Only',
        FlightType::ADDITIONAL_CARGO   => 'Additional Cargo/Mail',
        FlightType::VIP                => 'Special VIP Flight (FAA/Government)',
        FlightType::ADDTL_PAX          => 'Passenger - Additional',
        FlightType::CHARTER_CARGO_MAIL => 'Passenger - Additional',
        FlightType::AMBULANCE          => 'Ambulance Flight',
        FlightType::TRAINING           => 'Training Flight',
        FlightType::MAIL_SERVICE       => 'Mail Service',
        FlightType::CHARTER_SPECIAL    => 'Charter reqs Special Handling',
        FlightType::POSITIONING        => 'Positioning (Ferry/Delivery/Demo)',
        FlightType::TECHNICAL_TEST     => 'Technical Test',
        FlightType::MILITARY           => 'Military',
        FlightType::TECHNICAL_STOP     => 'Technical Stop',
    ];

    protected static $codes = [
        FlightType::SCHED_PAX          => 'J',
        FlightType::SCHED_CARGO        => 'F',
        FlightType::CHARTER_PAX_ONLY   => 'C',
        FlightType::ADDITIONAL_CARGO   => 'A',
        FlightType::VIP                => 'E',
        FlightType::ADDTL_PAX          => 'G',
        FlightType::CHARTER_CARGO_MAIL => 'H',
        FlightType::AMBULANCE          => 'I',
        FlightType::TRAINING           => 'K',
        FlightType::MAIL_SERVICE       => 'M',
        FlightType::CHARTER_SPECIAL    => 'O',
        FlightType::TECHNICAL_TEST     => 'T',
        FlightType::MILITARY           => 'M',
        FlightType::TECHNICAL_STOP     => 'X',
    ];
}
