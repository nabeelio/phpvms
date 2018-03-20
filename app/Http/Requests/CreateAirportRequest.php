<?php

namespace App\Http\Requests;

use App\Models\Airport;
use Illuminate\Foundation\Http\FormRequest;

class CreateAirportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = Airport::$rules;
        $rules['icao'] .= '|unique:airports';

        return $rules;
    }
}
