<?php

namespace App\Http\Requests;

use App\Models\Pirep;
use App\Repositories\PirepFieldRepository;
use Illuminate\Foundation\Http\FormRequest;
use Log;

class UpdatePirepRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Don't run validations if it's just being saved
        $action = strtolower(request('submit', 'submit'));
        if ($action === 'save' || $action === 'cancel' || $action === 'delete') {
            return [
                'airline_id'     => 'required|exists:airlines,id',
                'flight_number'  => 'required',
                'dpt_airport_id' => 'required',
                'arr_airport_id' => 'required',
            ];
        }

        $field_rules = Pirep::$rules;

        $field_rules['hours'] = 'nullable|integer';
        $field_rules['minutes'] = 'nullable|integer';

        # Add the validation rules for the custom fields
        $pirepFieldRepo = app(PirepFieldRepository::class);

        $custom_fields = $pirepFieldRepo->all();
        foreach ($custom_fields as $field) {
            Log::info('field:', $field->toArray());
            $field_rules[$field->slug] = $field->required ? 'required' : 'nullable';
        }

        return $field_rules;
    }
}
