<?php

namespace App\Http\Resources;

use App\Models\Enums\NavaidType;
use Illuminate\Http\Resources\Json\Resource;

class Navdata extends Resource
{
    public function toArray($request)
    {
        $point = parent::toArray($request);

        // Some details about the navaid type
        $type = [
            'type' => $point['type'],
            'name' => NavaidType::label($point['type']),
        ];

        $point['type'] = $type;

        return $point;
    }
}
