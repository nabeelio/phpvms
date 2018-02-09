<?php
/**
 * Created by IntelliJ IDEA.
 * User: nshahzad
 * Date: 12/12/17
 * Time: 2:48 PM
 */

namespace App\Services;

use function foo\func;
use Log;

use App\Models\Flight;
use App\Models\User;
use App\Models\UserBid;

class FlightService extends BaseService
{
    protected $userSvc;

    public function __construct(UserService $userSvc)
    {
        $this->userSvc = $userSvc;
    }

    /**
     * Filter out subfleets to only include aircraft that a user has access to
     * @param $user
     * @param $flight
     * @return mixed
     */
    public function filterSubfleets($user, $flight)
    {

        $subfleets = $flight->subfleets;

        /**
         * Only allow aircraft that the user has access to in their rank
         */
        if (setting('pireps.restrict_aircraft_to_rank', false)) {
            $allowed_subfleets = $this->userSvc->getAllowableSubfleets($user)->pluck('id');
            $subfleets = $subfleets->filter(function ($subfleet, $i) use ($allowed_subfleets) {
                if ($allowed_subfleets->contains($subfleet->id)) {
                    return true;
                }
            });
        }

        /**
         * Only allow aircraft that are at the current departure airport
         */
        if(setting('pireps.only_aircraft_at_dep_airport', false)) {
            foreach($subfleets as $subfleet) {
                $subfleet->aircraft = $subfleet->aircraft->filter(
                    function ($aircraft, $i) use ($flight) {
                        if ($aircraft->airport_id === $flight->dpt_airport_id) {
                            return true;
                        }
                    }
                );
            }
        }

        $flight->subfleets = $subfleets;

        return $flight;
    }

    /**
     * Delete a flight, and all the user bids, etc associated with it
     * @param Flight $flight
     * @throws \Exception
     */
    public function deleteFlight(Flight $flight)
    {
        $where = ['flight_id' => $flight->id];
        UserBid::where($where)->delete();
        $flight->delete();
    }

    /**
     * Allow a user to bid on a flight. Check settings and all that good stuff
     * @param Flight $flight
     * @param User $user
     * @return UserBid|null
     */
    public function addBid(Flight $flight, User $user)
    {
        # If it's already been bid on, then it can't be bid on again
        if($flight->has_bid && setting('bids.disable_flight_on_bid')) {
            Log::info($flight->id . ' already has a bid, skipping');
            return null;
        }

        # See if we're allowed to have multiple bids or not
        if(!setting('bids.allow_multiple_bids')) {
            $user_bids = UserBid::where(['user_id' => $user->id])->first();
            if($user_bids) {
                Log::info('User "' . $user->id . '" already has bids, skipping');
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

        # Only flip the flag if there are no bids left for this flight
        if(!UserBid::where('flight_id', $flight->id)->exists()) {
            $flight->has_bid = false;
            $flight->save();
        }
    }
}
