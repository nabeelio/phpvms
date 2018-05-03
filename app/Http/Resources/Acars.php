<?php

namespace App\Http\Resources;

use App\Support\Units\Distance;
use App\Support\Units\Fuel;
use Illuminate\Http\Resources\Json\Resource;

class Acars extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $obj = parent::toArray($request);

        if ($this->distance instanceof Distance) {
            $obj['distance'] = $this->distance->units;
        }

        if ($this->fuel instanceof Fuel) {
            $obj['fuel'] = $this->fuel->units;
        }

        return $obj;
    }
}
