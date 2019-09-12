<?php

namespace App\Contracts;

/**
 * Class FormRequest
 */
class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
