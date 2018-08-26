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
    public static function getMonthsList(Carbon $start_date)
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
    public static function getMonthBoundary($month)
    {
        [$year, $month] = explode('-', $month);
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        return [
            "$year-$month-01",
            "$year-$month-$days",
        ];
    }
}
