<?php

namespace App\Services;

use App\Contracts\AirportLookup as AirportLookupProvider;
use App\Contracts\Metar as MetarProvider;
use App\Contracts\Service;
use App\Support\Metar;
use Illuminate\Support\Facades\Cache;
use VaCentral\Airport;

/**
 * Class AnalyticsService
 */
class AirportService extends Service
{
    private $lookupProvider;
    private $metarProvider;

    public function __construct(
        AirportLookupProvider $lookupProvider,
        MetarProvider $metarProvider

    ) {
        $this->lookupProvider = $lookupProvider;
        $this->metarProvider = $metarProvider;
    }

    /**
     * Return the METAR for a given airport
     *
     * @param $icao
     *
     * @return Metar|null
     */
    public function getMetar($icao)
    {
        $icao = trim($icao);
        if ($icao === '') {
            return;
        }

        $raw_metar = $this->metarProvider->get_metar($icao);
        if ($raw_metar && $raw_metar !== '') {
            return new Metar($raw_metar);
        }
    }

    /**
     * Lookup an airport's information from a remote provider. This handles caching
     * the data internally
     *
     * @param string $icao ICAO
     *
     * @return Airport|array
     */
    public function lookupAirport($icao)
    {
        $key = config('cache.keys.AIRPORT_VACENTRAL_LOOKUP.key').$icao;

        $airport = Cache::get($key);
        if ($airport) {
            return $airport;
        }

        $airport = $this->lookupProvider->getAirport($icao);
        if ($airport === null) {
            return [];
        }

        Cache::add(
            $key,
            $airport,
            config('cache.keys.AIRPORT_VACENTRAL_LOOKUP.time')
        );

        return $airport;
    }
}
