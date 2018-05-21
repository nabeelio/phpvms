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
        FlightType::SCHED_PAX          => 'flights.type.pass_scheduled',
        FlightType::SCHED_CARGO        => 'flights.type.cargo_scheduled',
        FlightType::CHARTER_PAX_ONLY   => 'flights.type.charter_pass_only',
        FlightType::ADDITIONAL_CARGO   => 'flights.type.addtl_cargo_mail',
        FlightType::VIP                => 'flights.type.special_vip',
        FlightType::ADDTL_PAX          => 'flights.type.pass_addtl',
        FlightType::CHARTER_CARGO_MAIL => 'flights.type.charter_cargo',
        FlightType::AMBULANCE          => 'flights.type.ambulance',
        FlightType::TRAINING           => 'flights.type.training_flight',
        FlightType::MAIL_SERVICE       => 'flights.type.mail_service',
        FlightType::CHARTER_SPECIAL    => 'flights.type.charter_special',
        FlightType::POSITIONING        => 'flights.type.positioning',
        FlightType::TECHNICAL_TEST     => 'flights.type.technical_test',
        FlightType::MILITARY           => 'flights.type.military',
        FlightType::TECHNICAL_STOP     => 'flights.type.technical_stop',
    ];
}
