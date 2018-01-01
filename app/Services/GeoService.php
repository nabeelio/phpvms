<?php

namespace App\Services;

use Log;
use App\Models\Enums\AcarsType;
use App\Repositories\AcarsRepository;

use \GeoJson\Geometry\Point;
use \GeoJson\Geometry\LineString;
use \GeoJson\Feature\Feature;
use \GeoJson\Feature\FeatureCollection;

use \League\Geotools\Geotools;
use \League\Geotools\Coordinate\Coordinate;

use App\Models\GeoJson;
use App\Models\Flight;
use App\Models\Pirep;
use App\Repositories\NavdataRepository;

/**
 * Return different points/features in GeoJSON format
 * https://tools.ietf.org/html/rfc7946
 *
 * Once a PIREP is accepted, save this returned structure as a
 * JSON-encoded string into the raw_data field of the PIREP row
 *
 */
class GeoService extends BaseService
{
    private $acarsRepo, $navRepo;

    public function __construct(
        AcarsRepository $acarsRepo,
        NavdataRepository $navRepo
    ) {
        $this->acarsRepo = $acarsRepo;
        $this->navRepo = $navRepo;
    }

    /**
     * Determine the closest set of coordinates from the starting position
     * @param array $coordStart
     * @param array $all_coords
     * @return mixed
     * @throws \League\Geotools\Exception\InvalidArgumentException
     */
    public function getClosestCoords($coordStart, $all_coords)
    {
        $distance = [];
        $geotools = new Geotools();
        $start = new Coordinate($coordStart);

        foreach($all_coords as $coords) {
            $coord = new Coordinate($coords);
            $dist = $geotools->distance()->setFrom($start)->setTo($coord);
            $distance[] = $dist->greatCircle();
        }

        $distance = collect($distance);
        $min = $distance->min();
        return $all_coords[ $distance->search($min, true) ];
    }

    /**
     * @param $dep_icao     string  ICAO to ignore
     * @param $arr_icao     string  ICAO to ignore
     * @param $start_coords array   Starting point, [x, y]
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
                Log::debug('Looking for ' . $route_point);

                $points = $this->navRepo->findWhere(['id' => $route_point]);
                $size = \count($points);

                if($size === 0) {
                    continue;
                } else if($size === 1) {
                    $point = $points[0];
                    Log::info('name: ' . $point->id . ' - ' . $point->lat . 'x' . $point->lon);
                    $coords[] = $point;
                    continue;
                }

                # Find the point with the shortest distance
                Log::info('found ' . $size . ' for '. $route_point);

                # Get the start point and then reverse the lat/lon reference
                # If the first point happens to have multiple possibilities, use
                # the starting point that was passed in
                if (\count($coords) > 0) {
                    $start_point = $coords[\count($coords) - 1];
                    $start_point = [$start_point->lat, $start_point->lon];
                } else {
                    $start_point = $start_coords;
                }

                # Put all of the lat/lon sets into an array to pick of what's clsest
                # to the starting point
                $potential_coords = [];
                foreach($points as $point) {
                    $potential_coords[] = [$point->lat, $point->lon];
                }

                # returns an array with the closest lat/lon to start point
                $closest_coords = $this->getClosestCoords($start_point, $potential_coords);
                foreach($points as $point) {
                    if($point->lat === $closest_coords[0] && $point->lon === $closest_coords[1]) {
                        break;
                    }
                }

                $coords[] = $point;

            } catch (\Exception $e) {
                Log::error($e);
                continue;
            }
        }

        return $coords;
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

    /**
     * Read an array/relationship of ACARS model points
     * @param Pirep $pirep
     * @return array
     */
    public function getFeatureFromAcars(Pirep $pirep)
    {
        $route_line = [];
        $route_points = [];

        /**
         * @var $point \App\Models\Acars
         */
        $counter = 1;
        foreach ($pirep->acars as $point) {
            $route_line[] = [$point->lon, $point->lat];
            $route_points[] = new Feature(
                new Point([$point->lon, $point->lat]), [
                    'pirep_id' => $pirep->id,
                    'name' => $point->altitude,
                    'popup' => $counter . '<br />GS: ' . $point->gs . '<br />Alt: ' . $point->altitude,
                ]);

            ++$counter;
        }

        if(\count($route_line) >= 2) {
            $route_line = new Feature(new LineString($route_line));
            $route_line = new FeatureCollection([$route_line]);
        } else {
            $route_line = new FeatureCollection([]);
        }

        return [
            'line' => $route_line,
            'points' => new FeatureCollection($route_points)
        ];
    }

