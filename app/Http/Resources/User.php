<?php

namespace App\Http\Resources;

use App\Contracts\Resource;

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
            'name'          => $this->name_private,
            'name_private'  => $this->name_private,
            'avatar'        => $this->resolveAvatarUrl(),
            'rank_id'       => $this->rank_id,
            'home_airport'  => $this->home_airport_id,
            'curr_airport'  => $this->curr_airport_id,
            'last_pirep_id' => $this->last_pirep_id,
            'flights'       => $this->flights,
            'flight_time'   => $this->flight_time,
            'transfer_time' => $this->transfer_time,
            'timezone'      => $this->timezone,
            'state'         => $this->state,
        ];

        $res['airline'] = Airline::make($this->whenLoaded('airline'));
        $res['bids'] = UserBid::collection($this->whenLoaded('bids'));
        $res['rank'] = Rank::make($this->whenLoaded('rank'));
        $res['subfleets'] = Subfleet::make($this->whenLoaded('subfleets'));

        return $res;
    }
}
