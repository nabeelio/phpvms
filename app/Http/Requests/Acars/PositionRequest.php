<?php

namespace App\Http\Requests\Acars;

use App\Models\Pirep;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PositionRequest
 * @package App\Http\Requests\Acars
 */
class PositionRequest extends FormRequest
{
    public function authorize()
    {
        $pirep = Pirep::findOrFail($this->route('pirep_id'), ['user_id']);

        return $pirep->user_id === Auth::id();
    }

    public function rules()
    {
        $rules = [
            'positions'               => 'required|array',
            'positions.*.lat'         => 'required|numeric',
            'positions.*.lon'         => 'required|numeric',
            'positions.*.altitude'    => 'nullable|numeric',
            'positions.*.heading'     => 'nullable|numeric|between:0,360',
            'positions.*.vs'          => 'nullable',
            'positions.*.gs'          => 'nullable',
            'positions.*.transponder' => 'nullable',
            'positions.*.autopilot'   => 'nullable',
            'positions.*.fuel'        => 'nullable|numeric',
            'positions.*.fuel_flow'   => 'nullable|numeric',
            'positions.*.log'         => 'nullable',
            'positions.*.created_at'  => 'nullable|date',
        ];

        return $rules;
    }
}
