<?php

namespace App\Services;

use App\Models\Aircraft;
use App\Models\Fare;

class FareService extends BaseService {

    /**
     * Attach a fare to an aircraft
     *
     * @param Aircraft $aircraft
     * @param Fare     $fare
     * @param array    set the price/cost/capacity
     *
     * @return Aircraft
     */
    public function setForAircraft(
        Aircraft &$aircraft,
        Fare &$fare,
        array $override=[]
    ) {
        $aircraft->fares()->syncWithoutDetaching([$fare->id]);

        # modify any pivot values?
        if(count($override) > 0) {
            $aircraft->fares()->updateExistingPivot($fare->id, $override);
        }

        $aircraft->save();
        $aircraft = $aircraft->fresh();
        return $aircraft;
    }

    /**
     * return all the fares for an aircraft. check the pivot
     * table to see if the price/cost/capacity has been overridden
     * and return the correct amounts.
     * @param Aircraft $aircraft
     * @return Fare[]
     */
    public function getForAircraft(Aircraft &$aircraft)
    {
        $fares = $aircraft->fares->map(function($fare) {
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

    public function delFromAircraft(Aircraft &$aircraft, Fare &$fare)
    {
        $aircraft->fares()->detach($fare->id);
        $aircraft = $aircraft->fresh();
        return $aircraft;
    }
}
