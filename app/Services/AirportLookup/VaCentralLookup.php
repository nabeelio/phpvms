<?php

namespace App\Services\AirportLookup;

use App\Contracts\AirportLookup;
use Illuminate\Support\Facades\Log;
use VaCentral\Contracts\IVaCentral;
use VaCentral\Exceptions\HttpException;

class VaCentralLookup extends AirportLookup
{
    private IVaCentral $client;

    public function __construct(IVaCentral $client)
    {
        $this->client = $client;
    }

    /**
     * Lookup the information for an airport
     *
     * @param string $icao
     *
     * @return mixed
     */
    public function getAirport($icao)
    {
        try {
            $airport = $this->client->getAirport($icao);
            $airport->location = $airport->city;
            $airport->timezone = $airport->tz;

            return $airport;
        } catch (HttpException $e) {
            Log::error($e);
            return [];
        }
    }
}
