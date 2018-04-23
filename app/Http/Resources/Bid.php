<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Bid extends Resource
{
    public function toArray($request)
    {
        $bid = parent::toArray($request);
        $bid['flight'] = new Flight($this->flight);

        return $bid;
    }
}
