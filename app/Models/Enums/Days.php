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
        Days::MONDAY        => 'Monday',
        Days::TUESDAY       => 'Tuesday',
        Days::WEDNESDAY     => 'Wednesday',
        Days::THURSDAY      => 'Thursday',
        Days::FRIDAY        => 'Friday',
        Days::SATURDAY      => 'Saturday',
        Days::SUNDAY        => 'Sunday',
    ];
}
