<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class UserState
 * @package App\Models\Enums
 */
class UserState extends Enum
{
    public const PENDING   = 0;
    public const ACTIVE    = 1;
    public const REJECTED  = 2;
    public const ON_LEAVE  = 3;
    public const SUSPENDED = 4;

    protected static $labels = [
        UserState::PENDING   => 'user.state.pending',
        UserState::ACTIVE    => 'user.state.active',
        UserState::REJECTED  => 'user.state.rejected',
        UserState::ON_LEAVE  => 'user.state.on_leave',
        UserState::SUSPENDED => 'user.state.suspended',
    ];
}
