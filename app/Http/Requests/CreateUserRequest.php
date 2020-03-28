<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'name'            => 'required',
            'email'           => 'required|email|unique:users,email',
            'airline_id'      => 'required',
            'home_airport_id' => 'required',
            'password'        => 'required|confirmed',
            'timezone'        => 'required',
            'country'         => 'required',
            'transfer_time'   => 'sometimes|integer|min:0',
            'toc_accepted'    => 'accepted',
        ];

        if (config('captcha.enabled')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        return $rules;
    }
}
