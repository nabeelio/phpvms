<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * ACARS table but only include the fields for the routes
 * Class AcarsRoute
 * @package App\Http\Resources
 */
class AcarsLog extends Resource
{
    public function toArray($request)
    {
        return parent::toArray($request);
        /*return [
            'id' => $this->id,
            'name' => $this->name,
            'log' => $this->log,
            'lat' => $this->lat,
            'lon' => $this->lon,
        ];*/
    }
}
