<?php

namespace App\Services;

use App\Contracts\Service;
use App\Exceptions\BidExistsForFlight;
use App\Exceptions\UserBidLimit;
use App\Models\Bid;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BidService extends Service
{
    /**
     * Find all of the bids for a given user
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function findBidsForUser(User $user)
    {
        $bids = Bid::where(['user_id' => $user->id])->get();
        return $bids;
    }

    /**
     * Allow a user to bid on a flight. Check settings and all that good stuff
     *
     * @param Flight $flight
     * @param User   $user
     *
     * @throws \App\Exceptions\BidExistsForFlight
     *
     * @return mixed
     */
    public function addBid(Flight $flight, User $user)
    {
        // Get all of the bids for this user. See if they're allowed to have multiple
        // bids
        $bid_count = Bid::where(['user_id' => $user->id])->count();
        if ($bid_count > 0 && setting('bids.allow_multiple_bids') === false) {
            throw new UserBidLimit($user);
        }

        // Get all of the bids for this flight
        $bids = Bid::where(['flight_id' => $flight->id])->get();
        if ($bids->count() > 0) {
            // Does the flight have a bid set?
            if ($flight->has_bid === false) {
                $flight->has_bid = true;
                $flight->save();
            }

            // Check all the bids for one of this user
            foreach ($bids as $bid) {
                if ($bid->user_id === $user->id) {
                    Log::info('Bid exists, user='.$user->ident.', flight='.$flight->id);

                    return $bid;
                }
            }

            // Check if the flight should be blocked off
            if (setting('bids.disable_flight_on_bid') === true) {
                throw new BidExistsForFlight($flight);
            }

            if (setting('bids.allow_multiple_bids') === false) {
                throw new BidExistsForFlight($flight);
            }
        } else {
            /* @noinspection NestedPositiveIfStatementsInspection */
            if ($flight->has_bid === true) {
                Log::info('Bid exists, flight='.$flight->id.'; no entry in bids table, cleaning up');
            }
        }

        $bid = Bid::firstOrCreate([
            'user_id'   => $user->id,
            'flight_id' => $flight->id,
        ]);

        $flight->has_bid = true;
        $flight->save();

        return $bid;
    }

    /**
     * Remove a bid from a given flight
     *
     * @param Flight $flight
     * @param User   $user
     */
    public function removeBid(Flight $flight, User $user)
    {
        $bids = Bid::where([
            'flight_id' => $flight->id,
            'user_id'   => $user->id,
        ])->get();

        foreach ($bids as $bid) {
            $bid->forceDelete();
        }

        // Only flip the flag if there are no bids left for this flight
        $bid_count = Bid::where(['flight_id' => $flight->id])->count();
        if ($bid_count === 0) {
            $flight->has_bid = false;
            $flight->save();
        }
    }

    /**
     * If the setting is enabled, remove the bid
     *
     * @param Pirep $pirep
     *
     * @throws \Exception
     */
    public function removeBidForPirep(Pirep $pirep)
    {
        if (!setting('pireps.remove_bid_on_accept')) {
            return;
        }

        $flight = $pirep->flight;
        if (!$flight) {
            return;
        }

        $bid = Bid::where([
            'user_id'   => $pirep->user->id,
            'flight_id' => $flight->id,
        ]);

        if ($bid) {
            Log::info('Bid for user: '.$pirep->user->ident.' on flight '.$flight->ident);
            $bid->delete();
        }
    }
}
