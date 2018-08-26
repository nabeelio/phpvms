<?php

namespace App\Http\Requests\Acars;

use App\Interfaces\FormRequest;

/**
 * Class FileRequest
 */
class CommentRequest extends FormRequest
{
    public function authorize()
    {
        return true;  # Anyone can comment
    }

    public function rules()
    {
        $rules = [
            'comment'    => 'required',
            'created_at' => 'nullable|date',
        ];

        return $rules;
    }
}
