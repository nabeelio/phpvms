<?php

namespace App\Models\Enums;


class PirepState extends EnumBase {

    const REJECTED  = -1;
    const PENDING   = 0;
    const ACCEPTED  = 1;

    protected static $labels = [
        PirepState::REJECTED    => 'system.pireps.state.rejected',
        PirepState::PENDING     => 'system.pireps.state.pending',
        PirepState::ACCEPTED    => 'system.pireps.state.accepted',
    ];
}
