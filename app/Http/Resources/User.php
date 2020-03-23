<?php

namespace App\Http\Resources;

use App\Contracts\Resource;

class User extends Resource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'pilot_id'      => $this->pilot_id,
            'ident'         => $this->ident,
            'name'          => $this->name,
            'email'         => $this->email,
            'apikey'        => $this->apikey,
            'rank_id'       => $this->rank_id,
            'home_airport'  => $this->home_airport_id,
            'curr_airport'  => $this->curr_airport_id,
            'last_pirep_id' => $this->last_pirep_id,
            'flights'       => $this->flight,
            'flight_time'   => $this->flight_time,
            'balance'       => $this->balance,
            'timezone'      => $this->timezone,
            'status'        => $this->status,
            'state'         => $this->state,

            'airline' => Airline::make($this->airline),
            'bids'    => UserBid::collection($this->bids),
            'rank'    => Rank::make($this->rank),
        ];
    }
}
