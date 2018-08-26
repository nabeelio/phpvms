<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserBid extends Resource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'user_id'   => $this->user_id,
            'flight_id' => $this->flight_id,
            'flights'   => Flight::collection($this->whenLoaded('flight')),
            #'created_at' => $this->created_at,
            #'updated_at' => $this->updated_at,
        ];
    }
}
