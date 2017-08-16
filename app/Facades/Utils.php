<?php

namespace App\Facades;

use \Illuminate\Support\Facades\Facade;

class Utils extends Facade
{
    public static function secondsToTime($seconds, $incl_sec=false) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        $format = '%hh %im';
        if($incl_sec) {
            $format .= ' %ss';
        }

        return $dtF->diff($dtT)->format($format);
    }

    /**
     * Bitwise operator for setting days of week to integer field
     * @param int $datefield initial datefield
     * @param array $day_enums Array of values from config("enum.days")
     * @return int
     */
    public static function setDays(int $datefield, array $day_enums) {
        foreach($day_enums as $day) {
            $datefield |= $day;
        }

        return $datefield;
    }

    /**
     * Bit check if a day exists within a integer bitfield
     * @param int $datefield datefield from database
     * @param int $day_enum Value from config("enum.days")
     * @return bool
     */
    public static function hasDay(int $datefield, int $day_enum) {
        return ($datefield & $day_enum) === $datefield;
    }
}
