<?php

namespace App\Services\AirportLookup;

use App\Contracts\AirportLookup;
use Illuminate\Support\Facades\Log;
use VaCentral\Airport;
use VaCentral\HttpException;

class VaCentralLookup extends AirportLookup
{
    /**
     * Lookup the information for an airport
     *
     * @param string $icao
     *
     * @return array
     */
    public function getAirport($icao)
    {
        try {
            return Airport::get($icao);
        } catch (HttpException $e) {
            Log::error($e);
            return;
        }
    }
}
