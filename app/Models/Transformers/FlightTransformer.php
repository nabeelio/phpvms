<?php

namespace App\Models\Transformers;

use League\Fractal\TransformerAbstract;

use App\Models\Flight;


class FlightTransformer extends TransformerAbstract
{
    public function transform(Flight $flight)
    {
        $ret = [
            'id' => $flight->id,
            'airline' => [
                'id' => $flight->airline->id,
                'code' => $flight->airline->code,
                'name' => $flight->airline->name,
            ],
            'dpt' => [
                'id' => $flight->dpt_airport->id,
                'icao' => $flight->dpt_airport->icao,
                'name' => $flight->dpt_airport->name,
            ],
            'arr' => [
                'id' => $flight->arr_airport->id,
                'icao' => $flight->arr_airport->icao,
                'name' => $flight->arr_airport->name,
            ],
            'alt' => [],
        ];

        if($flight->alt_airport_id) {
            $flight['alt'] = [
                'id' => $flight->alt_airport->id,
                'icao' => $flight->alt_airport->icao,
                'name' => $flight->alt_airport->name,
            ];
        }

        return $ret;
    }
}
