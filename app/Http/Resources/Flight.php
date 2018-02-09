<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Flight extends Resource
{
    public function toArray($request)
    {
        $flight = parent::toArray($request);

        $flight['airline'] = new Airline($this->airline);
        $flight['subfleets'] = Subfleet::collection($this->subfleets);

        return $flight;
    }
}
