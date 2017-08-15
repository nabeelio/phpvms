<?php

namespace App\Models\Transformers;

use App\Models\Airport;
use League\Fractal\TransformerAbstract;

class AirportTransform extends TransformerAbstract
{
    public function transform(Airport $ap)
    {
        return [
            'id'              => $ap->id,
            'icao'            => $ap->icao,
            'name'            => $ap->name,
            'location'        => $ap->location,
            'country'         => $ap->country,
            'fuel_100ll_cost' => $ap->fuel_100ll_cost,
            'fuel_jeta_cost'  => $ap->fuel_jeta_cost,
            'fuel_mogas_cost' => $ap->fuel_mogas_cost,
            'lat'             => $ap->lat,
            'lon'             => $ap->lon,
        ];
    }
}
