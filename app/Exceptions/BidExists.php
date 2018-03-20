<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class BidExists
 * @package App\Exceptions
 */
class BidExists extends HttpException
{
    public function __construct(
        string $message = null,
        \Exception $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        parent::__construct(
            409,
            'A bid already exists for this flight',
            $previous, $headers, $code
        );
    }
}
