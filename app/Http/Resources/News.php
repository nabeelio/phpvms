<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class Response
 * @package App\Http\Resources
 * Generic response resource
 */
class News extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $resp = parent::toArray($request);
        $resp['user'] = [
            'id'              => $this->user->id,
            'name'            => $this->user->name,
        ];

        return $resp;
    }
}
