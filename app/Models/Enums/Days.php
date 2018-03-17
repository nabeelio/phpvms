<?php
/**
 *
 */

namespace App\Models\Enums;

/**
 * Class Days
 * @package App\Models\Enums
 */
class Days extends EnumBase {

    public const MONDAY    = 1 << 0;
    public const TUESDAY   = 1 << 1;
    public const WEDNESDAY = 1 << 2;
    public const THURSDAY  = 1 << 3;
    public const FRIDAY    = 1 << 4;
    public const SATURDAY  = 1 << 5;
    public const SUNDAY    = 1 << 6;

    protected static $labels = [
        Days::MONDAY        => 'system.days.mon',
        Days::TUESDAY       => 'system.days.tues',
        Days::WEDNESDAY     => 'system.days.wed',
        Days::THURSDAY      => 'system.days.thurs',
        Days::FRIDAY        => 'system.days.fri',
        Days::SATURDAY      => 'system.days.sat',
        Days::SUNDAY        => 'system.days.sun',
    ];
}
