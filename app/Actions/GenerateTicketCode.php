<?php

namespace App\Actions;

use App\Models\Ticket;
use Illuminate\Support\Str;

class GenerateTicketCode
{
    public function execute(): string
    {
        do {
            $code = 'TKT-' . strtoupper(Str::random(6));
        } while (Ticket::where('ticket_code', $code)->exists());

        return $code;
    }
}
