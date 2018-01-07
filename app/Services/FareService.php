<?php

namespace App\Services;

use App\Models\Fare;
use App\Models\Flight;
use App\Models\Subfleet;

class FareService extends BaseService
{
    /**
     * Attach a fare to an flight
     *
     * @param Flight $flight
     * @param Fare $fare
     * @param array    set the price/cost/capacity
     * @return Flight
     */
    public function setForFlight(Flight $flight, Fare $fare, array $override = []): Flight
    {
        $flight->fares()->syncWithoutDetaching([$fare->id]);

        # modify any pivot values?
        if (\count($override) > 0) {
            $flight->fares()->updateExistingPivot($fare->id, $override);
        }

        $flight->save();
        $flight->refresh();
        return $flight;
    }

    /**
     * return all the fares for a flight. check the pivot
     * table to see if the price/cost/capacity has been overridden
     * and return the correct amounts.
     * @param Flight $flight
     * @return Fare[]
     */
    public function getForFlight(Flight $flight)
    {
        $fares = $flight->fares->map(function ($fare) {
            if (null !== $fare->pivot->price) {
                $fare->price = $fare->pivot->price;
            }

            if (null !== $fare->pivot->cost) {
                $fare->cost = $fare->pivot->cost;
            }

            if (null !== $fare->pivot->capacity) {
                $fare->capacity = $fare->pivot->capacity;
            }

            return $fare;
        });

        return $fares;
    }

    /**
     * @param Flight $flight
     * @param Fare $fare
     * @return Flight
     */
    public function delFareFromFlight(Flight $flight, Fare $fare)
    {
        $flight->fares()->detach($fare->id);
        $flight->refresh();
        return $flight;
    }

    /**
     * Attach a fare to a subfleet
     *
     * @param Subfleet $subfleet
     * @param Fare     $fare
     * @param array    set the price/cost/capacity
     * @return Subfleet
     */
    public function setForSubfleet(Subfleet $subfleet, Fare $fare, array $override=[]): Subfleet
    {
        $subfleet->fares()->syncWithoutDetaching([$fare->id]);

        # modify any pivot values?
        if(count($override) > 0) {
            $subfleet->fares()->updateExistingPivot($fare->id, $override);
        }

        $subfleet->save();
        $subfleet->refresh();
        return $subfleet;
    }

    /**
     * return all the fares for an aircraft. check the pivot
     * table to see if the price/cost/capacity has been overridden
     * and return the correct amounts.
     * @param Subfleet $subfleet
     * @return Fare[]
     */
    public function getForSubfleet(Subfleet $subfleet)
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

    /**
     * Delete the fare from a subfleet
     * @param Subfleet $subfleet
     * @param Fare $fare
     * @return Subfleet|null|static
     */
    public function delFareFromSubfleet(Subfleet &$subfleet, Fare &$fare)
    {
        $subfleet->fares()->detach($fare->id);
        $subfleet->refresh();
        return $subfleet;
    }
}
