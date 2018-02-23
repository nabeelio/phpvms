<?php

namespace App\Services;

use App\Models\Fare;
use App\Models\Flight;
use App\Models\Subfleet;
use App\Support\Math;

class FareService extends BaseService
{
    /**
     * Get fares
     * @param $fare
     * @return mixed
     */
    protected function getFares($fare)
    {
        if (filled($fare->pivot->price)) {
            if (substr_count($fare->pivot->price, '%', -1)) {
                $fare->price = Math::addPercent($fare->price, $fare->pivot->price);
            } else {
                $fare->price = $fare->pivot->price;
            }
        }

        if (filled($fare->pivot->cost)) {
            if (substr_count($fare->pivot->cost, '%', -1)) {
                $fare->cost = Math::addPercent($fare->cost, $fare->pivot->cost);
            } else {
                $fare->cost = $fare->pivot->cost;
            }
        }

        if (filled($fare->pivot->capacity)) {
            if (substr_count($fare->pivot->capacity, '%', -1)) {
                $fare->capacity = Math::addPercent($fare->capacity, $fare->pivot->capacity);
            } else {
                $fare->capacity = $fare->pivot->capacity;
            }
        }

        return $fare;
    }

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
            return $this->getFares($fare);
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
            return $this->getFares($fare);
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
