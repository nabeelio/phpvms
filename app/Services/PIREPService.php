<?php

namespace App\Services;

use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Pirep;
use App\Models\PirepFieldValues;

use App\Events\PirepAccepted;
use App\Events\PirepFiled;
use App\Events\PirepRejected;
use App\Events\UserStatsChanged;

use App\Repositories\PirepRepository;
use Log;

class PIREPService extends BaseService
{
    protected $pilotSvc, $pirepRepo;

    /**
     * PIREPService constructor.
     * @param UserService $pilotSvc
     * @param PirepRepository $pirepRepo
     */
    public function __construct(
        UserService $pilotSvc,
        PirepRepository $pirepRepo
    ) {
        $this->pilotSvc = $pilotSvc;
        $this->pirepRepo = $pirepRepo;
    }

    /**
     * Create a new PIREP with some given fields
     *
     * @param Pirep $pirep
     * @param array [PirepFieldValues] $field_values
     *
     * @return Pirep
     */
    public function create(Pirep $pirep, array $field_values=[]): Pirep
    {
        if($field_values === null) {
            $field_values = [];
        }

        # Figure out what default state should be. Look at the default
        # behavior from the rank that the pilot is assigned to
        if($pirep->source === PirepSource::ACARS) {
            $default_state = $pirep->pilot->rank->auto_approve_acars;
        } else {
            $default_state = $pirep->pilot->rank->auto_approve_manual;
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

        if ($default_state === PirepState::ACCEPTED) {
            $pirep = $this->accept($pirep);
        }

        # only update the pilot last state if they are accepted
        if ($default_state === PirepState::ACCEPTED) {
            $this->setPilotState($pirep);
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
        Log::info('PIREP ' . $pirep->id . ' state change from '.$pirep->state.' to ' . $new_state);

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
        }

        /*
         * Move from a ACCEPTED to REJECTED status
         */
        elseif ($pirep->state === PirepState::ACCEPTED) {
            $pirep = $this->reject($pirep);
            return $pirep;
        }

        /**
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

        $this->setPilotState($pirep);

        Log::info('PIREP '.$pirep->id.' state change to ACCEPTED');

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
    public function setPilotState(Pirep $pirep)
    {
        $pilot = $pirep->pilot;
        $pilot->refresh();

        $pilot->curr_airport_id = $pirep->arr_airport_id;
        $pilot->last_pirep_id = $pirep->id;
        $pilot->save();

        event(new UserStatsChanged($pilot));
    }
}
