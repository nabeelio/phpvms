<?php

namespace App\Models\Enums;

use Illuminate\Support\Facades\Facade;

class PirepState extends EnumBase {
    const REJECTED  = -1;
    const PENDING   = 0;
    const ACCEPTED  = 1;

    protected static $labels = [
        PirepState::REJECTED    => 'Rejected',
        PirepState::PENDING     => 'Pending',
        PirepState::ACCEPTED    => 'Accepted',
    ];
}
