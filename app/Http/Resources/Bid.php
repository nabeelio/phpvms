<?php

namespace App\Http\Resources;

class Bid extends Response
{
    public function toArray($request)
    {
        $res = parent::toArray($request);
        $res['flight'] = new Flight($this->flight);

        return $res;
    }
}
