<?php

namespace App\Http\Requests;

use App\Contracts\FormRequest;
use App\Models\Aircraft;

class CreateAircraftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = Aircraft::$rules;
        $rules['registration'] .= '|unique:aircraft';
        return $rules;
    }
}
