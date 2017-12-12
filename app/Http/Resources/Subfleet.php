<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Subfleet extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'airline_id' => $this->airline_id,
            'name' => $this->name,
            'type' => $this->type,
            'fuel_type' => $this->fuel_type,
            'cargo_capacity' => $this->cargo_capacity,
            'fuel_capacity' => $this->fuel_capacity,
            'gross_weight' => $this->gross_weight,

            'aircraft' => Aircraft::collection($this->aircraft),
        ];
    }
}
