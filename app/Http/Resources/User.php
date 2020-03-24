<?php

namespace App\Http\Resources;

use App\Contracts\Resource;
use App\Http\Resources\Flight as FlightResource;

/**
 * @mixin \App\Models\User
 */
class User extends Resource
{
    public function toArray($request)
    {
        $res = [
            'id'            => $this->id,
            'pilot_id'      => $this->pilot_id,
            'avatar'        => $this->avatar,
            'ident'         => $this->ident,
            'name'          => $this->name,
            'email'         => $this->email,
            'rank_id'       => $this->rank_id,
            'home_airport'  => $this->home_airport_id,
            'curr_airport'  => $this->curr_airport_id,
            'last_pirep_id' => $this->last_pirep_id,
            'flight_time'   => $this->flight_time,
            'timezone'      => $this->timezone,
            'state'         => $this->state,
            'rank'          => Rank::make($this->rank),
        ];

        $res['airline'] = Airline::make($this->airline);
        $res['bids'] = UserBid::collection($this->whenLoaded('bids'));
        $res['flights'] = new FlightResource($this->whenLoaded('flights'));

        return $res;
    }
}
