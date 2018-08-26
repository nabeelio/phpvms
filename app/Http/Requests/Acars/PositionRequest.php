<?php

namespace App\Http\Requests\Acars;

use App\Interfaces\FormRequest;
use App\Models\Pirep;
use Auth;

/**
 * Class PositionRequest
 */
class PositionRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        $pirep = Pirep::findOrFail($this->route('pirep_id'), ['user_id']);
        return $pirep->user_id === Auth::id();
    }

    /**
     * @return array
     */
    /*public function sanitize()
    {
        return [
            'positions.*.sim_time' => Acars::$sanitize['sim_time'],
        ];
    }*/

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            'positions'               => 'required|array',
            'positions.*.lat'         => 'required|numeric',
            'positions.*.lon'         => 'required|numeric',
            'positions.*.status'      => 'nullable',
            'positions.*.altitude'    => 'nullable|numeric',
            'positions.*.heading'     => 'nullable|numeric|between:0,360',
            'positions.*.vs'          => 'nullable',
            'positions.*.gs'          => 'nullable',
            'positions.*.transponder' => 'nullable',
            'positions.*.autopilot'   => 'nullable',
            'positions.*.fuel'        => 'nullable|numeric',
            'positions.*.fuel_flow'   => 'nullable|numeric',
            'positions.*.log'         => 'nullable',
            'positions.*.sim_time'    => 'nullable|date',
            'positions.*.created_at'  => 'nullable|date',
        ];

        return $rules;
    }
}
