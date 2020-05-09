<?php

namespace App\Http\Resources;

use App\Contracts\Resource;
use App\Support\Units\Distance;
use App\Support\Units\Fuel;

/**
 * @mixin \App\Models\Acars
 */
class Acars extends Resource
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
        $distance = !empty($res['distance']) ? $res['distance'] : 0;
        $distance = new Distance($distance, config('phpvms.internal_units.distance'));
        $res['distance'] = $distance->getResponseUnits();

        $fuel = !empty($res['fuel']) ? $res['fuel'] : 0;
        $fuel = new Fuel($fuel, config('phpvms.internal_units.fuel'));
        $res['fuel'] = $fuel->getResponseUnits();

        $res['pirep'] = Pirep::make($this->whenLoaded('pirep'));

        return $res;
    }
}
