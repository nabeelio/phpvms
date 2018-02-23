<?php

namespace App\Support;

/**
 * Helper math
 * @package App\Support
 */
class Math
{

    /**
     * Add/subtract a percentage to a number
     * @param $number
     * @param $percent
     * @return float
     */
    public static function addPercent($number, $percent): float
    {
        if(!is_numeric($number)) {
            $number = (float) $number;
        }

        if(!is_numeric($percent)) {
            $percent = (float) $percent;
        }


        return $number + ($number * ($percent/100));
    }

}
