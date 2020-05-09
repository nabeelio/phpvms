<?php

namespace App\Contracts;

use Illuminate\Validation\Rule;

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

    /**
     * Set a given column as being unique
     *
     * @param $table
     *
     * @return array
     */
    public function unique($table)
    {
        return [
            Rule::unique($table)->ignore($this->id, 'id'),
        ];
    }
}
