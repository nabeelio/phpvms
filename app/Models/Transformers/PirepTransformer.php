<?php

namespace App\Models\Transformers;

use App\Models\Pirep;
use League\Fractal\TransformerAbstract;

class PirepTransformer extends TransformerAbstract
{
    public static $aptXform = null;
    public static $flightXform = null;

    public function __construct()
    {
        PirepTransformer::$aptXform = new AirportTransform();
        PirepTransformer::$flightXform = new FlightTransformer();
    }

    public function transform(Pirep $pirep)
    {
        $p = [
            'id'          => $pirep->id,
            'route_code'  => $pirep->route_code,
            'route_leg'   => $pirep->route_leg,
            'level'       => $pirep->level,
            'route'       => $pirep->route,
            'source'      => $pirep->source,
            'status'      => $pirep->status,
            'raw_data'    => $pirep->raw_data,
            'flight_time' => $pirep->flight_time,
            'aircraft'    => [],
            'dpt'         => PirepTransformer::$aptXform->transform($pirep->dpt_airport),
            'arr'         => PirepTransformer::$aptXform->transform($pirep->arr_airport),
            'user'        => [
                'id'       => $pirep->user->id,
                'pilot_id' => $pirep->user->pilot_id(),
            ],
        ];

        if ($pirep->flight_id) {
            $p['flight'] = PirepTransformer::$flightXform->transform($pirep->flight);
        }

        return $p;
    }
}
