<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class PirepCancelled
 */
class PirepCancelled extends HttpException
{
    public function __construct(
        string $message = null,
        \Exception $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        parent::__construct(
            400,
            'PIREP has been cancelled, updates are not allowed',
            $previous, $headers, $code
        );
    }
}
