<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Checkin;
use App\Models\User;

class CheckinService
{
    public function __construct(private readonly TicketService $ticketService) {}

    public function performCheckin(string $input, User $user, bool $isQrPayload = false, ?string $device = null): array
    {
        $ticket = $isQrPayload
            ? $this->ticketService->validateQrPayload($input)
            : $this->ticketService->validateTicket($input);

        if (! $ticket) {
            return ['status' => 'invalid'];
        }

        if ($ticket->status === TicketStatus::CANCELLED) {
            return ['status' => 'invalid'];
        }

        if ($ticket->status === TicketStatus::USED) {
            return [
                'status' => 'already_used',
                'checked_in_at' => $ticket->checked_in_at,
            ];
        }

        $ticket->update([
            'status' => TicketStatus::USED,
            'checked_in_at' => now(),
        ]);

        Checkin::create([
            'ticket_id' => $ticket->id,
            'checked_by' => $user->id,
            'device' => $device,
            'checked_at' => now(),
        ]);

        $ticket->load('participant');

        return [
            'status' => 'valid',
            'participant' => $ticket->participant,
        ];
    }
}
