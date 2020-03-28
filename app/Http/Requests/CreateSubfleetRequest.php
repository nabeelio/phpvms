<?php

namespace App\Http\Requests;

use App\Contracts\FormRequest;
use App\Models\Subfleet;

class CreateSubfleetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = Subfleet::$rules;
        $rules['type'] .= '|unique:subfleets';
        return $rules;
    }
}
