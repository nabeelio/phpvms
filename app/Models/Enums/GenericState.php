<?php

namespace App\Models\Enums;

/**
 * Class GenericState
 * @package App\Models\Enums
 */
class GenericState extends EnumBase
{
    const INACTIVE = 0;
    const ACTIVE = 1;

    public static $labels = [
        GenericState::INACTIVE  => 'Inactive',
        GenericState::ACTIVE    => 'Active',
    ];
}
