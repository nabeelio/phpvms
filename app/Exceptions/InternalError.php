<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;
use Validator;
use Log;

/**
 * Show an internal error, bug piggyback off of the validation
 * exception type - this has a place to show up in the UI as a
 * flash message.
 * @package App\Exceptions
 */
class InternalError extends ValidationException
{
    protected const FLASH_FIELD_NAME = 'internal_error_message';

    /**
     * InternalError constructor.
     * @param string|null $message
     * @param null        $field
     */
    final public function __construct(string $message = null, $field = null)
    {
        Log::error($message);
        $validator = Validator::make([], []);
        $validator->errors()->add($field ?? static::FLASH_FIELD_NAME, $message);

        parent::__construct($validator);
    }
}
