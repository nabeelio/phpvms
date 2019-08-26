<?php

namespace App\Http\Resources;

class AirportDistance extends Response
{
    public function toArray($request)
    {
        $res = parent::toArray($request);
        $res['distance'] = $res['distance']->getResponseUnits();

        return $res;
    }
}