    /**
     * Return a single feature point for the
     */
    public function getFeatureForLiveFlights($pireps)
    {
        $flight_points = [];

        /**
         * @var Pirep $pirep
         */
        foreach($pireps as $pirep) {

            /**
             * @var $point \App\Models\Acars
             */
            $point = $pirep->position;
            if(!$point) {
                continue;
            }

            $flight_points[] = new Feature(
                new Point([$point->lon, $point->lat]), [
                    'pirep_id'  => $pirep->id,
                    'gs'        => $point->gs,
                    'alt'       => $point->altitude,
                    'heading'   => $point->heading ?: 0,
                    'popup'     => $pirep->ident . '<br />GS: ' . $point->gs . '<br />Alt: ' . $point->altitude,
                ]);
        }

        return new FeatureCollection($flight_points);
    }

    /**
     * Return a FeatureCollection GeoJSON object
     * @param Flight $flight
     * @return array
     */
    public function flightGeoJson(Flight $flight): array
    {
        $route = new GeoJson();

        ## Departure Airport
        $route->addPoint($flight->dpt_airport->lat, $flight->dpt_airport->lon, [
            'name' => $flight->dpt_airport->icao,
            'popup' => $flight->dpt_airport->full_name,
            'icon' => 'airport',
        ]);

        if($flight->route) {
            $all_route_points = $this->getCoordsFromRoute(
                $flight->dpt_airport->icao,
                $flight->arr_airport->icao,
                [$flight->dpt_airport->lat, $flight->dpt_airport->lon],
                $flight->route);

            // lat, lon needs to be reversed for GeoJSON
            foreach($all_route_points as $point) {
                $route->addPoint($point->lat, $point->lon, [
                    'name'  => $point->name,
                    'popup' => $point->name . ' (' . $point->name . ')',
                    'icon'  => ''
                ]);
            }
        }

        $route->addPoint($flight->arr_airport->lat, $flight->arr_airport->lon, [
            'name'  => $flight->arr_airport->icao,
            'popup' => $flight->arr_airport->full_name,
            'icon'  => 'airport',
        ]);

        return [
            'route_points'        => $route->getPoints(),
            'planned_route_line'  => $route->getLine(),
        ];
    }

    /**
     * Return a GeoJSON FeatureCollection for a PIREP
     * @param Pirep $pirep
     * @return array
     */
    public function pirepGeoJson(Pirep $pirep)
    {
        $planned = new GeoJson();
        $actual = new GeoJson();

        /**
         * PLANNED ROUTE
         */
        $planned->addPoint($pirep->dpt_airport->lat, $pirep->dpt_airport->lon, [
            'name' => $pirep->dpt_airport->icao,
            'popup' => $pirep->dpt_airport->full_name,
        ]);

        $planned_route = $this->acarsRepo->forPirep($pirep->id, AcarsType::ROUTE);
        foreach($planned_route as $point) {
            $planned->addPoint($point->lat, $point->lon, [
                'name' => $point->name,
                'popup' => $point->name . ' (' . $point->name . ')',
            ]);
        }

        $planned->addPoint($pirep->arr_airport->lat, $pirep->arr_airport->lon, [
            'name' => $pirep->arr_airport->icao,
            'popup' => $pirep->arr_airport->full_name,
            'icon' => 'airport',
        ]);

        /**
         * ACTUAL ROUTE
         */
        $actual_route = $this->acarsRepo->forPirep($pirep->id, AcarsType::FLIGHT_PATH);
        foreach ($actual_route as $point) {
            $actual->addPoint($point->lat, $point->lon, [
                'pirep_id' => $pirep->id,
                'name' => $point->altitude,
                'popup' => 'GS: ' . $point->gs . '<br />Alt: ' . $point->altitude,
            ]);
        }

        return [
            'planned_rte_points'  => $planned->getPoints(),
            'planned_rte_line'    => $planned->getLine(),

            'actual_route_points' => $actual->getPoints(),
            'actual_route_line'   => $actual->getLine(),
        ];
    }
}
