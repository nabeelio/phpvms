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
            'dpt_airport_id' => 'required',
            'arr_airport_id' => 'required',
            'flight_id' => 'nullable',
            'flight_number' => 'required',
            'route_code' => 'nullable',
            'route_leg' => 'nullable',
            'distance' => 'nullable|numeric',
            'planned_distance' => 'nullable|numeric',
            'flight_time' => 'nullable|integer',
            'planned_flight_time' => 'nullable|integer',
            'level' => 'required|integer',
            'route' => 'nullable',
            'notes' => 'nullable',
            'created_at' => 'nullable|date',
        ];

        return $rules;
    }
}
