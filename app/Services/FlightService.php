<?php
/**
 * Created by IntelliJ IDEA.
 * User: nshahzad
 * Date: 12/12/17
 * Time: 2:48 PM
 */

namespace App\Services;

use Log;
use App\Models\Flight;
use App\Models\User;
use App\Models\UserBid;

class FlightService extends BaseService
{
    /**
     * Allow a user to bid on a flight. Check settings and all that good stuff
     * @param Flight $flight
     * @param User $user
     * @return UserBid
     */
    public function addBid(Flight $flight, User $user): UserBid
    {
        # If it's already been bid on, then it can't be bid on again
        if($flight->has_bid && setting('bids.disable_flight_on_bid')) {
            Log.info($flight->id . ' already has a bid, skipping');
            return null;
        }

        # See if we're allowed to have multiple bids or not
        if(!setting('bids.allow_multiple_bids')) {
            $user_bids = UserBid::where(['user_id' => $user->id])->first();
            if($user_bids) {
                Log.info('User "' . $user->id . '" already has bids, skipping');
                return null;
            }
        }

        # See if this user has this flight bid on already
        $bid_data = [
            'user_id' => $user->id,
            'flight_id' => $flight->id
        ];

        $user_bid = UserBid::where($bid_data)->first();
        if($user_bid) {
            return $user_bid;
        }

        $user_bid = UserBid::create($bid_data);

        $flight->has_bid = true;
        $flight->save();

        return $user_bid;
    }

    /**
     * Remove a bid from a given flight
     * @param Flight $flight
     * @param User $user
     * @throws Exception
     */
    public function removeBid(Flight $flight, User $user)
    {
        $user_bid = UserBid::where([
            'flight_id' => $flight->id, 'user_id' => $user->id
        ])->first();

        if($user_bid) {
            $user_bid->forceDelete();
        }

        $flight->has_bid = false;
        $flight->save();
    }
}
