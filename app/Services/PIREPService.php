<?php

namespace App\Services;

use App\Models\Pirep;
use App\Models\PirepFieldValues;


class PIREPService extends BaseService
{
    protected $pilotSvc;

    /**
     * return a PIREP model
     */
    public function __construct()
    {
        $this->pilotSvc = app('App\Services\PilotService');
    }

    /**
     * Create a new PIREP with some given fields
     *
     * @param Pirep $pirep
     * @param array [PirepFieldValues] $field_values
     *
     * @return Pirep
     */
    public function create(
        Pirep $pirep,
        array $field_values=[]
    ): Pirep {

        # Figure out what default state should be. Look at the default
        # behavior from the rank that the pilot is assigned to
        if($pirep->source == \VMSEnums::$sources['ACARS']) {
            $default_status = $pirep->pilot->rank->auto_approve_acars;
        } else {
            $default_status = $pirep->pilot->rank->auto_approve_manual;
        }

        if ($default_status == \VMSEnums::$pirep_status['ACCEPTED']) {
            $pirep = $this->accept($pirep);
        }

        foreach ($field_values as $fv) {
            $v = new PirepFieldValues();
            $v->name = $fv['name'];
            $v->value = $fv['value'];
            $v->source = $fv['source'];
            $v->save();
        }

        # TODO: Financials even if it's rejected, log the expenses

        $pirep->save();

        # update pilot information
        $pilot = $pirep->pilot;
        $pilot->refresh();

        $pilot->curr_airport_id = $pirep->arr_airport_id;
        $pilot->last_pirep_id = $pirep->id;
        $pilot->save();

        return $pirep;
    }

    public function changeStatus(Pirep &$pirep, int $new_status): Pirep
    {
        if ($pirep->status === $new_status) {
            return $pirep;
        }

        /**
         * Move from a PENDING status into either ACCEPTED or REJECTED
         */
        if ($pirep->status == \VMSEnums::$pirep_status['PENDING']) {
            if ($new_status == \VMSEnums::$pirep_status['ACCEPTED']) {
                return $this->accept($pirep);
            } elseif ($new_status == \VMSEnums::$pirep_status['REJECTED']) {
                return $this->reject($pirep);
            } else {
                return $pirep;
            }
        }

        /*
         * Move from a ACCEPTED to REJECTED status
         */
        elseif ($pirep->status == \VMSEnums::$pirep_status['ACCEPTED']) {
            $pirep = $this->reject($pirep);
            return $pirep;
        }

        /**
         * Move from REJECTED to ACCEPTED
         */
        elseif ($pirep->status == \VMSEnums::$pirep_status['REJECTED']) {
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
        if ($pirep->status == \VMSEnums::$pirep_status['ACCEPTED']) {
            return $pirep;
        }

        $ft = $pirep->flight_time;
        $pilot = $pirep->pilot;

        $this->pilotSvc->adjustFlightHours($pilot, $ft);
        $this->pilotSvc->adjustFlightCount($pilot, +1);
        $this->pilotSvc->calculatePilotRank($pilot);
        $pirep->pilot->refresh();

        # Change the status
        $pirep->status = \VMSEnums::$pirep_status['ACCEPTED'];
        $pirep->save();

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
        if ($pirep->status == \VMSEnums::$pirep_status['ACCEPTED']) {
            $pilot = $pirep->pilot;
            $ft = $pirep->flight_time * -1;

            $this->pilotSvc->adjustFlightHours($pilot, $ft);
            $this->pilotSvc->adjustFlightCount($pilot, -1);
            $this->pilotSvc->calculatePilotRank($pilot);
            $pirep->pilot->refresh();
        }

        # Change the status
        $pirep->status = \VMSEnums::$pirep_status['REJECTED'];
        $pirep->save();

        return $pirep;
    }
}
