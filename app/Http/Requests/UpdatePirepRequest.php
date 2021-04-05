<?php

namespace App\Http\Requests;

use App\Contracts\FormRequest;
use App\Models\Pirep;
use App\Repositories\PirepFieldRepository;
use Illuminate\Support\Facades\Log;

class UpdatePirepRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
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

        $field_rules['hours'] = 'required|integer';
        $field_rules['minutes'] = 'required|integer';

        // Add the validation rules for the custom fields
        $pirepFieldRepo = app(PirepFieldRepository::class);

        $custom_fields = $pirepFieldRepo->all();
        foreach ($custom_fields as $field) {
            Log::info('field:', $field->toArray());
            $field_rules[$field->slug] = $field->required ? 'required' : 'nullable';
        }

        return $field_rules;
    }
}
