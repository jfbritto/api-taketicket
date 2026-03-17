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

    public function undoCheckin(string $ticketCode, User $user): array
    {
        $ticket = $this->ticketService->validateTicket($ticketCode);

        if (! $ticket) {
            return ['status' => 'invalid'];
        }

        if ($ticket->status !== TicketStatus::USED) {
            return ['status' => 'not_checked_in'];
        }

        $ticket->update([
            'status' => TicketStatus::VALID,
            'checked_in_at' => null,
        ]);

        // Delete the most recent checkin record
        $ticket->checkins()->latest('checked_at')->first()?->delete();

        // Log the undo action for audit trail
        \Illuminate\Support\Facades\Log::info('Check-in undone', [
            'ticket_id' => $ticket->id,
            'ticket_code' => $ticket->ticket_code,
            'undone_by' => $user->id,
            'undone_at' => now()->toIso8601String(),
        ]);

        return ['status' => 'undone'];
    }
}
