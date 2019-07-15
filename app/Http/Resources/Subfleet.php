<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Subfleet extends Resource
{
    public function toArray($request)
    {
        $res = parent::toArray($request);
        $res['aircraft'] = Aircraft::collection($this->aircraft);

        return $res;
    }
}
