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
            'airline_id' => 'required|exists:airlines,id',
            'aircraft_id' => 'required|exists:aircraft,id',
            'flight_number' => 'required',
            'level' => 'required|numeric',
            'dpt_airport_id' => 'required',
            'arr_airport_id' => 'required',
            'planned_distance' => 'required|numeric',
            'source_name' => 'required',

            'flight_id' => 'nullable',
            'route_code' => 'nullable',
            'route_leg' => 'nullable',
            'distance' => 'nullable|numeric',
            'flight_time' => 'nullable|integer',
            'planned_flight_time' => 'nullable|integer',
            'route' => 'nullable',
            'notes' => 'nullable',
            'flight_type' => 'nullable|integer',
            'created_at' => 'nullable|date',
        ];

        return $rules;
    }
}
