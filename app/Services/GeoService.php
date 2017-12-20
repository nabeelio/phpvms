<?php

namespace App\Services;

//use \League\Geotools\Geotools;
use \GeoJson\Geometry\LineString;
use \GeoJson\Feature\Feature;
use \GeoJson\Feature\FeatureCollection;

use \League\Geotools\Geotools;
use \League\Geotools\Coordinate\Coordinate;

class GeoService extends BaseService
{
    /**
     * Return all of the coordinates, start to finish
     * Returned in the GeoJSON format
     * https://tools.ietf.org/html/rfc7946
     *
     * TODO: Save this data:
     * Once a PIREP is accepted, save this returned structure as a
     * JSON-encoded string into the raw_data field of the PIREP row
     *
     * @param \App\Models\Pirep|\App\Models\Flight $model
     * @return array
     */
    public function getRouteCoordsGeoJSON($model): array
    {
        # NOTE!! GeoJSON takes coords in [lon, lat] format!!
        $line = new LineString([
            [$model->dpt_airport->lon, $model->dpt_airport->lat],
            [$model->arr_airport->lon, $model->arr_airport->lat],
        ]);

        // TODO: Add markers for the start/end airports
        // TODO: Read from the ACARS data table

        $feature = new Feature($line, [], 1);
        $features = new FeatureCollection([$feature]);

        return [
            'features' => $features,
            //'center' => $center,
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
