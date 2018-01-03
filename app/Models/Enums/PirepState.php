<?php

namespace App\Models\Enums;


class PirepState extends EnumBase {

    const REJECTED      = -1;
    const IN_PROGRESS   = 0;
    const PENDING       = 1;
    const ACCEPTED      = 2;
    const CANCELLED     = 3;

    protected static $labels = [
        PirepState::REJECTED        => 'system.pireps.state.rejected',
        PirepState::IN_PROGRESS     => 'system.pireps.state.in_progress',
        PirepState::PENDING         => 'system.pireps.state.pending',
        PirepState::ACCEPTED        => 'system.pireps.state.accepted',
        PirepState::CANCELLED       => 'system.pireps.state.cancelled',
    ];
}
