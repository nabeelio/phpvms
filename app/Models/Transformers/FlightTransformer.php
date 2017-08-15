<?php

namespace App\Models\Transformers;

use App\Models\Flight;
use League\Fractal\TransformerAbstract;


class FlightTransformer extends TransformerAbstract
{
    public static $aptXform = null;

    public function __construct()
    {
        FlightTransformer::$aptXform = new AirportTransform();
    }

    public function transform(Flight $flight)
    {
        $ret = [
            'id' => $flight->id,
            'airline' => [
                'id' => $flight->airline->id,
                'code' => $flight->airline->code,
                'name' => $flight->airline->name,
            ],
            'dpt' => FlightTransformer::$aptXform->transform($flight->dpt_airport),
            'arr' => FlightTransformer::$aptXform->transform($flight->arr_airport),
            'alt' => [],
        ];

        if($flight->alt_airport_id) {
            $flight['alt'] = FlightTransformer::$aptXform->transform($flight->alt_airport);
        }

        return $ret;
    }
}
