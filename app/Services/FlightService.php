<?php

namespace App\Services;

use App\Contracts\Service;
use App\Exceptions\BidExistsForFlight;
use App\Exceptions\DuplicateFlight;
use App\Models\Bid;
use App\Models\Enums\Days;
use App\Models\Flight;
use App\Models\FlightFieldValue;
use App\Models\User;
use App\Repositories\FlightRepository;
use App\Repositories\NavdataRepository;
use App\Support\Units\Time;
use Illuminate\Support\Facades\Log;

class FlightService extends Service
{
    private $airportSvc;
    private $flightRepo;
    private $navDataRepo;
    private $userSvc;

    /**
     * FlightService constructor.
     *
     * @param AirportService    $airportSvc
     * @param FlightRepository  $flightRepo
     * @param NavdataRepository $navdataRepo
     * @param UserService       $userSvc
     */
    public function __construct(
        AirportService $airportSvc,
        FlightRepository $flightRepo,
        NavdataRepository $navdataRepo,
        UserService $userSvc
    ) {
        $this->airportSvc = $airportSvc;
        $this->flightRepo = $flightRepo;
        $this->navDataRepo = $navdataRepo;
        $this->userSvc = $userSvc;
    }

    /**
     * Create a new flight
     *
     * @param array $fields
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createFlight($fields)
    {
        $fields['dpt_airport_id'] = strtoupper($fields['dpt_airport_id']);
        $fields['arr_airport_id'] = strtoupper($fields['arr_airport_id']);

        $flightTmp = new Flight($fields);
        if ($this->isFlightDuplicate($flightTmp)) {
            throw new DuplicateFlight($flightTmp);
        }

        $this->airportSvc->lookupAirportIfNotFound($fields['dpt_airport_id']);
        $this->airportSvc->lookupAirportIfNotFound($fields['arr_airport_id']);

        $fields = $this->transformFlightFields($fields);
        $flight = $this->flightRepo->create($fields);

        return $flight;
    }

    /**
     * Update a flight with values from the given fields
     *
     * @param Flight $flight
     * @param array  $fields
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \App\Models\Flight|mixed
     */
    public function updateFlight($flight, $fields)
    {
        // apply the updates here temporarily, don't save
        // the repo->update() call will actually do it
        $flight->fill($fields);

        if ($this->isFlightDuplicate($flight)) {
            throw new DuplicateFlight($flight);
        }

        $fields = $this->transformFlightFields($fields);
        $flight = $this->flightRepo->update($fields, $flight->id);

        return $flight;
    }

    /**
     * Check the fields for a flight and transform them
     *
     * @param array $fields
     *
     * @return array
     */
    protected function transformFlightFields($fields)
    {
        if (array_key_exists('days', $fields) && filled($fields['days'])) {
            $fields['days'] = Days::getDaysMask($fields['days']);
        }

        $fields['flight_time'] = Time::init($fields['minutes'], $fields['hours'])->getMinutes();
        $fields['active'] = get_truth_state($fields['active']);

        // Figure out a distance if not found
        if (empty($fields['distance'])) {
            $fields['distance'] = $this->airportSvc->calculateDistance(
                $fields['dpt_airport_id'],
                $fields['arr_airport_id']
            );
        }

        return $fields;
    }

    /**
     * Filter out any flights according to different settings
     *
     * @param $user
     *
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
     *
     * @param $user
     * @param $flight
     *
     * @return mixed
     */
    public function filterSubfleets($user, $flight)
    {
        $subfleets = $flight->subfleets;

        /*
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

        /*
         * Only allow aircraft that are at the current departure airport
         */
        if (setting('pireps.only_aircraft_at_dpt_airport', false)) {
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
     * Check if this flight has a duplicate already
     *
     * @param Flight $flight
     *
     * @return bool
     */
    public function isFlightDuplicate(Flight $flight)
    {
        $where = [
            ['id', '<>', $flight->id],
            'airline_id'    => $flight->airline_id,
            'flight_number' => $flight->flight_number,
        ];

        $found_flights = $this->flightRepo->findWhere($where);
        if ($found_flights->count() === 0) {
            return false;
        }

        // Find within all the flights with the same flight number
        // Return any flights that have the same route code and leg
        // If this list is > 0, then this has a duplicate
        $found_flights = $found_flights->filter(function ($value, $key) use ($flight) {
            return $flight->route_code === $value->route_code
                && $flight->route_leg === $value->route_leg;
        });

        return !($found_flights->count() === 0);
    }

    /**
     * Delete a flight, and all the user bids, etc associated with it
     *
     * @param Flight $flight
     *
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
     *
     * @param Flight $flight
     * @param array  $field_values
     */
    public function updateCustomFields(Flight $flight, array $field_values): void
    {
        foreach ($field_values as $fv) {
            FlightFieldValue::updateOrCreate(
                [
                    'flight_id' => $flight->id,
                    'name'      => $fv['name'],
                ],
                [
                    'value' => $fv['value'],
                ]
            );
        }
    }

    /**
     * Return all of the navaid points as a collection
     *
     * @param Flight $flight
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRoute(Flight $flight)
    {
        if (!$flight->route) {
            return collect();
        }

        $route_points = array_map('strtoupper', explode(' ', $flight->route));

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
     *
     * @param Flight $flight
     * @param User   $user
     *
     *@throws \App\Exceptions\BidExistsForFlight
     *
     * @return mixed
     */
    public function addBid(Flight $flight, User $user)
    {
        // Get all of the bids for this user. See if they're allowed to have multiple
        // bids
        $bids = Bid::where('user_id', $user->id)->get();
        if ($bids->count() > 0 && setting('bids.allow_multiple_bids') === false) {
            throw new BidExistsForFlight('User "'.$user->ident.'" already has bids, skipping');
        }

        // Get all of the bids for this flight
        $bids = Bid::where('flight_id', $flight->id)->get();
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
        $bids = Bid::where('flight_id', $flight->id)->get();
        if ($bids->count() === 0) {
            $flight->has_bid = false;
            $flight->save();
        }
    }
}
