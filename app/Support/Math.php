<?php

namespace App\Support;

/**
 * Helper math
 */
class Math
{
    /**
     * Determine from the base rate, if we want to return the overridden rate
     * or if the overridden rate is a percentage, then return that amount
     *
     * @param $base_rate
     * @param $override_rate
     *
     * @return float|null
     */
    public static function applyAmountOrPercent($base_rate, $override_rate = null): ?float
    {
        if (!$override_rate) {
            return $base_rate;
        }

        // Not a percentage override
        if (substr_count($override_rate, '%') === 0) {
            return $override_rate;
        }

        return static::getPercent($base_rate, $override_rate);
    }

    /**
     * Add/subtract a percentage to a number
     *
     * @param $number
     * @param $percent
     *
     * @return float
     */
    public static function getPercent($number, $percent): float
    {
        if (!is_numeric($number)) {
            $number = (float) $number;
        }

        if (!is_numeric($percent)) {
            $percent = (float) $percent;
        }

        return $number * ($percent / 100);
    }
}
