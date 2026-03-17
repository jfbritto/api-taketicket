<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InsufficientStockException extends HttpException
{
    public function __construct(string $ticketTypeName = '')
    {
        parent::__construct(422, "Insufficient stock for ticket type: {$ticketTypeName}");
    }
}
