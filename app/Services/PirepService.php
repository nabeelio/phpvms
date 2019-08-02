<?php

namespace App\Services;

use App\Contracts\Service;
use App\Events\PirepAccepted;
use App\Events\PirepFiled;
use App\Events\PirepRejected;
use App\Events\UserStateChanged;
use App\Events\UserStatsChanged;
use App\Models\Acars;
use App\Models\Aircraft;
use App\Models\Bid;
use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use App\Models\Enums\UserState;
use App\Models\Navdata;
use App\Models\Pirep;
use App\Models\PirepFieldValue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

/**
 * Class PirepService
 */
class PirepService extends Service
{
    private $geoSvc;
    private $pilotSvc;

    /**
     * PirepService constructor.
     *
     * @param GeoService  $geoSvc
     * @param UserService $pilotSvc
     */
    public function __construct(
        GeoService $geoSvc,
        UserService $pilotSvc
    ) {
        $this->geoSvc = $geoSvc;
        $this->pilotSvc = $pilotSvc;
    }

    /**
     * Find if there are duplicates to a given PIREP. Ideally, the passed
     * in PIREP hasn't been saved or gone through the create() method
     *
     * @param Pirep $pirep
     *
     * @return bool|Pirep
     */
    public function findDuplicate(Pirep $pirep)
    {
        $minutes = setting('pireps.duplicate_check_time', 10);
        $time_limit = Carbon::now()->subMinutes($minutes)->toDateTimeString();

        $where = [
            'user_id'       => $pirep->user_id,
            'airline_id'    => $pirep->airline_id,
            'flight_number' => $pirep->flight_number,
        ];

        if (filled($pirep->route_code)) {
            $where['route_code'] = $pirep->route_code;
        }

        if (filled($pirep->route_leg)) {
            $where['route_leg'] = $pirep->route_leg;
        }

        try {
            $found_pireps = Pirep::where($where)
                ->where('state', '!=', PirepState::CANCELLED)
                ->where('created_at', '>=', $time_limit)
                ->get();

            if ($found_pireps->count() === 0) {
                return false;
            }

            return $found_pireps[0];
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Save the route into the ACARS table with AcarsType::ROUTE
     * This attempts to create the route from the navdata and the route
     * entered into the PIREP's route field
     *
     * @param Pirep $pirep
     *
     * @throws \Exception
     *
     * @return Pirep
     */
    public function saveRoute(Pirep $pirep): Pirep
    {
        // Delete all the existing nav points
        Acars::where([
            'pirep_id' => $pirep->id,
            'type'     => AcarsType::ROUTE,
        ])->delete();

        // See if a route exists
        if (!filled($pirep->route)) {
            return $pirep;
        }

        if (!filled($pirep->dpt_airport)) {
            Log::error('saveRoute: dpt_airport not found: '.$pirep->dpt_airport_id);
            return $pirep;
        }

        $route = $this->geoSvc->getCoordsFromRoute(
            $pirep->dpt_airport_id,
            $pirep->arr_airport_id,
            [$pirep->dpt_airport->lat, $pirep->dpt_airport->lon],
            $pirep->route
        );

        /**
         * @var $point Navdata
         */
        $point_count = 1;
        foreach ($route as $point) {
            $acars = new Acars();
            $acars->pirep_id = $pirep->id;
            $acars->type = AcarsType::ROUTE;
            $acars->nav_type = $point->type;
            $acars->order = $point_count;
            $acars->name = $point->id;
            $acars->lat = $point->lat;
            $acars->lon = $point->lon;

            $acars->save();
            $point_count++;
        }

        return $pirep;
    }

    /**
     * Create a new PIREP with some given fields
     *
     * @param Pirep                   $pirep
     * @param array PirepFieldValue[] $field_values
     *
     * @return Pirep
     */
    public function create(Pirep $pirep, array $field_values = []): Pirep
    {
        if (empty($field_values)) {
            $field_values = [];
        }

        // Check the block times. If a block on (arrival) time isn't
        // specified, then use the time that it was submitted. It won't
        // be the most accurate, but that might be OK
        if (!$pirep->block_on_time) {
            if ($pirep->submitted_at) {
                $pirep->block_on_time = $pirep->submitted_at;
            } else {
                $pirep->block_on_time = Carbon::now('UTC');
            }
        }

        // If the depart time isn't set, then try to calculate it by
        // subtracting the flight time from the block_on (arrival) time
        if (!$pirep->block_off_time && $pirep->flight_time > 0) {
            $pirep->block_off_time = $pirep->block_on_time->subMinutes($pirep->flight_time);
        }

        // Check that there's a submit time
        if (!$pirep->submitted_at) {
            $pirep->submitted_at = Carbon::now('UTC');
        }

        $pirep->status = PirepStatus::ARRIVED;

        $pirep->save();
        $pirep->refresh();

        if (\count($field_values) > 0) {
            $this->updateCustomFields($pirep->id, $field_values);
        }

        return $pirep;
    }

    /**
     * Submit the PIREP. Figure out its default state
     *
     * @param Pirep $pirep
     *
     * @throws \Exception
     */
    public function submit(Pirep $pirep)
    {
        // Figure out what default state should be. Look at the default
        // behavior from the rank that the pilot is assigned to
        $default_state = PirepState::PENDING;
        if ($pirep->source === PirepSource::ACARS) {
            if ($pirep->pilot->rank->auto_approve_acars) {
                $default_state = PirepState::ACCEPTED;
            }
        } else {
            if ($pirep->pilot->rank->auto_approve_manual) {
                $default_state = PirepState::ACCEPTED;
            }
        }

        Log::info('New PIREP filed', [$pirep]);
        event(new PirepFiled($pirep));

        // only update the pilot last state if they are accepted
        if ($default_state === PirepState::ACCEPTED) {
            $pirep = $this->accept($pirep);
            $this->setPilotState($pirep->pilot, $pirep);
        } else {
            $pirep->state = $default_state;
        }

        $pirep->save();

        // Check the user state, set them to ACTIVE if on leave
        if ($pirep->user->state !== UserState::ACTIVE) {
            $old_state = $pirep->user->state;
            $pirep->user->state = UserState::ACTIVE;
            $pirep->user->save();

            event(new UserStateChanged($pirep->user, $old_state));
        }
    }

    /**
     * Update any custom PIREP fields
     *
     * @param       $pirep_id
     * @param array $field_values
     */
    public function updateCustomFields($pirep_id, array $field_values)
    {
        foreach ($field_values as $fv) {
            PirepFieldValue::updateOrCreate(
                ['pirep_id' => $pirep_id,
                 'name'     => $fv['name'],
                ],
                ['value'  => $fv['value'],
                 'source' => $fv['source'],
                ]
            );
        }
    }

    /**
     * @param Pirep $pirep
     * @param int   $new_state
     *
     * @throws \Exception
     *
     * @return Pirep
     */
    public function changeState(Pirep $pirep, int $new_state)
    {
        Log::info('PIREP '.$pirep->id.' state change from '.$pirep->state.' to '.$new_state);

        if ($pirep->state === $new_state) {
            return $pirep;
        }

        /*
         * Move from a PENDING status into either ACCEPTED or REJECTED
         */
        if ($pirep->state === PirepState::PENDING) {
            if ($new_state === PirepState::ACCEPTED) {
                return $this->accept($pirep);
            }

            if ($new_state === PirepState::REJECTED) {
                return $this->reject($pirep);
            }

            return $pirep;
        }

        /*
         * Move from a ACCEPTED to REJECTED status
         */
        if ($pirep->state === PirepState::ACCEPTED) {
            $pirep = $this->reject($pirep);
            return $pirep;
        }

        /*
         * Move from REJECTED to ACCEPTED
         */
        if ($pirep->state === PirepState::REJECTED) {
            $pirep = $this->accept($pirep);
            return $pirep;
        }

        return $pirep->refresh();
    }

    /**
     * @param Pirep $pirep
     *
     * @throws \Exception
     *
     * @return Pirep
     */
    public function accept(Pirep $pirep): Pirep
    {
        // moving from a REJECTED state to ACCEPTED, reconcile statuses
        if ($pirep->state === PirepState::ACCEPTED) {
            return $pirep;
        }

        $ft = $pirep->flight_time;
        $pilot = $pirep->pilot;

        $this->pilotSvc->adjustFlightTime($pilot, $ft);
        $this->pilotSvc->adjustFlightCount($pilot, +1);
        $this->pilotSvc->calculatePilotRank($pilot);
        $pirep->pilot->refresh();

        // Change the status
        $pirep->state = PirepState::ACCEPTED;
        $pirep->save();
        $pirep->refresh();

        Log::info('PIREP '.$pirep->id.' state change to ACCEPTED');

        // Update the aircraft
        $pirep->aircraft->flight_time = $pirep->aircraft->flight_time + $pirep->flight_time;
        $pirep->aircraft->airport_id = $pirep->arr_airport_id;
        $pirep->aircraft->landing_time = $pirep->updated_at;
        $pirep->aircraft->save();

        $pirep->refresh();

        $this->setPilotState($pilot, $pirep);
        event(new PirepAccepted($pirep));

        return $pirep;
    }

    /**
     * @param Pirep $pirep
     *
     * @return Pirep
     */
    public function reject(Pirep $pirep): Pirep
    {
        // If this was previously ACCEPTED, then reconcile the flight hours
        // that have already been counted, etc
        if ($pirep->state === PirepState::ACCEPTED) {
            $pilot = $pirep->pilot;
            $ft = $pirep->flight_time * -1;

            $this->pilotSvc->adjustFlightTime($pilot, $ft);
            $this->pilotSvc->adjustFlightCount($pilot, -1);
            $this->pilotSvc->calculatePilotRank($pilot);
            $pirep->pilot->refresh();
        }

        // Change the status
        $pirep->state = PirepState::REJECTED;
        $pirep->save();
        $pirep->refresh();

        $pirep->aircraft->flight_time -= $pirep->flight_time;
        $pirep->aircraft->save();

        Log::info('PIREP '.$pirep->id.' state change to REJECTED');

        event(new PirepRejected($pirep));

        return $pirep;
    }

    /**
     * @param Pirep $pirep
     */
    public function setPilotState(User $pilot, Pirep $pirep)
    {
        $pilot->refresh();

        $previous_airport = $pilot->curr_airport_id;
        $pilot->curr_airport_id = $pirep->arr_airport_id;
        $pilot->last_pirep_id = $pirep->id;
        $pilot->save();

        $pirep->refresh();

        event(new UserStatsChanged($pilot, 'airport', $previous_airport));
    }

    /**
     * If the setting is enabled, remove the bid
     *
     * @param Pirep $pirep
     *
     * @throws \Exception
     */
    public function removeBid(Pirep $pirep)
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
