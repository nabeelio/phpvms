<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class Days
 * Start on Monday - ISO8601
 * @package App\Models\Enums
 */
class Days extends Enum
{
    public const MONDAY    = 1 << 0;
    public const TUESDAY   = 1 << 1;
    public const WEDNESDAY = 1 << 2;
    public const THURSDAY  = 1 << 3;
    public const FRIDAY    = 1 << 4;
    public const SATURDAY  = 1 << 5;
    public const SUNDAY    = 1 << 6;

    public static $labels = [
        Days::MONDAY    => 'system.days.mon',
        Days::TUESDAY   => 'system.days.tues',
        Days::WEDNESDAY => 'system.days.wed',
        Days::THURSDAY  => 'system.days.thurs',
        Days::FRIDAY    => 'system.days.fri',
        Days::SATURDAY  => 'system.days.sat',
        Days::SUNDAY    => 'system.days.sun',
    ];

    public static $codes = [
        'M'  => Days::MONDAY,
        'T'  => Days::TUESDAY,
        'W'  => Days::WEDNESDAY,
        'Th' => Days::THURSDAY,
        'F'  => Days::FRIDAY,
        'S'  => Days::SATURDAY,
        'Su' => Days::SUNDAY,
    ];

    /**
     * Map the ISO8601 numeric today to day
     */
    public static $isoDayMap = [
        1 => Days::MONDAY,
        2 => Days::TUESDAY,
        3 => Days::WEDNESDAY,
        4 => Days::THURSDAY,
        5 => Days::FRIDAY,
        6 => Days::SATURDAY,
        7 => Days::SUNDAY,
    ];

    /**
     * Create the masked value for the days of week
     * @param array $days
     * @return int|mixed
     */
    public static function getDaysMask(array $days)
    {
        $mask = 0;
        foreach($days as $day) {
            $mask |= $day;
        }

        return $mask;
    }

    /**
     * See if the given mask has a day
     * @param $mask
     * @param $day
     * @return bool
     */
    public static function in($mask, $day): bool
    {
        return ($mask & $day) === $day;
    }

    /**
     * Does the mask contain today?
     * @param $val
     * @return bool
     */
    public static function isToday($val): bool
    {
        return static::in($val, static::$isoDayMap[(int) date('N')]);
    }
}
