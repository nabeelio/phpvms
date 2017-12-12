<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Flight extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'airline' => $this->airline,
            'flight_number' => $this->flight_number,
            'route_code' => $this->route_code,
            'route_leg' => $this->route_leg,
            'dpt_airport_id' => $this->dpt_airport_id,
            'arr_airport_id' => $this->arr_airport_id,
            'alt_airport_id' => $this->alt_airport_id,
            'route' => $this->route,
            'dpt_time' => $this->dpt_time,
            'arr_time' => $this->arr_time,
            'flight_time' => $this->flight_time,
            'notes' => $this->notes,
            'active' => $this->active,

            'subfleet' => Subfleet::collection($this->subfleets),
            #'created_at' => $this->created_at,
            #'updated_at' => $this->updated_at,
        ];
    }
}
