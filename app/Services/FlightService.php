<?php

namespace App\Services;

use App\Exceptions\BidExists;
use App\Interfaces\Service;
use App\Models\Bid;
use App\Models\Flight;
use App\Models\FlightFieldValue;
use App\Models\User;
use App\Repositories\FlightRepository;
use App\Repositories\NavdataRepository;
use Log;

/**
 * Class FlightService
 * @package App\Services
 */
class FlightService extends Service
{
    private $fareSvc,
            $flightRepo,
            $navDataRepo,
            $userSvc;

    /**
     * FlightService constructor.
     * @param FareService       $fareSvc
     * @param FlightRepository  $flightRepo
     * @param NavdataRepository $navdataRepo
     * @param UserService       $userSvc
     */
    public function __construct(
        FareService $fareSvc,
        FlightRepository $flightRepo,
        NavdataRepository $navdataRepo,
        UserService $userSvc
    ) {
        $this->fareSvc = $fareSvc;
        $this->flightRepo = $flightRepo;
        $this->navDataRepo = $navdataRepo;
        $this->userSvc = $userSvc;
    }

    /**
     * Filter out any flights according to different settings
     * @param $user
     * @return FlightRepository
     */
    public function filterFlights($user)
    {
        $where = [];
        if (setting('pilots.only_flights_from_current', false)) {
            $where['dpt_airport_id'] = $user->curr_airport_id;
        }

        return $this->flightRepo
            ->whereOrder($where, 'flight_number', 'asc');
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
        if (setting('pireps.only_aircraft_at_dep_airport', false)) {
            foreach ($subfleets as $subfleet) {
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
    public function deleteFlight(Flight $flight): void
    {
        $where = ['flight_id' => $flight->id];
        Bid::where($where)->delete();
        $flight->delete();
    }

    /**
     * Update any custom PIREP fields
     * @param Flight  $flight_id
     * @param array   $field_values
     */
    public function updateCustomFields(Flight $flight_id, array $field_values): void
    {
        foreach ($field_values as $fv) {
            FlightFieldValue::updateOrCreate(
                [
                    'flight_id' => $flight_id,
                    'name'     => $fv['name'],
                ],
                [
                    'value'  => $fv['value']
                ]
            );
        }
    }

    /**
     * Return all of the navaid points as a collection
     * @param Flight $flight
     * @return \Illuminate\Support\Collection
     */
    public function getRoute(Flight $flight)
    {
        if (!$flight->route) {
            return collect();
        }

        $route_points = array_map(function ($point) {
            return strtoupper($point);
        }, explode(' ', $flight->route));

        $route = $this->navDataRepo->findWhereIn('id', $route_points);

        // Put it back into the original order the route is in
        $return_points = [];
        foreach ($route_points as $rp) {
            $return_points[] = $route->where('id', $rp)->first();
        }

        return collect($return_points);
    }

    /**
     * Allow a user to bid on a flight. Check settings and all that good stuff
     * @param Flight $flight
     * @param User   $user
     * @return Bid|null
     * @throws \App\Exceptions\BidExists
     */
    public function addBid(Flight $flight, User $user)
    {
        # If it's already been bid on, then it can't be bid on again
        if ($flight->has_bid && setting('bids.disable_flight_on_bid')) {
            Log::info($flight->id.' already has a bid, skipping');
            throw new BidExists();
        }

        # See if we're allowed to have multiple bids or not
        if (!setting('bids.allow_multiple_bids')) {
            $user_bids = Bid::where(['user_id' => $user->id])->first();
            if ($user_bids) {
                Log::info('User "'.$user->id.'" already has bids, skipping');
                throw new BidExists();
            }
        }

        # See if this user has this flight bid on already
        $bid_data = [
            'user_id'   => $user->id,
            'flight_id' => $flight->id
        ];

        $user_bid = Bid::where($bid_data)->first();
        if ($user_bid) {
            return $user_bid;
        }

        $user_bid = Bid::create($bid_data);

        $flight->has_bid = true;
        $flight->save();

        return $user_bid;
    }

    /**
     * Remove a bid from a given flight
     * @param Flight $flight
     * @param User   $user
     */
    public function removeBid(Flight $flight, User $user)
    {
        $user_bid = Bid::where([
            'flight_id' => $flight->id, 'user_id' => $user->id
        ])->first();

        if ($user_bid) {
            $user_bid->forceDelete();
        }

        # Only flip the flag if there are no bids left for this flight
        if (!Bid::where('flight_id', $flight->id)->exists()) {
            $flight->has_bid = false;
            $flight->save();
        }
    }
}
