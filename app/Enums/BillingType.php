<?php

namespace App\Enums;

enum BillingType: string
{
    case PIX = 'PIX';
    case CREDIT_CARD = 'CREDIT_CARD';
}
