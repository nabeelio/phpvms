<?php

namespace App\Http\Requests\Acars;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PrefileRequest
 * @package App\Http\Requests\Acars
 */
class PrefileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'airline_id'     => 'required|exists:airlines,id',
            'aircraft_id'    => 'required|exists:aircraft,id',
            'flight_number'  => 'required',
            'dpt_airport_id' => 'required',
            'arr_airport_id' => 'required',
            'source_name'    => 'required|max:25',

            'level'               => 'nullable|numeric',
            'route_code'          => 'nullable',
            'route_leg'           => 'nullable',
            'distance'            => 'nullable|numeric',
            'flight_time'         => 'nullable|integer',
            'planned_distance'    => 'nullable|numeric',
            'planned_flight_time' => 'nullable|integer',
            'zfw'                 => 'nullable|numeric',
            'block_fuel'          => 'nullable|numeric',
            'route'               => 'nullable',
            'notes'               => 'nullable',
            'flight_type'         => 'nullable|integer',
            'created_at'          => 'nullable|date',

            # See if the fare objects are included and formatted properly
            'fares'               => 'nullable|array',
            'fares.*.id'          => 'required',
            'fares.*.count'       => 'required|numeric',
        ];

        return $rules;
    }
}
