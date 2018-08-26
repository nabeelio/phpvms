<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

/**
 * Validate that the files are imported
 */
class ImportRequest extends FormRequest
{
    public static $rules = [
        'csv_file' => 'required|file',
    ];

    /**
     * @param Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function validate(Request $request)
    {
        \Validator::make($request->all(), static::$rules)->validate();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return static::$rules;
    }
}
