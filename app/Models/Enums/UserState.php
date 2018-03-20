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
        UserState::PENDING   => 'Pending',
        UserState::ACTIVE    => 'Active',
        UserState::REJECTED  => 'Rejected',
        UserState::ON_LEAVE  => 'On Leave',
        UserState::SUSPENDED => 'Suspended',
    ];
}
