<?php

namespace App\Http\Requests\Acars;

use App\Contracts\FormRequest;
use App\Models\Pirep;
use Illuminate\Support\Facades\Auth;

class LogRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pirep = Pirep::findOrFail($this->route('pirep_id'), ['user_id']);
        return $pirep->user_id === Auth::id();
    }

    public function rules(): array
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
