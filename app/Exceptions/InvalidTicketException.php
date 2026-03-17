<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidTicketException extends HttpException
{
    public function __construct(string $message = 'Invalid ticket.')
    {
        parent::__construct(404, $message);
    }
}
