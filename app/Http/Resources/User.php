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
            'ident'         => $this->ident,
            'name'          => $this->name,
            'email'         => $this->email,
            'rank_id'       => $this->rank_id,
            'home_airport'  => $this->home_airport_id,
            'curr_airport'  => $this->curr_airport_id,
            'last_pirep_id' => $this->last_pirep_id,
            'flights'       => $this->flights,
            'flight_time'   => $this->flight_time,
            'timezone'      => $this->timezone,
            'state'         => $this->state,
        ];

        $res['airline'] = Airline::make($this->airline);
        $res['bids'] = UserBid::collection($this->whenLoaded('bids'));
        $res['flights'] = new FlightResource($this->whenLoaded('flights'));
        $res['rank'] = Rank::make($this->rank);

        /*
         * Determine which avatar to send/use
         */
        $res['avatar'] = $this->avatar;
        if (empty($res['avatar'])) {
            $res['avatar'] = $this->gravatar();
        } else {
            $res['avatar'] = $res['avatar']->url;
        }

        return $res;
    }
}
