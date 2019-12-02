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
            'dpt_airport_id'      => 'required|size:4',
            'arr_airport_id'      => 'required|size:4',
            'flight_id'           => 'sometimes|exists:flights,id',
            'source_name'         => 'required',
            'alt_airport_id'      => 'sometimes|size:4',
            'status'              => 'sometimes',
            'level'               => 'nullable|numeric',
            'flight_type'         => 'sometimes',
            'route_code'          => 'sometimes',
            'route_leg'           => 'sometimes',
            'distance'            => 'sometimes|numeric',
            'block_time'          => 'sometimes|integer',
            'flight_time'         => 'sometimes|integer',
            'planned_distance'    => 'sometimes|numeric',
            'planned_flight_time' => 'sometimes|integer',
            'zfw'                 => 'sometimes|numeric',
            'block_fuel'          => 'sometimes|numeric',
            'route'               => 'nullable',
            'notes'               => 'nullable',
            'score'               => 'sometimes|integer',
            'block_off_time'      => 'sometimes|date',
            'block_on_time'       => 'sometimes|date',
            'created_at'          => 'sometimes|date',

            // See if the fare objects are included and formatted properly
            'fares'         => 'nullable|array',
            'fares.*.id'    => 'required',
            'fares.*.count' => 'required|numeric',
        ];

        return $rules;
    }
}
