<?php

namespace App\Http\Resources;

class Airline extends Response
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'icao'    => $this->icao,
            'iata'    => $this->iata,
            'name'    => $this->name,
            'country' => $this->country,
            'logo'    => $this->logo,
            //'active' => $this->active,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ];
    }
}
