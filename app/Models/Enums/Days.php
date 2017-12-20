<?php
/**
 *
 */

namespace App\Models\Enums;


class Days extends EnumBase {

    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 4;
    const THURSDAY  = 8;
    const FRIDAY    = 16;
    const SATURDAY  = 32;
    const SUNDAY    = 64;

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
