<?php

namespace App\Http\Requests\Acars;

use App\Contracts\FormRequest;
use App\Models\Pirep;
use Illuminate\Support\Facades\Auth;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pirep = Pirep::findOrFail($this->route('pirep_id'), ['user_id']);
        return $pirep->user_id === Auth::id();
    }

    public function rules(): array
    {
        $rules = [
            'events'              => 'required|array',
            'events.*.event'      => 'required',
            'events.*.lat'        => 'nullable|numeric',
            'events.*.lon'        => 'nullable|numeric',
            'events.*.created_at' => 'nullable|date',
        ];

        return $rules;
    }
}
