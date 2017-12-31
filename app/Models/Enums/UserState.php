<?php
/**
 * Hold the user states
 */

namespace App\Models\Enums;

class UserState extends EnumBase
{
    const PENDING   = 0;
    const ACTIVE    = 1;
    const REJECTED  = 2;
    const ON_LEAVE  = 3;
    const SUSPENDED = 4;

    protected static $labels = [
        UserState::PENDING     => 'Pending',
        UserState::ACTIVE      => 'Active',
        UserState::REJECTED    => 'Rejected',
        UserState::ON_LEAVE    => 'On Leave',
        UserState::SUSPENDED   => 'Suspended',
    ];
}
