<?php

namespace App\Http\Requests;

use App\Contracts\FormRequest;
use App\Models\Subfleet;

class UpdateSubfleetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return Subfleet::$rules;
    }
}
