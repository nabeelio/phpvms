<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Subfleet extends Resource
{
    public function toArray($request)
    {
        $arr = parent::toArray($request);
        $arr['aircraft'] = Aircraft::collection($this->aircraft);

        return $arr;
    }
}
