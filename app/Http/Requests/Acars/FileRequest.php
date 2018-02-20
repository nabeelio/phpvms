<?php

namespace App\Http\Requests\Acars;

use Auth;
use App\Models\Pirep;
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
            'distance'              => 'required|numeric',
            'flight_time'           => 'required|integer',
            'fuel_used'             => 'required|numeric',

            'airline_id'            => 'nullable|exists:airlines,id',
            'aircraft_id'           => 'nullable|exists:aircraft,id',
            'flight_id'             => 'nullable|exists:flights,id',
            'flight_number'         => 'nullable',
            'dpt_airport_id'        => 'nullable',
            'arr_airport_id'        => 'nullable',
            'route_code'            => 'nullable',
            'route_leg'             => 'nullable',
            'planned_distance'      => 'nullable|numeric',
            'planned_flight_time'   => 'nullable|integer',
            'level'                 => 'nullable|numeric',
            'zfw'                   => 'nullable|numeric',
            'block_fuel'            => 'nullable|numeric',
            'route'                 => 'nullable',
            'notes'                 => 'nullable',
            'source_name'           => 'nullable|max:25',
            'landing_rate'          => 'nullable|numeric',
            'flight_type'           => 'nullable|integer',
            'created_at'            => 'nullable|date',
        ];

        return $rules;
    }
}
