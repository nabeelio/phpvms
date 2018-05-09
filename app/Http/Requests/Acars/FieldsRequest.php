<?php

namespace App\Http\Requests\Acars;

use App\Interfaces\FormRequest;

/**
 * Class PrefileRequest
 * @package App\Http\Requests\Acars
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
