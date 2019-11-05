<?php

namespace App\Http\Requests\Acars;

use App\Contracts\FormRequest;

class CommentRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'comment'    => 'required',
            'created_at' => 'nullable|date',
        ];

        return $rules;
    }
}
