<?php

namespace App\Http\Requests\Acars;

use App\Contracts\FormRequest;

class PrefileRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'airline_id'          => 'required|exists:airlines,id',
            'aircraft_id'         => 'required|exists:aircraft,id',
            'flight_number'       => 'required',
            'dpt_airport_id'      => 'required',
            'arr_airport_id'      => 'required',
            'flight_id'           => 'nullable',
            'source_name'         => 'required',
            'alt_airport_id'      => 'nullable',
            'status'              => 'nullable',
            'level'               => 'nullable|numeric',
            'flight_type'         => 'nullable',
            'route_code'          => 'nullable',
            'route_leg'           => 'nullable',
            'distance'            => 'nullable|numeric',
            'block_time'          => 'nullable|integer',
            'flight_time'         => 'nullable|integer',
            'planned_distance'    => 'nullable|numeric',
            'planned_flight_time' => 'nullable|integer',
            'zfw'                 => 'nullable|numeric',
            'block_fuel'          => 'nullable|numeric',
            'route'               => 'nullable',
            'notes'               => 'nullable',
            'score'               => 'nullable|integer',
            'block_off_time'      => 'nullable|date',
            'block_on_time'       => 'nullable|date',
            'created_at'          => 'nullable|date',

            // See if the fare objects are included and formatted properly
            'fares'               => 'nullable|array',
            'fares.*.id'          => 'required',
            'fares.*.count'       => 'required|numeric',
        ];

        return $rules;
    }
}
