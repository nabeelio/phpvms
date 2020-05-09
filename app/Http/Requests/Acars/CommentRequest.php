<?php

namespace App\Http\Requests\Acars;

use App\Contracts\FormRequest;

class CommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment'    => 'required',
            'created_at' => 'sometimes|date',
        ];
    }
}
