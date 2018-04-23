<?php

namespace App\Http\Requests;

use App\Models\Subfleet;
use Illuminate\Foundation\Http\FormRequest;

class CreateSubfleetRequest extends FormRequest
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
        $rules = Subfleet::$rules;
        $rules['type'] .= '|unique:subfleets';
        return $rules;
    }
}
