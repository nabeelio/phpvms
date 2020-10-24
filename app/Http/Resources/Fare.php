<?php

namespace App\Http\Resources;

use App\Contracts\Resource;
use App\Services\FareService;

/**
 * @mixin \App\Models\Fare
 */
class Fare extends Resource
{
    public function toArray($request)
    {
        /** @var FareService $fareSvc */
        $fareSvc = app(FareService::class);
        $fare = $fareSvc->getFares($this);

        return [
            'id'       => $fare->id,
            'code'     => $fare->code,
            'name'     => $fare->name,
            'capacity' => $fare->capacity,
            'cost'     => $fare->cost,
            'price'    => $fare->price,
            'type'     => $this->type,
            'notes'    => $this->notes,
            'active'   => $this->active,
        ];
    }
}
