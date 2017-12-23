<?php

namespace App\Services;

use Log;
//use \League\Geotools\Geotools;
use App\Models\Flight;
use App\Repositories\NavdataRepository;
use \GeoJson\Geometry\LineString;
use \GeoJson\Feature\Feature;
use \GeoJson\Feature\FeatureCollection;

use League\Flysystem\Exception;
use \League\Geotools\Geotools;
use \League\Geotools\Coordinate\Coordinate;

/**
 * Return all of the coordinates, start to finish
 * Returned in the GeoJSON format
 * https://tools.ietf.org/html/rfc7946
 *
 * TODO: Save this data:
 * Once a PIREP is accepted, save this returned structure as a
 * JSON-encoded string into the raw_data field of the PIREP row
 *
 */
class GeoService extends BaseService
{
    private $navRepo;

    public function __construct(NavdataRepository $navRepo)
    {
        $this->navRepo = $navRepo;
    }


    public function getClosestCoords($coordStart, $all_coords, $measure='flat')
    {
        $distance = [];
        $geotools = new Geotools();
        $start = new Coordinate($coordStart);

        foreach($all_coords as $coords) {
            $coord = new Coordinate($coords);
            $dist = $geotools->distance()->setFrom($start)->setTo($coord);

            if($measure === 'flat') {
                $distance[] = $dist->flat();
            } elseif ($measure === 'greatcircle') {
                $distance[] = $dist->greatCircle();
            }
        }

        $distance = collect($distance);
        $min = $distance->min();
        return $all_coords[ $distance->search($min, true) ];
    }

    /**
     * @param $dep_icao     string  ICAO to ignore
     * @param $arr_icao     string  ICAO to ignore
     * @param $start_coords array   [x, y]
     * @param $route        string  Textual route
     * @return array
     */
    public function getCoordsFromRoute($dep_icao, $arr_icao, $start_coords, $route)
    {
        $coords = [];
        $split_route = explode(' ', $route);

        $skip = [
            $dep_icao,
            $arr_icao,
            'SID',
            'STAR'
        ];

        foreach ($split_route as $route_point) {

            $route_point = trim($route_point);

            if (\in_array($route_point, $skip, true)) {
                continue;
            }

            try {
                Log::info('Looking for ' . $route_point);

                $points = $this->navRepo->findWhere(['id' => $route_point]);
                $size = \count($points);

                if($size === 0) {
                    continue;
                } else if($size === 1) {

                    $point = $points[0];

                    Log::info('name: ' . $point->id . ' - ' . $point->lat . 'x' . $point->lon);

                    $coords[] = [
                        $point->lat,
                        $point->lon,
                    ];

                    continue;
                }

                # Find the point with the shortest distance
                Log::info('found ' . $size . ' for '. $route_point);

                $potential_coords = [];
                foreach($points as $point) {
                    #Log::debug('name: ' . $point->id . ' - '.$point->lat .'x'.$point->lon);
                    $potential_coords[] = [$point->lat, $point->lon];
                }

                # Get the start point and then reverse the lat/lon reference
                # If the first point happens to have multiple possibilities, use
                # the starting point that was passed in
                if(\count($coords) > 0) {
                    $start_point = $coords[\count($coords) - 1];
                    $start_point = [$start_point[0], $start_point[1]];
                } else {
                    $start_point = $start_coords;
                }

                $coords[] = $this->getClosestCoords($start_point, $potential_coords);

            } catch (\Exception $e) {
                Log::error($e);
                continue;
            }
        }

        return $coords;
    }

    /**
     * Return a FeatureCollection GeoJSON object
     * @param Flight $flight
     * @return array
     */
    public function flightGeoJson(Flight $flight): array
    {
        $coords = [];
        $coords[] = [$flight->dpt_airport->lon, $flight->dpt_airport->lat];

        // TODO: Add markers for the start/end airports
        // TODO: Read from the ACARS data table
        if($flight->route) {
            $route_coords =$this->getCoordsFromRoute(
                $flight->dpt_airport->icao,
                $flight->arr_airport->icao,
                [$flight->dpt_airport->lat, $flight->dpt_airport->lon],
                $flight->route);

            // lat, lon needs to be reversed for GeoJSON
            foreach($route_coords as $rc) {
                $coords[] = [$rc[1], $rc[0]];
            }
        }

        $coords[] = [$flight->arr_airport->lon, $flight->arr_airport->lat];

        $line = new LineString($coords);

        $features = new FeatureCollection([
            new Feature($line, [], 1)
        ]);

        return [
            'features' => $features,
        ];
    }

    /**
     * Return a GeoJSON FeatureCollection for a PIREP
     * @param Pirep $pirep
     * @return array
     */
    public function pirepGeoJson($pirep)
    {
        $coords = [];
        $coords[] = [$pirep->dpt_airport->lon, $pirep->dpt_airport->lat];

        // TODO: Add markers for the start/end airports

        // TODO: Check if there's data in the ACARS table
        if (!empty($pirep->route)) {
            $route_coords = $this->getCoordsFromRoute(
                $pirep->dpt_airport->icao,
                $pirep->arr_airport->icao,
                [$pirep->dpt_airport->lat, $pirep->dpt_airport->lon],
                $pirep->route);

            // lat, lon needs to be reversed for GeoJSON
            foreach ($route_coords as $rc) {
                $coords[] = [$rc[1], $rc[0]];
            }
        }

        $coords[] = [$pirep->arr_airport->lon, $pirep->arr_airport->lat];

        $line = new LineString($coords);

        $features = new FeatureCollection([
            new Feature($line, [], 1)
        ]);

        return [
            'features' => $features,
        ];
    }

    /**
     * Determine the center point between two sets of coordinates
     * @param $latA
     * @param $lonA
     * @param $latB
     * @param $lonB
     * @return array
     * @throws \League\Geotools\Exception\InvalidArgumentException
     */
    public function getCenter($latA, $lonA, $latB, $lonB)
    {
        $geotools = new Geotools();
        $coordA = new Coordinate([$latA, $lonA]);
        $coordB = new Coordinate([$latB, $lonB]);

        $vertex = $geotools->vertex()->setFrom($coordA)->setTo($coordB);
        $middlePoint = $vertex->middle();

        $center = [
            $middlePoint->getLatitude(),
            $middlePoint->getLongitude()
        ];

        return $center;
    }
}
