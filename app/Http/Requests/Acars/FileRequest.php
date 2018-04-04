<?php

namespace App\Http\Requests\Acars;

use App\Models\Pirep;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class FileRequest
 * @package App\Http\Requests\Acars
 */
class FileRequest extends FormRequest
{
    public function authorize()
    {
        $pirep = Pirep::findOrFail($this->route('pirep_id'), ['user_id']);

        return $pirep->user_id === Auth::id();
    }

    public function rules()
    {
        $rules = [
            'distance'    => 'required|numeric',
            'flight_time' => 'required|integer',
            'fuel_used'   => 'required|numeric',

            'block_time'          => 'nullable|integer',
            'airline_id'          => 'nullable|exists:airlines,id',
            'aircraft_id'         => 'nullable|exists:aircraft,id',
            'flight_number'       => 'nullable',
            'dpt_airport_id'      => 'nullable',
            'arr_airport_id'      => 'nullable',
            'route_code'          => 'nullable',
            'route_leg'           => 'nullable',
            'planned_distance'    => 'nullable|numeric',
            'planned_flight_time' => 'nullable|integer',
            'level'               => 'nullable|numeric',
            'zfw'                 => 'nullable|numeric',
            'block_fuel'          => 'nullable|numeric',
            'route'               => 'nullable',
            'notes'               => 'nullable',
            'source_name'         => 'nullable',
            'landing_rate'        => 'nullable|numeric',
            'flight_type'         => 'nullable|integer',
            'block_off_time'      => 'nullable|date',
            'block_on_time'       => 'nullable|date',
            'created_at'          => 'nullable|date',

            # See if the fare objects are included and formatted properly
            'fares'               => 'nullable|array',
            'fares.*.id'          => 'required',
            'fares.*.count'       => 'required|numeric',
        ];

        return $rules;
    }
}
