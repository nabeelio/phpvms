<?php

namespace App\Services;

use App\Contracts\Metar as MetarProvider;
use App\Contracts\Service;
use App\Support\Metar;

/**
 * Class AnalyticsService
 */
class AirportService extends Service
{
    private $metarProvider;

    public function __construct(
        MetarProvider $metarProvider
    ) {
        $this->metarProvider = $metarProvider;
    }

    /**
     * Return the METAR for a given airport
     *
     * @param $icao
     *
     * @return Metar|null
     */
    public function getMetar($icao): Metar
    {
        $metar = null;
        $wind = null;
        $raw_metar = $this->metarProvider->get_metar($icao);

        if ($raw_metar && $raw_metar !== '') {
            return new Metar($raw_metar);
        }

        return null;
    }
}
