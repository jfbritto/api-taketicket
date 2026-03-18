<?php

namespace App\Http\Controllers\Web\Staff;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\CheckinService;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffCheckinController extends Controller
{
    public function __construct(
        private readonly CheckinService $checkinService,
        private readonly TicketService $ticketService
    ) {}

    public function index(Request $request, Event $event): View
    {
        $total = $event->ticketTypes()->sum('quantity');
        $checkedIn = $event->tickets()->where('status', TicketStatus::USED)->count();

        return view('staff.checkin', compact('event', 'total', 'checkedIn'));
    }

    public function validateTicket(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'ticket_code' => 'required_without:qr_code_payload|string',
            'qr_code_payload' => 'required_without:ticket_code|string',
        ]);

        $isQrPayload = isset($validated['qr_code_payload']);
        $input = $isQrPayload ? $validated['qr_code_payload'] : $validated['ticket_code'];

        // Step 1: look up ticket via TicketService to get the model instance
        $ticket = $isQrPayload
            ? $this->ticketService->validateQrPayload($input)
            : $this->ticketService->validateTicket($input);

        // Step 2: null means not found
        if (! $ticket) {
            return response()->json(['status' => 'invalid'], 404);
        }

        // Step 3: verify ticket belongs to this event
        if ($ticket->event_id !== $event->id) {
            return response()->json(['status' => 'invalid'], 404);
        }

        // Step 4: perform check-in via service
        $result = $this->checkinService->performCheckin($input, $request->user(), $isQrPayload);

        // Step 5: build JSON response with ticket_code from retained $ticket
        if ($result['status'] === 'valid') {
            return response()->json([
                'status' => 'valid',
                'participant' => [
                    'name' => $result['participant']->name,
                    'ticket_code' => $ticket->ticket_code,
                ],
            ]);
        }

        if ($result['status'] === 'already_used') {
            return response()->json([
                'status' => 'already_used',
                'checked_in_at' => $result['checked_in_at'],
            ], 409);
        }

        return response()->json(['status' => 'invalid'], 404);
    }

    public function undo(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'ticket_code' => 'required|string',
        ]);

        $ticket = $this->ticketService->validateTicket($validated['ticket_code']);

        if (! $ticket || $ticket->event_id !== $event->id) {
            return response()->json(['status' => 'invalid'], 404);
        }

        $result = $this->checkinService->undoCheckin($validated['ticket_code'], $request->user());

        return response()->json($result, $result['status'] === 'undone' ? 200 : 404);
    }
}
