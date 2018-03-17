<?php
/**
 * Hold the user states
 */

namespace App\Models\Enums;

class UserState extends EnumBase
{
    public const PENDING   = 0;
    public const ACTIVE    = 1;
    public const REJECTED  = 2;
    public const ON_LEAVE  = 3;
    public const SUSPENDED = 4;

    protected static $labels = [
        UserState::PENDING     => 'Pending',
        UserState::ACTIVE      => 'Active',
        UserState::REJECTED    => 'Rejected',
        UserState::ON_LEAVE    => 'On Leave',
        UserState::SUSPENDED   => 'Suspended',
    ];
}
