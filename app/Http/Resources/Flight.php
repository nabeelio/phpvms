<?php

namespace App\Http\Resources;

use App\Support\Units\Distance;
use Illuminate\Http\Resources\Json\Resource;

class Flight extends Resource
{
    public function toArray($request)
    {
        $flight = parent::toArray($request);

        // Return multiple measures so the client can pick what they want
        if(\in_array('distance', $flight, true)
            && $flight['distance'] instanceof Distance)
        {
            $flight['distance'] = $flight['distance']->toObject();
        }

        $flight['airline'] = new Airline($this->airline);
        $flight['subfleets'] = Subfleet::collection($this->subfleets);

        return $flight;
    }
}
