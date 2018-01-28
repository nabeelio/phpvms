<?php

namespace App\Http\Requests\Acars;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class FileRequest
 * @package App\Http\Requests\Acars
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
            'comment' => 'required',
            'created_at' => 'nullable|date',
        ];

        return $rules;
    }
}
