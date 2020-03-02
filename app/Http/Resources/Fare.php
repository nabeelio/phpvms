<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * @mixin \App\Models\Fare
 */
class Fare extends Resource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'code'     => $this->code,
            'name'     => $this->name,
            'price'    => $this->price,
            'cost'     => $this->cost,
            'capacity' => $this->capacity,
            'notes'    => $this->notes,
            'active'   => $this->active,
        ];
    }
}
