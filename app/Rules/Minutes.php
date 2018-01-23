<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class Minutes
 * @package App\Rules
 *
 * Make sure that a given value is an integer, but the custom
 * validation message is what really matters here
 */
class Minutes implements Rule
{
    public function passes($attribute, $value): bool
    {
        return \is_int(filter_var($value, FILTER_VALIDATE_INT));
    }

    public function message(): string
    {
        return ':attribute must be an integer, in minutes';
    }
}
