<?php

namespace App\Support;

/**
 * ICAO Helper Tools
 */
class ICAO
{
    /**
     * Create a random hex code. Eventually this may follow the format in:
     * ICAO Aeronautical Telecommunications, Annex 10, Vol. III, chapter 9
     *
     * @param null $country
     *
     * @return string
     */
    public static function createHexCode($country = null)
    {
        $bytes = random_bytes(4);
        return bin2hex($bytes);
    }
}
