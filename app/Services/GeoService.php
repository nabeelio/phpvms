<?php

namespace App\Services;

use App\Models\Acars;
use Log;

use \GeoJson\Geometry\Point;
use \GeoJson\Geometry\LineString;
use \GeoJson\Feature\Feature;
use \GeoJson\Feature\FeatureCollection;

use \League\Geotools\Geotools;
use \League\Geotools\Coordinate\Coordinate;

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
    private $navRepo;

    public function __construct(NavdataRepository $navRepo)
    {
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
     * @return FeatureCollection
     */
    public function getFeatureFromAcars(Pirep $pirep)
    {
        $route_line = [];
        $route_points = [];

        $route_line[] = [$pirep->dpt_airport->lon, $pirep->dpt_airport->lat];

        /**
         * @var $point \App\Models\Acars
         */
        foreach ($pirep->acars as $point)
        {
            $route_line[] = [$point->lon, $point->lat];
            $route_points[] = new Feature(
                new Point([$point->lon, $point->lat]), [
                    'pirep_id'  => $pirep->id,
                    'name'      => $point->altitude,
                    'popup'     => 'GS: ' . $point->gs . '<br />Alt: ' . $point->altitude,
                ]);
        }

        # Arrival
        $route_line[] = [$pirep->arr_airport->lon, $pirep->arr_airport->lat];
        $route_line = new Feature(new LineString($route_line));

        return new FeatureCollection([$route_line, $route_points]);
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
            $flight_points[] = new Feature(
                new Point([$point->lon, $point->lat]), [
                    'pirep_id'  => $pirep->id,
                    'gs'        => $point->gs,
                    'alt'       => $point->altitude,
                    'heading'   => $point->heading ?: 0,
                    'popup'     => 'Flight: ' . $pirep->ident,
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
        $route_coords = [];
        $route_points = [];

        ## Departure Airport
        $route_coords[] = [$flight->dpt_airport->lon, $flight->dpt_airport->lat];

        $route_points[] = new Feature(
            new Point([$flight->dpt_airport->lon, $flight->dpt_airport->lat]), [
                'name'  => $flight->dpt_airport->icao,
                'popup' => $flight->dpt_airport->full_name,
                'icon'  => 'airport',
            ]
        );

        if($flight->route) {
            $all_route_points = $this->getCoordsFromRoute(
                $flight->dpt_airport->icao,
                $flight->arr_airport->icao,
                [$flight->dpt_airport->lat, $flight->dpt_airport->lon],
                $flight->route);

            // lat, lon needs to be reversed for GeoJSON
            foreach($all_route_points as $point) {
                $route_coords[] = [$point->lon, $point->lat];
                $route_points[] = new Feature(new Point([$point->lon, $point->lat]), [
                    'name'  => $point->name,
                    'popup' => $point->name . ' (' . $point->name . ')',
                    'icon'  => ''
                ]);
            }
        }

        ## Arrival Airport
        $route_coords[] = [$flight->arr_airport->lon, $flight->arr_airport->lat,];

        $route_points[] = new Feature(
            new Point([$flight->arr_airport->lon, $flight->arr_airport->lat]), [
                'name'  => $flight->arr_airport->icao,
                'popup' => $flight->arr_airport->full_name,
                'icon'  => 'airport',
            ]
        );

        $route_points = new FeatureCollection($route_points);
        $planned_route_line = new FeatureCollection([new Feature(new LineString($route_coords), [])]);

        return [
            'route_points'          => $route_points,
            'planned_route_line'    => $planned_route_line,
        ];
    }

    /**
     * Return a GeoJSON FeatureCollection for a PIREP
     * @param Pirep $pirep
     * @return array
     */
    public function pirepGeoJson(Pirep $pirep)
    {
        $planned_rte_points = [];
        $planned_rte_coords = [];

        $planned_rte_coords[] = [$pirep->dpt_airport->lon, $pirep->dpt_airport->lat];
        $feature = new Feature(
            new Point([$pirep->dpt_airport->lon, $pirep->dpt_airport->lat]), [
                'name' => $pirep->dpt_airport->icao,
                'popup' => $pirep->dpt_airport->full_name,
                'icon' => 'airport',
           ]);

        $planned_rte_points[] = $feature;

        if (!empty($pirep->route)) {
            $all_route_points = $this->getCoordsFromRoute(
                $pirep->dpt_airport->icao,
                $pirep->arr_airport->icao,
                [$pirep->dpt_airport->lat, $pirep->dpt_airport->lon],
                $pirep->route);

            // lat, lon needs to be reversed for GeoJSON
            foreach ($all_route_points as $point) {
                $planned_rte_coords[] = [$point->lon, $point->lat];
                $planned_rte_points[] = new Feature(new Point([$point->lon, $point->lat]), [
                    'name'  => $point->name,
                    'popup' => $point->name . ' (' . $point->name . ')',
                    'icon'  => ''
                ]);
            }
        }

        $planned_rte_coords[] = [$pirep->arr_airport->lon, $pirep->arr_airport->lat];

        $planned_rte_points[] = new Feature(
            new Point([$pirep->arr_airport->lon, $pirep->arr_airport->lat]), [
                'name'  => $pirep->arr_airport->icao,
                'popup' => $pirep->arr_airport->full_name,
                'icon'  => 'airport',
            ]
        );

        $planned_rte_points = new FeatureCollection($planned_rte_points);

        $planned_route = new FeatureCollection([
            new Feature(new LineString($planned_rte_coords), [])
        ]);

        $actual_route = $this->getFeatureFromAcars($pirep);

        return [
            'planned_rte_points'  => $planned_rte_points,
            'planned_rte_line'    => $planned_route,
            'actual_route_line'   => $actual_route['line'],
            'actual_route_points' => $actual_route['points'],
        ];
    }
}
