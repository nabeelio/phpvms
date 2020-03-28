<?php

namespace App\Contracts;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * Authorized by default
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
