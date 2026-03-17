<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderExpiredException extends HttpException
{
    public function __construct()
    {
        parent::__construct(422, 'This order has expired.');
    }
}
