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
}
