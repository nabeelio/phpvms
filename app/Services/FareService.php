<?php

namespace App\Services;

use App\Interfaces\Service;
use App\Models\Fare;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\PirepFare;
use App\Models\Subfleet;
use App\Support\Math;
use Illuminate\Support\Collection;

/**
 * Class FareService
 * @package App\Services
 */
class FareService extends Service
{
    /**
     * Get the fares for a particular flight, with an optional subfleet
     * This will go through if there are any fares assigned to the flight,
     * and then check the fares assigned on the subfleet, and give the
     * final "authoritative" list of the fares for a flight.
     *
     * If a subfleet is passed in,
     * @param Flight|null   $flight
     * @param Subfleet|null $subfleet
     * @return Collection
     */
    public function getAllFares($flight, $subfleet)
    {
        if (!$flight) {
            $flight_fares = collect();
        } else {
            $flight_fares = $this->getForFlight($flight);
        }

        $subfleet_fares = $this->getForSubfleet($subfleet);

        # Go through all of the fares assigned by the subfleet
        # See if any of the same fares are assigned to the flight
        $fares = $subfleet_fares->map(function ($fare, $idx) use ($flight_fares) {
            $flight_fare = $flight_fares->whereStrict('id', $fare->id)->first();
            if (!$flight_fare) {
                return $fare;
            }

            return $flight_fare;
        });

        return $fares;
    }

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
     * @param Fare   $fare
     * @param array    set the price/cost/capacity
     * @return Flight
     */
    public function setForFlight(Flight $flight, Fare $fare, array $override = []): Flight
    {
        $flight->fares()->syncWithoutDetaching([$fare->id]);

        foreach($override as $key => $item) {
            if(!$item) {
                unset($override[$key]);
            }
        }

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
     * @return Collection
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
     * @param Fare   $fare
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
    public function setForSubfleet(Subfleet $subfleet, Fare $fare, array $override = []): Subfleet
    {
        $subfleet->fares()->syncWithoutDetaching([$fare->id]);

        # modify any pivot values?
        if (count($override) > 0) {
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
     * @return Collection
     */
    public function getForSubfleet(Subfleet $subfleet)
    {
        $fares = $subfleet->fares->map(function ($fare) {
            return $this->getFares($fare);
        });

        return $fares;
    }

    /**
     * Delete the fare from a subfleet
     * @param Subfleet $subfleet
     * @param Fare     $fare
     * @return Subfleet|null|static
     */
    public function delFareFromSubfleet(Subfleet &$subfleet, Fare &$fare)
    {
        $subfleet->fares()->detach($fare->id);
        $subfleet->refresh();

        return $subfleet;
    }

    /**
     * Get the fares for a PIREP, this just returns the PirepFare
     * model which includes the counts for that particular fare
     * @param Pirep $pirep
     * @return Collection
     */
    public function getForPirep(Pirep $pirep)
    {
        $fares = [];
        $found_fares = PirepFare::where('pirep_id', $pirep->id)->get();

        return $found_fares;
    }

    /**
     * Save the list of fares
     * @param Pirep $pirep
     * @param array $fares ['fare_id', 'count']
     * @throws \Exception
     */
    public function saveForPirep(Pirep $pirep, array $fares)
    {
        if (!$fares) {
            return;
        }

        # Remove all the previous fares
        PirepFare::where('pirep_id', $pirep->id)->delete();

        # Add them in
        foreach ($fares as $fare) {
            $fare['pirep_id'] = $pirep->id;
            # other fields: ['fare_id', 'count']

            $field = new PirepFare($fare);
            $field->save();
        }
    }
}
