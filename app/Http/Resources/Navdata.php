<?php

namespace App\Http\Resources;

use App\Models\Enums\NavaidType;
use Illuminate\Http\Resources\Json\Resource;

class Navdata extends Resource
{
    public function toArray($request)
    {
        $res = parent::toArray($request);

        // Some details about the navaid type
        $type = [
            'type' => $res['type'],
            'name' => NavaidType::label($res['type']),
        ];

        $res['type'] = $type;

        return $res;
    }
}
