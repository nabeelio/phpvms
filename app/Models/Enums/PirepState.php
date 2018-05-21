<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class PirepState
 * @package App\Models\Enums
 */
class PirepState extends Enum
{
    public const REJECTED    = -1;
    public const IN_PROGRESS = 0;  // flight is ongoing
    public const PENDING     = 1;  // waiting admin approval
    public const ACCEPTED    = 2;
    public const CANCELLED   = 3;
    public const DELETED     = 4;
    public const DRAFT       = 5;

    protected static $labels = [
        PirepState::REJECTED    => 'pireps.state.rejected',
        PirepState::IN_PROGRESS => 'pireps.state.in_progress',
        PirepState::PENDING     => 'pireps.state.pending',
        PirepState::ACCEPTED    => 'pireps.state.accepted',
        PirepState::CANCELLED   => 'pireps.state.cancelled',
        PirepState::DELETED     => 'pireps.state.deleted',
        PirepState::DRAFT       => 'pireps.state.draft',
    ];
}
