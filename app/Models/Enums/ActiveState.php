<?php

namespace App\Models\Enums;

/**
 * Class ActiveState
 * @package App\Models\Enums
 */
class ActiveState extends EnumBase
{
    public const INACTIVE = 0;
    public const ACTIVE = 1;

    public static $labels = [
        ActiveState::INACTIVE  => 'Inactive',
        ActiveState::ACTIVE    => 'Active',
    ];
}
