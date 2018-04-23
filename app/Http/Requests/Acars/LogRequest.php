<?php

namespace App\Http\Requests\Acars;

use App\Models\Pirep;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LogRequest
 * @package App\Http\Requests\Acars
 */
class LogRequest extends FormRequest
{
    public function authorize()
    {
        $pirep = Pirep::findOrFail($this->route('pirep_id'), ['user_id']);

        return $pirep->user_id === Auth::id();
    }

    public function rules()
    {
        $rules = [
            'logs'              => 'required|array',
            'logs.*.log'        => 'required',
            'logs.*.lat'        => 'nullable|numeric',
            'logs.*.lon'        => 'nullable|numeric',
            'logs.*.created_at' => 'nullable|date',
        ];

        return $rules;
    }
}
