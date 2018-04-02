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
        UserState::PENDING   => 'system.users.state.pending',
        UserState::ACTIVE    => 'system.users.state.active',
        UserState::REJECTED  => 'system.users.state.rejected',
        UserState::ON_LEAVE  => 'system.users.state.on_leave',
        UserState::SUSPENDED => 'system.users.state.suspended',
    ];
}
