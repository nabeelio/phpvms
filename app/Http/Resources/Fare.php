<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Fare extends Resource
{
    public function toArray($request)
    {
        return [
            'code'     => $this->code,
            'name'     => $this->name,
            'price'    => $this->price,
            'cost'     => $this->code,
            'capacity' => $this->capacity,
            'notes'    => $this->notes,
            'active'   => $this->active,
        ];
    }
}
