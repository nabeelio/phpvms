<?php

namespace App\Models\Enums;

use App\Contracts\Enum;

class FlightType extends Enum
{
    public const SCHED_PAX = 'J';
    public const SCHED_CARGO = 'F';
    public const CHARTER_PAX_ONLY = 'C';
    public const ADDITIONAL_CARGO = 'A';
    public const VIP = 'E';
    public const ADDTL_PAX = 'G';
    public const CHARTER_CARGO_MAIL = 'H';
    public const AMBULANCE = 'I';
    public const TRAINING = 'K';
    public const MAIL_SERVICE = 'M';
    public const CHARTER_SPECIAL = 'O';
    public const POSITIONING = 'P';
    public const TECHNICAL_TEST = 'T';
    public const MILITARY = 'W';
    public const TECHNICAL_STOP = 'X';

    protected static array $labels = [
        self::SCHED_PAX          => 'flights.type.pass_scheduled',
        self::SCHED_CARGO        => 'flights.type.cargo_scheduled',
        self::CHARTER_PAX_ONLY   => 'flights.type.charter_pass_only',
        self::ADDITIONAL_CARGO   => 'flights.type.addtl_cargo_mail',
        self::VIP                => 'flights.type.special_vip',
        self::ADDTL_PAX          => 'flights.type.pass_addtl',
        self::CHARTER_CARGO_MAIL => 'flights.type.charter_cargo',
        self::AMBULANCE          => 'flights.type.ambulance',
        self::TRAINING           => 'flights.type.training_flight',
        self::MAIL_SERVICE       => 'flights.type.mail_service',
        self::CHARTER_SPECIAL    => 'flights.type.charter_special',
        self::POSITIONING        => 'flights.type.positioning',
        self::TECHNICAL_TEST     => 'flights.type.technical_test',
        self::MILITARY           => 'flights.type.military',
        self::TECHNICAL_STOP     => 'flights.type.technical_stop',
    ];
}
