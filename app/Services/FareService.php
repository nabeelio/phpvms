<?php

namespace App\Services;

use App\Contracts\Service;
use App\Models\Fare;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\PirepFare;
use App\Models\Subfleet;
use App\Support\Math;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

use function count;

class FareService extends Service
{
    /**
     * @param Collection[Fare] $subfleet_fares The fare for a subfleet
     * @param Collection[Fare] $flight_fares   The fares on a flight
     *
     * @return Collection[Fare] Collection of Fare
     */
    public function getFareWithOverrides($subfleet_fares, $flight_fares): Collection
    {
        /**
         * Make sure we've got something in terms of fares on the subfleet or the flight
         */
        if (empty($subfleet_fares) && empty($flight_fares)) {
            return collect();
        }

        /**
         * Check to see if there are any subfleet fares. This might only have fares on the
         * flight, no matter how rare that might be
         */
        if ($subfleet_fares === null || count($subfleet_fares) === 0) {
            return $flight_fares->map(function ($fare, $_) {
                return $this->getFareWithPivot($fare, $fare->pivot);
            });
        }

        return $subfleet_fares->map(function ($sf_fare, $_) use ($flight_fares) {
            /**
             * Get the fare, using the subfleet's pivot values. This will return
             * the fares with all the costs, etc, that are overridden for the given subfleet
             */
            $fare = $this->getFareWithPivot($sf_fare, $sf_fare->pivot);

            /**
             * Now, using the fares that have already been used from the subfleet
             * now pass those fares in for the flight to override.
             *
             * First look to see that there actually is an override for that fare that's on
             * the flight
             */
            $flight_fare = $flight_fares->whereStrict('id', $fare->id)->first();
            if ($flight_fare === null) {
                return $fare;
            }

            /**
             * Found an override on the flight for the given fare. Check to see if we
             * have values there that can be used to override or act as a pivot
             */
            $fare = $this->getFareWithPivot($fare, $flight_fare->pivot);

            /**
             * Finally return the fare that we have, it should have gone through the
             * multiple levels of reconciliation that were required
             */
            return $fare;
        });
    }

    /**
     * This will return the flight but all of the subfleets will have the corrected fares with the
     * right amounts based on the pivots, and with the correct "inheritence" for the flights
     *
     * @param Flight $flight
     *
     * @return \App\Models\Flight
     */
    public function getReconciledFaresForFlight(Flight $flight): Flight
    {
        $subfleets = $flight->subfleets;
        $flight_fares = $flight->fares;

        /**
         * @var int      $key
         * @var Subfleet $subfleet
         */
        foreach ($subfleets as $key => $subfleet) {
            $subfleet->fares = $this->getFareWithOverrides($subfleet->fares, $flight_fares);
        }

        $flight->subfleets = $subfleets;
        return $flight;
    }

    /**
     * Get the fares for a particular flight, with the subfleet that is in use being passed in
     *
     * @param Flight|null   $flight
     * @param Subfleet|null $subfleet
     *
     * @return Collection
     */
    public function getAllFares($flight, $subfleet)
    {
        if (!$flight) {
            $flight_fares = collect();
        } else {
            $flight_fares = $flight->fares;
        }

        if (empty($subfleet)) {
            throw new InvalidArgumentException('Subfleet argument missing');
        }

        return $this->getFareWithOverrides($subfleet->fares, $flight_fares);
    }

    /**
     * Get a fare with the proper prices/costs populated in the pivot
     *
     * @param $fare
     *
     * @return mixed
     */
    public function getFares($fare)
    {
        return $this->getFareWithPivot($fare, $fare->pivot);
    }

