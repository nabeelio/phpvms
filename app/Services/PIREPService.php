<?php

namespace App\Services;

use App\Models\Pirep;
use App\Models\PirepFieldValues;

use App\Events\PirepAccepted;
use App\Events\PirepFiled;
use App\Events\PirepRejected;
use App\Events\UserStateChanged;

use App\Repositories\PirepRepository;
use Log;

class PIREPService extends BaseService
{
    protected $pilotSvc, $pirepRepo;

    /**
     * PIREPService constructor.
     * @param PilotService $pilotSvc
     * @param PirepRepository $pirepRepo
     */
    public function __construct(
        PilotService $pilotSvc,
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
    public function create(Pirep &$pirep, array $field_values): Pirep
    {
        if($field_values === null) {
            $field_values = [];
        }

        # Figure out what default state should be. Look at the default
        # behavior from the rank that the pilot is assigned to
        if($pirep->source === config('enums.sources.ACARS')) {
            $default_status = $pirep->pilot->rank->auto_approve_acars;
        } else {
            $default_status = $pirep->pilot->rank->auto_approve_manual;
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

        if ($default_status === config('enums.pirep_status.ACCEPTED')) {
            $pirep = $this->accept($pirep);
        }

        # only update the pilot last state if they are accepted
        if ($default_status === config('enums.pirep_status.ACCEPTED')) {
            $this->setPilotState($pirep);
        }

        return $pirep;
    }

    /**
     * @param Pirep $pirep
     * @param int $new_status
     * @return Pirep
     */
    public function changeStatus(Pirep &$pirep, int $new_status): Pirep
    {
        Log::info('PIREP ' . $pirep->id . ' status change from '.$pirep->status.' to ' . $new_status);

        if ($pirep->status === $new_status) {
            return $pirep;
        }

        /**
         * Move from a PENDING status into either ACCEPTED or REJECTED
         */
        if ($pirep->status === config('enums.pirep_status.PENDING')) {
            if ($new_status === config('enums.pirep_status.ACCEPTED')) {
                return $this->accept($pirep);
            } elseif ($new_status === config('enums.pirep_status.REJECTED')) {
                return $this->reject($pirep);
            } else {
                return $pirep;
            }
        }

        /*
         * Move from a ACCEPTED to REJECTED status
         */
        elseif ($pirep->status === config('enums.pirep_status.ACCEPTED')) {
            $pirep = $this->reject($pirep);
            return $pirep;
        }

        /**
         * Move from REJECTED to ACCEPTED
         */
        elseif ($pirep->status === config('enums.pirep_status.REJECTED')) {
            $pirep = $this->accept($pirep);
            return $pirep;
        }
    }

    /**
     * @param Pirep $pirep
     * @return Pirep
     */
    public function accept(Pirep &$pirep): Pirep
    {
        # moving from a REJECTED state to ACCEPTED, reconcile statuses
        if ($pirep->status === config('enums.pirep_status.ACCEPTED')) {
            return $pirep;
        }

        $ft = $pirep->flight_time;
        $pilot = $pirep->pilot;

        $this->pilotSvc->adjustFlightHours($pilot, $ft);
        $this->pilotSvc->adjustFlightCount($pilot, +1);
        $this->pilotSvc->calculatePilotRank($pilot);
        $pirep->pilot->refresh();

        # Change the status
        $pirep->status = config('enums.pirep_status.ACCEPTED');
        $pirep->save();
        $pirep->refresh();

        $this->setPilotState($pirep);

        Log::info('PIREP '.$pirep->id.' status change to ACCEPTED');

        event(new PirepAccepted($pirep));

        return $pirep;
    }

    /**
     * @param Pirep $pirep
     * @return Pirep
     */
    public function reject(Pirep &$pirep): Pirep
    {
        # If this was previously ACCEPTED, then reconcile the flight hours
        # that have already been counted, etc
        if ($pirep->status === config('enums.pirep_status.ACCEPTED')) {
            $pilot = $pirep->pilot;
            $ft = $pirep->flight_time * -1;

            $this->pilotSvc->adjustFlightHours($pilot, $ft);
            $this->pilotSvc->adjustFlightCount($pilot, -1);
            $this->pilotSvc->calculatePilotRank($pilot);
            $pirep->pilot->refresh();
        }

        # Change the status
        $pirep->status = config('enums.pirep_status.REJECTED');
        $pirep->save();
        $pirep->refresh();

        Log::info('PIREP ' . $pirep->id . ' status change to REJECTED');

        event(new PirepRejected($pirep));

        return $pirep;
    }

    /**
     * @param Pirep $pirep
     */
    public function setPilotState(Pirep &$pirep)
    {
        $pilot = $pirep->pilot;
        $pilot->refresh();

        $pilot->curr_airport_id = $pirep->arr_airport_id;
        $pilot->last_pirep_id = $pirep->id;
        $pilot->save();

        event(new UserStateChanged($pilot));
    }
}
