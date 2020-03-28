<?php

namespace App\Http\Requests;

use App\Contracts\FormRequest;
use App\Models\Airline;

class CreateAirlineRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = Airline::$rules;
        $rules['iata'] .= '|unique:airlines';
        $rules['icao'] .= '|unique:airlines';

        return $rules;
    }
}
