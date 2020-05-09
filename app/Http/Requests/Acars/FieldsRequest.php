<?php

namespace App\Http\Requests\Acars;

use App\Contracts\FormRequest;

class FieldsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fields' => 'required|array',
        ];
    }
}
