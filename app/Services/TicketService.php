<?php

namespace App\Services;

use App\Actions\GenerateTicketCode;
use App\Models\Order;
use App\Models\Ticket;

class TicketService
{
    public function __construct(private readonly GenerateTicketCode $generateCode) {}

    public function generateTickets(Order $order): void
    {
        $tickets = Ticket::whereIn('order_item_id', $order->items->pluck('id'))->get();

        foreach ($tickets as $ticket) {
            $code = $this->generateCode->execute();
            $payload = $this->generateQrPayload($ticket);

            $ticket->update([
                'ticket_code' => $code,
                'qr_code_payload' => $payload,
            ]);
        }
    }

    public function generateQrPayload(Ticket $ticket): string
    {
        $hmac = hash_hmac('sha256', (string) $ticket->id, config('app.key'));

        return "{$ticket->id}:{$hmac}";
    }

    public function validateTicket(string $ticketCode): ?Ticket
    {
        return Ticket::where('ticket_code', $ticketCode)->with('participant')->first();
    }

    public function validateQrPayload(string $payload): ?Ticket
    {
        $parts = explode(':', $payload, 2);
        if (count($parts) !== 2) {
            return null;
        }

        [$ticketId, $hmac] = $parts;
        $expectedHmac = hash_hmac('sha256', $ticketId, config('app.key'));

        if (! hash_equals($expectedHmac, $hmac)) {
            return null;
        }

        return Ticket::with('participant')->find($ticketId);
    }
}
