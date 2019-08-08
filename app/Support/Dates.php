<?php

namespace App\Support;

use Carbon\Carbon;

class Dates
{
    /**
     * Get the list of months, given a start date
     *
     * @param Carbon $start_date
     *
     * @return array
     */
    public static function getMonthsList(Carbon $start_date): array
    {
        $months = [];
        $now = date('Y-m');
        $last_month = $start_date;

        do {
            $last_value = $last_month->format('Y-m');
            $months[$last_value] = $last_month->format('Y F');
            $last_month = $last_month->addMonth();
        } while ($last_value !== $now);

        return $months;
    }

    /**
     * Return the start/end dates for a given month/year
     *
     * @param $month YYYY-MM
     *
     * @return array
     */
    public static function getMonthBoundary($month): array
    {
        [$year, $month] = explode('-', $month);
        $days = static::getDaysInMonth($month, $year);

        return [
            "$year-$month-01",
            "$year-$month-$days",
        ];
    }

    /**
     * Get the number of days in a month
     * https://www.php.net/manual/en/function.cal-days-in-month.php#38666
     *
     * @param int $month
     * @param int $year
     *
     * @return int
     */
    public static function getDaysInMonth($month, $year): int
    {
        $month = (int) $month;
        $year = (int) $year;
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }
}
