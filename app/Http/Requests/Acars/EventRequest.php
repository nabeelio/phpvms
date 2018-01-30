<?php

namespace App\Http\Requests\Acars;

use Auth;
use App\Models\Pirep;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EventRequest
 * @package App\Http\Requests\Acars
 */
class EventRequest extends FormRequest
{
    public function authorize()
    {
        $pirep = Pirep::findOrFail($this->route('pirep_id'), ['user_id']);
        return $pirep->user_id === Auth::id();
    }

    public function rules()
    {
        $rules = [
            'events' => 'required|array',
            'events.*.event' => 'required',
            'events.*.lat' => 'nullable|numeric',
            'events.*.lon' => 'nullable|numeric',
            'events.*.created_at' => 'nullable|date',
        ];

        return $rules;
    }
}