    /**
     * Get the correct price of something supplied with the correct pivot
     *
     * @param Fare  $fare
     * @param Pivot $pivot
     *
     * @return \App\Models\Fare
     */
    public function getFareWithPivot(Fare $fare, Pivot $pivot)
    {
        if (filled($pivot->price)) {
            if (strpos($pivot->price, '%', -1) !== false) {
                $fare->price = Math::getPercent($fare->price, $pivot->price);
            } else {
                $fare->price = $pivot->price;
            }
        }

        if (filled($pivot->cost)) {
            if (strpos($pivot->cost, '%', -1) !== false) {
                $fare->cost = Math::getPercent($fare->cost, $pivot->cost);
            } else {
                $fare->cost = $pivot->cost;
            }
        }

        if (filled($pivot->capacity)) {
            if (strpos($pivot->capacity, '%', -1) !== false) {
                $fare->capacity = floor(Math::getPercent($fare->capacity, $pivot->capacity));
            } else {
                $fare->capacity = floor($pivot->capacity);
            }
        }

        // $fare->notes = '';
        $fare->active = true;

        return $fare;
    }

    /**
     * Return all the fares for an aircraft. check the pivot
     * table to see if the price/cost/capacity has been overridden
     * and return the correct amounts.
     *
     * @param Subfleet $subfleet
     *
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
     * Attach a fare to an flight
     *
     * @param Flight $flight
     * @param Fare   $fare
     * @param array    set the price/cost/capacity
     *
     * @return Flight
     */
    public function setForFlight(Flight $flight, Fare $fare, array $override = []): Flight
    {
        Log::info('Setting fare "'.$fare->name.'" to flight "'.$flight->ident.'"');

        $flight->fares()->syncWithoutDetaching([$fare->id]);

        // modify any pivot values?
        if (count($override) > 0) {
            $flight->fares()->updateExistingPivot($fare->id, $override);
        }

        $flight->save();
        $flight->refresh();

        return $flight;
    }

    /**
     * @param Flight $flight
     * @param Fare   $fare
     *
     * @return Flight
     */
    public function delFareFromFlight(Flight $flight, Fare $fare)
    {
        Log::info('Removing fare "'.$fare->name.'" to flight "'.$flight->ident.'"');

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
     *
     * @return Subfleet
     */
    public function setForSubfleet(Subfleet $subfleet, Fare $fare, array $override = []): Subfleet
    {
        Log::info('Setting fare "'.$fare->name.'" to subfleet "'.$subfleet->name.'"');

        $subfleet->fares()->syncWithoutDetaching([$fare->id]);

        // modify any pivot values?
        if (count($override) > 0) {
            $subfleet->fares()->updateExistingPivot($fare->id, $override);
        }

        $subfleet->save();
        $subfleet->refresh();

        return $subfleet;
    }

    /**
     * Delete the fare from a subfleet
     *
     * @param Subfleet $subfleet
     * @param Fare     $fare
     *
     * @return Subfleet|null|static
     */
    public function delFareFromSubfleet(Subfleet &$subfleet, Fare &$fare)
    {
        Log::info('Removing fare "'.$fare->name.'" from subfleet "'.$subfleet->name.'"');

        $subfleet->fares()->detach($fare->id);
        $subfleet->refresh();

        return $subfleet;
    }

    /**
     * Get the fares for a PIREP, this just returns the PirepFare
     * model which includes the counts for that particular fare
     *
     * @param Pirep $pirep
     *
     * @return Collection
     */
    public function getForPirep(Pirep $pirep)
    {
        return PirepFare::where('pirep_id', $pirep->id)->get();
    }

    /**
     * Save the list of fares
     *
     * @param Pirep       $pirep
     * @param PirepFare[] $fares
     *
     * @throws \Exception
     */
    public function saveForPirep(Pirep $pirep, array $fares)
    {
        if (!$fares || empty($fares)) {
            return;
        }

        // Remove all the previous fares
        PirepFare::where('pirep_id', $pirep->id)->delete();

        // Add them in
        foreach ($fares as $fare) {
            $fare->pirep_id = $pirep->id;
            Log::info('Saving fare pirep='.$pirep->id.', fare='.$fare['count']);
            $fare->save();
        }
    }
}
