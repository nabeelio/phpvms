<?php

namespace App\Models\Enums;

class FuelType extends EnumBase {

    const LOW_LEAD  = 0;
    const JET_A     = 1;
    const MOGAS     = 2;

    protected static $labels = [
        FuelType::LOW_LEAD  => '100LL',
        FuelType::JET_A     => 'JET A',
        FuelType::MOGAS     => 'MOGAS',
    ];
}
