<?php

namespace App\Http\Requests;

use Log;
use Illuminate\Foundation\Http\FormRequest;

use App\Models\Pirep;
use App\Repositories\PirepFieldRepository;

class CreatePirepRequest extends FormRequest
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

        Log::debug('createPirepFormRequest::rules', $field_rules);

        return $field_rules;
    }
}
