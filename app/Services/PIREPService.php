<?php

namespace App\Services;

use Log;
use Carbon\Carbon;
use App\Repositories\AcarsRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Acars;
use App\Models\Navdata;
use App\Models\Pirep;
use App\Models\PirepFieldValues;
use App\Models\User;

use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;

use App\Events\PirepAccepted;
use App\Events\PirepFiled;
use App\Events\PirepRejected;
use App\Events\UserStatsChanged;

use App\Repositories\NavdataRepository;
use App\Repositories\PirepRepository;

class PIREPService extends BaseService
{
    protected $acarsRepo,
        $geoSvc,
        $navRepo,
        $pilotSvc,
        $pirepRepo;

    /**
     * PIREPService constructor.
     * @param UserService $pilotSvc
     * @param GeoService $geoSvc
     * @param NavdataRepository $navRepo
     * @param PirepRepository $pirepRepo
     */
    public function __construct(
        AcarsRepository $acarsRepo,
        GeoService $geoSvc,
        NavdataRepository $navRepo,
        PirepRepository $pirepRepo,
        UserService $pilotSvc
    )
    {
        $this->acarsRepo = $acarsRepo;
        $this->geoSvc = $geoSvc;
        $this->pilotSvc = $pilotSvc;
        $this->navRepo = $navRepo;
        $this->pirepRepo = $pirepRepo;
    }

    /**
     * Find if there are duplicates to a given PIREP. Ideally, the passed
     * in PIREP hasn't been saved or gone through the create() method
     * @param Pirep $pirep
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

        if(!empty($pirep->route_code)) {
            $where['route_code'] = $pirep->route_code;
        }

        if(!empty($pirep->route_leg)) {
            $where['route_leg'] = $pirep->route_leg;
        }

        try {
            $found_pireps = Pirep::where($where)
                            ->where('created_at', '>=', $time_limit)
                            ->get();

            if($found_pireps->count() === 0) {
                return false;
            }

            return $found_pireps[0];
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Save the route into the ACARS table with AcarsType::ROUTE
     * @param Pirep $pirep
     * @return Pirep
     */
    public function saveRoute(Pirep $pirep): Pirep
    {
        # Delete all the existing nav points
        Acars::where([
            'pirep_id' => $pirep->id,
            'type' => AcarsType::ROUTE,
        ])->delete();

        # Delete the route
        if (empty($pirep->route)) {
            return $pirep;
        }

        if(!$pirep->dpt_airport) {
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
            ++$point_count;
        }

        return $pirep;
    }

    /**
     * Create a new PIREP with some given fields
     *
     * @param Pirep $pirep
     * @param array [PirepFieldValues] $field_values
     *
     * @return Pirep
     */
    public function create(Pirep $pirep, array $field_values = []): Pirep
    {
        if (empty($field_values)) {
            $field_values = [];
        }

        # Figure out what default state should be. Look at the default
        # behavior from the rank that the pilot is assigned to
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

        $pirep->save();
        $pirep->refresh();

        foreach ($field_values as $fv) {
            $v = new PirepFieldValues();
            $v->pirep_id = $pirep->id;
            $v->name = $fv['name'];
            $v->value = $fv['value'];
            $v->source = $fv['source'];
            $v->save();
        }

        Log::info('New PIREP filed', [$pirep]);
        event(new PirepFiled($pirep));

        # only update the pilot last state if they are accepted
        if ($default_state === PirepState::ACCEPTED) {
            $pirep = $this->accept($pirep);
            $this->setPilotState($pirep->pilot, $pirep);
        }

        return $pirep;
    }

    /**
     * @param Pirep $pirep
     * @param int $new_state
     * @return Pirep
     */
    public function changeState(Pirep $pirep, int $new_state)
    {
        Log::info('PIREP ' . $pirep->id . ' state change from ' . $pirep->state . ' to ' . $new_state);

        if ($pirep->state === $new_state) {
            return $pirep;
        }

        /**
         * Move from a PENDING status into either ACCEPTED or REJECTED
         */
        if ($pirep->state === PirepState::PENDING) {
            if ($new_state === PirepState::ACCEPTED) {
                return $this->accept($pirep);
            } elseif ($new_state === PirepState::REJECTED) {
                return $this->reject($pirep);
            } else {
                return $pirep;
            }
        } /*
         * Move from a ACCEPTED to REJECTED status
         */
        elseif ($pirep->state === PirepState::ACCEPTED) {
            $pirep = $this->reject($pirep);
            return $pirep;
        } /**
         * Move from REJECTED to ACCEPTED
         */
        elseif ($pirep->state === PirepState::REJECTED) {
            $pirep = $this->accept($pirep);
            return $pirep;
        }

        return $pirep->refresh();
    }

    /**
     * @param Pirep $pirep
     * @return Pirep
     */
    public function accept(Pirep $pirep): Pirep
    {
        # moving from a REJECTED state to ACCEPTED, reconcile statuses
        if ($pirep->state === PirepState::ACCEPTED) {
            return $pirep;
        }

        $ft = $pirep->flight_time;
        $pilot = $pirep->pilot;

        $this->pilotSvc->adjustFlightTime($pilot, $ft);
        $this->pilotSvc->adjustFlightCount($pilot, +1);
        $this->pilotSvc->calculatePilotRank($pilot);
        $pirep->pilot->refresh();

        # Change the status
        $pirep->state = PirepState::ACCEPTED;
        $pirep->save();
        $pirep->refresh();

        $this->setPilotState($pilot, $pirep);

        Log::info('PIREP ' . $pirep->id . ' state change to ACCEPTED');

        event(new PirepAccepted($pirep));

        return $pirep;
    }

    /**
     * @param Pirep $pirep
     * @return Pirep
     */
    public function reject(Pirep $pirep): Pirep
    {
        # If this was previously ACCEPTED, then reconcile the flight hours
        # that have already been counted, etc
        if ($pirep->state === PirepState::ACCEPTED) {
            $pilot = $pirep->pilot;
            $ft = $pirep->flight_time * -1;

            $this->pilotSvc->adjustFlightTime($pilot, $ft);
            $this->pilotSvc->adjustFlightCount($pilot, -1);
            $this->pilotSvc->calculatePilotRank($pilot);
            $pirep->pilot->refresh();
        }

        # Change the status
        $pirep->state = PirepState::REJECTED;
        $pirep->save();
        $pirep->refresh();

        Log::info('PIREP ' . $pirep->id . ' state change to REJECTED');

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
}
