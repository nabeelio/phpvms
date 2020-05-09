<?php

namespace App\Http\Resources;

use App\Contracts\Resource;

class Award extends Resource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'image'       => $this->image,
        ];
    }
}
