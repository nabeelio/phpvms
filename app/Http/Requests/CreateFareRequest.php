<?php

namespace App\Http\Requests;

use App\Models\Fare;
use Illuminate\Foundation\Http\FormRequest;

class CreateFareRequest extends FormRequest
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
        $rules = Fare::$rules;
        $rules['code'] .= '|unique:fares';
        return $rules;
    }
}
