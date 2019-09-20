<?php

namespace App\Http\Resources;

use App\Models\Enums\PirepStatus;
use App\Support\Units\Distance;
use App\Support\Units\Fuel;

class Pirep extends Response
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName
     *
     * @return array
     */
    public function toArray($request)
    {
        $res = parent::toArray($request);
        $res['ident'] = $this->ident;

        // Set these to the response units
        if (!array_key_exists('distance', $res)) {
            $res['distance'] = 0;
        }

        $distance = new Distance($res['distance'], config('phpvms.internal_units.distance'));
        $res['distance'] = $distance->getResponseUnits();

        if (!array_key_exists('fuel_used', $res)) {
            $res['fuel_used'] = 0;
        }

        $fuel_used = new Fuel($res['fuel_used'], config('phpvms.internal_units.fuel'));
        $res['fuel_used'] = $fuel_used->getResponseUnits();

        if (! array_key_exists('planned_distance', $res)) {
            $res['planned_distance'] = 0;
        }

        $planned_dist = new Distance($res['planned_distance'], config('phpvms.internal_units.distance'));
        $res['planned_distance'] = $planned_dist->getResponseUnits();

        /*
         * Relationship fields
         */

        if ($this->block_on_time) {
            $res['block_on_time'] = $this->block_on_time->toIso8601ZuluString();
        }

        if ($this->block_off_time) {
            $res['block_off_time'] = $this->block_off_time->toIso8601ZuluString();
        }

        $res['status_text'] = PirepStatus::label($this->status);

        $res['airline'] = new Airline($this->airline);
        $res['dpt_airport'] = new Airport($this->dpt_airport);
        $res['arr_airport'] = new Airport($this->arr_airport);

        $res['position'] = new Acars($this->position);
        $res['comments'] = PirepComment::collection($this->comments);
        $res['user'] = [
            'id'              => $this->user->id,
            'name'            => $this->user->name,
            'home_airport_id' => $this->user->home_airport_id,
            'curr_airport_id' => $this->user->curr_airport_id,
        ];

        // format to kvp
        $res['fields'] = new PirepFieldCollection($this->fields);

        return $res;
    }
}
