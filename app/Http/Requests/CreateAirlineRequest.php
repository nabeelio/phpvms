<?php

namespace App\Http\Requests;

use App\Models\Airline;
use Illuminate\Foundation\Http\FormRequest;

class CreateAirlineRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = Airline::$rules;
        $rules['iata'] .= '|unique:airlines';
        $rules['icao'] .= '|unique:airlines';
        return $rules;
    }
}
