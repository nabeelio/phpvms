<?php

namespace App\Http\Requests\Acars;

use App\Interfaces\FormRequest;

/**
 * Class PrefileRequest
 */
class FieldsRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'fields' => 'required|array',
        ];
    }
}
