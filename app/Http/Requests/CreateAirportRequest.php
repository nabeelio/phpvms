<?php

namespace App\Http\Requests;

use App\Models\Airport;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateAirportRequest
 * @package App\Http\Requests
 */
class CreateAirportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        $rules = Airport::$rules;
        $rules['icao'] .= '|unique:airports';
        return $rules;
    }
}
