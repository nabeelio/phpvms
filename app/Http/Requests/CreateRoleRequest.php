<?php

namespace App\Http\Requests;

use App\Contracts\FormRequest;
use App\Models\Role;

class CreateRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return Role::$rules;
    }
}
