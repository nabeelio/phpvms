<?php

namespace App\Http\Resources;

use App\Models\Enums\PirepStatus;
use App\Support\Units\Distance;
use App\Support\Units\Fuel;
use Illuminate\Http\Resources\Json\Resource;

class Pirep extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $pirep = parent::toArray($request);

        $pirep['ident'] = $this->ident;

        if ($this->distance instanceof Distance) {
            $pirep['distance'] = $this->distance->units;
        }

        if ($this->fuel_used instanceof Fuel) {
            $pirep['fuel_used'] = $this->fuel_used->units;
        }

        if ($this->planned_distance instanceof Distance) {
            $pirep['planned_distance'] = $this->planned_distance->units;
        }

        /*
         * Relationship fields
         */

        if ($this->block_on_time) {
            $pirep['block_on_time'] = $this->block_on_time->toIso8601ZuluString();
        }

        if ($this->block_off_time) {
            $pirep['block_off_time'] = $this->block_off_time->toIso8601ZuluString();
        }

        $pirep['status_text'] = PirepStatus::label($this->status);

        $pirep['airline'] = new Airline($this->airline);
        $pirep['dpt_airport'] = new Airport($this->dpt_airport);
        $pirep['arr_airport'] = new Airport($this->arr_airport);

        $pirep['position'] = new Acars($this->position);
        $pirep['comments'] = PirepComment::collection($this->comments);
        $pirep['user'] = [
            'id'              => $this->user->id,
            'name'            => $this->user->name,
            'home_airport_id' => $this->user->home_airport_id,
            'curr_airport_id' => $this->user->curr_airport_id,
        ];

        // format to kvp
        $pirep['fields'] = new PirepFieldCollection($this->fields);

        return $pirep;
    }
}
