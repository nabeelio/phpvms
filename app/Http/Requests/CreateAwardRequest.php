<?php

namespace App\Http\Requests;

use App\Contracts\FormRequest;
use App\Models\Award;

class CreateAwardRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return Award::$rules;
    }
}
