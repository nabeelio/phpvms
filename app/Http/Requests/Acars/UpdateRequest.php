<?php

namespace App\Http\Requests\Acars;

use App\Contracts\FormRequest;
use App\Models\Pirep;
use Auth;

/**
 * Class UpdateRequest
 */
class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        $pirep = Pirep::findOrFail($this->route('pirep_id'), ['user_id']);
        return $pirep->user_id === Auth::id();
    }

    public function rules()
    {
        $rules = [
            'airline_id'          => 'nullable|exists:airlines,id',
            'aircraft_id'         => 'nullable|exists:aircraft,id',
            'flight_number'       => 'nullable',
            'dpt_airport_id'      => 'nullable',
            'arr_airport_id'      => 'nullable',
            'route_code'          => 'nullable',
            'route_leg'           => 'nullable',
            'distance'            => 'nullable|numeric',
            'planned_distance'    => 'nullable|numeric',
            'block_time'          => 'nullable|integer',
            'flight_time'         => 'nullable|integer',
            'flight_type'         => 'nullable',
            'planned_flight_time' => 'nullable|integer',
            'level'               => 'nullable|numeric',
            'zfw'                 => 'nullable|numeric',
            'fuel_used'           => 'nullable|numeric',
            'block_fuel'          => 'nullable|numeric',
            'route'               => 'nullable',
            'notes'               => 'nullable',
            'source_name'         => 'nullable|max:25',
            'landing_rate'        => 'nullable|numeric',
            'block_off_time'      => 'nullable',
            'block_on_time'       => 'nullable',
            'created_at'          => 'nullable',
            'status'              => 'nullable',
            'score'               => 'nullable|integer',

            // See if the fare objects are included and formatted properly
            'fares'         => 'nullable|array',
            'fares.*.id'    => 'required',
            'fares.*.count' => 'required|numeric',
        ];

        return $rules;
    }
}
