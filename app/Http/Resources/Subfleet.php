<?php

namespace App\Http\Resources;

use App\Contracts\Resource;

class Subfleet extends Resource
{
    public function toArray($request)
    {
        $res = parent::toArray($request);
        $res['fares'] = Fare::collection($this->fares);
        $res['aircraft'] = Aircraft::collection($this->aircraft);

        return $res;
    }
}
