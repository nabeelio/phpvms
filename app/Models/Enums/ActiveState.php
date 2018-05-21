<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class ActiveState
 * @package App\Models\Enums
 */
class ActiveState extends Enum
{
    public const INACTIVE = 0;
    public const ACTIVE   = 1;

    public static $labels = [
        ActiveState::ACTIVE   => 'common.active',
        ActiveState::INACTIVE => 'common.inactive',
    ];
}
