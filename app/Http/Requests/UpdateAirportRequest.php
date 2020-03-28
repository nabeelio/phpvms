<?php

namespace App\Http\Requests;

use App\Contracts\FormRequest;
use App\Models\Airport;

class UpdateAirportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return Airport::$rules;
    }
}
