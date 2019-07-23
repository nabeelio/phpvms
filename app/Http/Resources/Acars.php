<?php

namespace App\Http\Resources;

use App\Support\Units\Distance;
use App\Support\Units\Fuel;

class Acars extends Response
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName
     *
     * @return array
     */
    public function toArray($request)
    {
        $res = parent::toArray($request);

        // Set these to the response units
        if (!empty($res['distance'])) {
            $distance = new Distance($res['distance'], config('phpvms.internal_units.distance'));
            $res['distance'] = $distance->getResponseUnits();
        }

        if (!empty($res['fuel'])) {
            $fuel = new Fuel($res['fuel'], config('phpvms.internal_units.fuel'));
            $res['fuel'] = $fuel->getResponseUnits();
        }

        return $res;
    }
}
