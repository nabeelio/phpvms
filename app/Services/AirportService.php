<?php

namespace App\Services;

use App\Contracts\AirportLookup as AirportLookupProvider;
use App\Contracts\Metar as MetarProvider;
use App\Contracts\Service;
use App\Repositories\AirportRepository;
use App\Support\Metar;
use App\Support\Units\Distance;
use Illuminate\Support\Facades\Cache;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Geotools;
use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;
use VaCentral\Airport;

/**
 * Class AnalyticsService
 */
class AirportService extends Service
{
    private $airportRepo;
    private $lookupProvider;
    private $metarProvider;

    public function __construct(
        AirportLookupProvider $lookupProvider,
        AirportRepository $airportRepo,
        MetarProvider $metarProvider

    ) {
        $this->airportRepo = $airportRepo;
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

    /**
     * Calculate the distance from one airport to another
     *
     * @param string $fromIcao
     * @param string $toIcao
     *
     * @return Distance
     */
    public function calculateDistance($fromIcao, $toIcao)
    {
        $from = $this->airportRepo->find($fromIcao, ['lat', 'lon']);
        $to = $this->airportRepo->find($toIcao, ['lat', 'lon']);

        // Calculate the distance
        $geotools = new Geotools();
        $start = new Coordinate([$from->lat, $from->lon]);
        $end = new Coordinate([$to->lat, $to->lon]);
        $dist = $geotools->distance()->setFrom($start)->setTo($end);

        // Convert into a Distance object
        try {
            $distance = new Distance($dist->in('mi')->greatCircle(), 'mi');
            return $distance;
        } catch (NonNumericValue $e) {
            return;
        } catch (NonStringUnitName $e) {
            return;
        }
    }
}
