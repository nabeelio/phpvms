<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * @mixin \App\Models\SimBrief
 */
class SimBrief extends Resource
{
    public function toArray($request)
    {
        return [
            'id'  => $this->id,
            'url' => $this->acars_flightplan_url,
        ];
    }
}
