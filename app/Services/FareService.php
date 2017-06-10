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
    public function set_for_aircraft(
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
    public function get_for_aircraft(Aircraft &$aircraft)
    {
        $fares = [];
        foreach($aircraft->fares as $fare) {
            if(!is_null($fare->pivot->price)) {
                $fare->price = $fare->pivot->price;
            }

            if(!is_null($fare->pivot->cost)) {
                $fare->cost = $fare->pivot->cost;
            }

            if(!is_null($fare->pivot->capacity)) {
                $fare->capacity = $fare->pivot->capacity;
            }
            array_push($fares, $fare);
        }

        return $fares;
    }

    public function delete_from_aircraft(Aircraft &$aircraft, Fare &$fare)
    {
        $aircraft->fares()->detach($fare->id);
        $aircraft = $aircraft->fresh();
        return $aircraft;
    }
}
