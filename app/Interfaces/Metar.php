<?php

namespace App\Interfaces;

abstract class Metar
{
    /**
     * Implement the METAR- Return the string
     * @param $icao
     * @return mixed
     */
    abstract public function get($icao);
}
