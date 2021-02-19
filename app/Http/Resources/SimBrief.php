<?php

namespace App\Http\Resources;

use App\Contracts\Resource;

/**
 * @mixin \App\Models\SimBrief
 */
class SimBrief extends Resource
{
    public function toArray($request)
    {
        return [
            'id'  => $this->id,
            'url' => url(route('api.flights.briefing', ['id' => $this->id])),
        ];
    }
}
