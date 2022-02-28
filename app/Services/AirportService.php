<?php

namespace App\Services;

use App\Contracts\AirportLookup;
use App\Contracts\Metar as MetarProvider;
use App\Contracts\Service;
use App\Exceptions\AirportNotFound;
use App\Models\Airport;
use App\Repositories\AirportRepository;
use App\Support\Metar;
use App\Support\Units\Distance;
use Illuminate\Support\Facades\Cache;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Geotools;
use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;

class AirportService extends Service
{
    private AirportRepository $airportRepo;
    private AirportLookup $lookupProvider;
    private MetarProvider $metarProvider;

    public function __construct(
        AirportLookup $lookupProvider,
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
    public function getMetar($icao): ?Metar
    {
        $icao = trim($icao);
        if ($icao === '') {
            return null;
        }

        $raw_metar = $this->metarProvider->metar($icao);
        if ($raw_metar && $raw_metar !== '') {
            return new Metar($raw_metar);
        }

        return null;
    }

    /**
     * Return the METAR for a given airport
     *
     * @param $icao
     *
     * @return Metar|null
     */
    public function getTaf($icao): ?Metar
    {
        $icao = trim($icao);
        if ($icao === '') {
            return null;
        }

        $raw_taf = $this->metarProvider->taf($icao);
        if ($raw_taf && $raw_taf !== '') {
            return new Metar($raw_taf, true);
        }

        return null;
    }

    /**
     * Lookup an airport's information from a remote provider. This handles caching
     * the data internally
     *
     * @param string $icao ICAO
     *
     * @return mixed
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

        $airport = (array) $airport;

        Cache::add(
            $key,
            $airport,
            config('cache.keys.AIRPORT_VACENTRAL_LOOKUP.time')
        );

        return $airport;
    }

    /**
     * Lookup an airport and save it if it hasn't been found
     *
     * @param string $icao
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function lookupAirportIfNotFound($icao)
    {
        $icao = strtoupper($icao);
        $airport = $this->airportRepo->findWithoutFail($icao);
        if ($airport !== null) {
            return $airport;
        }

        // Don't lookup the airport, so just add in something generic
        if (!setting('general.auto_airport_lookup')) {
            $airport = new Airport([
                'id'   => $icao,
                'icao' => $icao,
                'name' => $icao,
                'lat'  => 0,
                'lon'  => 0,
            ]);

            $airport->save();

            return $airport;
        }

        $lookup = $this->lookupAirport($icao);
        if (empty($lookup)) {
            return;
        }

        $airport = new Airport($lookup);
        $airport->save();

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

        if (!$from) {
            throw new AirportNotFound($fromIcao);
        }

        if (!$to) {
            throw new AirportNotFound($toIcao);
        }

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
