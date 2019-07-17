<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use function request;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = User::$rules;

        $user_id = request('id', null);

        // Validate if the pilot ID is already being used or not
        $rules['pilot_id'] = 'required|integer|unique:users,pilot_id,'.$user_id.',id';

        // Add additional rules for when we're modifying
        $rules['email'] .= '|unique:users,email,'.$user_id.',id';

        return $rules;
    }
}
