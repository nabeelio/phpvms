<?php

namespace App\Services;

use App\Models\Subfleet;
use App\Models\Fare;

class FareService extends BaseService {

    /**
     * Attach a fare to an aircraft
     *
     * @param Aircraft $subfleet
     * @param Fare     $fare
     * @param array    set the price/cost/capacity
     *
     * @return Aircraft
     */
    public function setForSubfleet(Subfleet &$subfleet, Fare &$fare, array $override=[])
    {
        $subfleet->fares()->syncWithoutDetaching([$fare->id]);

        # modify any pivot values?
        if(count($override) > 0) {
            $subfleet->fares()->updateExistingPivot($fare->id, $override);
        }

        $subfleet->save();
        $subfleet = $subfleet->fresh();
        return $subfleet;
    }

    /**
     * return all the fares for an aircraft. check the pivot
     * table to see if the price/cost/capacity has been overridden
     * and return the correct amounts.
     * @param Aircraft $subfleet
     * @return Fare[]
     */
    public function getForSubfleet(Subfleet &$subfleet)
    {
        $fares = $subfleet->fares->map(function($fare) {
            if(!is_null($fare->pivot->price)) {
                $fare->price = $fare->pivot->price;
            }

            if(!is_null($fare->pivot->cost)) {
                $fare->cost = $fare->pivot->cost;
            }

            if(!is_null($fare->pivot->capacity)) {
                $fare->capacity = $fare->pivot->capacity;
            }

            return $fare;
        });

        return $fares;
    }

    public function delFromAircraft(Subfleet &$subfleet, Fare &$fare)
    {
        $subfleet->fares()->detach($fare->id);
        $subfleet = $subfleet->fresh();
        return $subfleet;
    }
}
