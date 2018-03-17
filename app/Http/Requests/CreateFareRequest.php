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
        return Fare::$rules;
    }
}
