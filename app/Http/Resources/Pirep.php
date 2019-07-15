<?php

namespace App\Http\Resources;

use App\Models\Enums\PirepStatus;

class Pirep extends Response
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
        $res = parent::toArray($request);
        $res['ident'] = $this->ident;

        $this->checkUnitFields($res, [
            'distance',
            'fuel_used',
            'planned_distance',
        ]);

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
