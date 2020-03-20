<?php

namespace App\Http\Resources;

use App\Contracts\Resource;

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
            'type'     => $this->type,
            'notes'    => $this->notes,
            'active'   => $this->active,
        ];
    }
}
