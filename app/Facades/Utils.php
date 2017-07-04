<?php

namespace App\Facades;

use \Illuminate\Support\Facades\Facade;

class Utils extends Facade
{
    public static function secondsToTime($seconds) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%hh %im %ss');
    }
}
