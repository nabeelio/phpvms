<?php

namespace App\Interfaces;

/**
 * Class FormRequest
 */
class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    protected $sanitizer;

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
