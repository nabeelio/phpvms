<?php
/**
 * Created by IntelliJ IDEA.
 * User: nabeelshahzad
 * Date: 12/22/17
 * Time: 12:14 PM
 */

namespace App\Models\Enums;


class PilotState extends EnumBase
{
    const PENDING   = 1;
    const ACTIVE    = 2;
    const ON_LEAVE  = 3;
    const SUSPENDED = 4;

    protected static $labels = [
        PilotState::PENDING     => 'Pending',
        PilotState::ACTIVE      => 'Active',
        PilotState::ON_LEAVE    => 'On Leave',
        PilotState::SUSPENDED   => 'Suspended',
    ];
}
