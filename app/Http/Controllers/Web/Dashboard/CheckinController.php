<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Enums\EventStatus;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\CheckinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckinController extends Controller
{
    public function __construct(private readonly CheckinService $checkinService) {}

    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        $events = $organizer->events()
            ->where('status', EventStatus::PUBLISHED)
            ->withCount(['tickets', 'tickets as checked_in_count' => fn ($q) => $q->where('status', TicketStatus::USED)])
            ->get();

        return view('dashboard.checkin', compact('events'));
    }

    public function validateTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ticket_code' => 'required_without:qr_code_payload|string',
            'qr_code_payload' => 'required_without:ticket_code|string',
        ]);

        $isQr = isset($validated['qr_code_payload']);
        $input = $isQr ? $validated['qr_code_payload'] : $validated['ticket_code'];

        // Verify ticket belongs to organizer's events before check-in
        $organizer = $request->user()->organizer;
        $ticket = Ticket::where('ticket_code', $isQr ? null : $input)
            ->orWhere('qr_code_payload', $isQr ? $input : null)
            ->first();

        if ($ticket && ! $organizer->events()->where('id', $ticket->event_id)->exists()) {
            return response()->json(['status' => 'unauthorized', 'message' => 'This ticket does not belong to your events.'], 403);
        }

        $result = $this->checkinService->performCheckin($input, $request->user(), $isQr);

        $statusCode = match ($result['status']) {
            'valid' => 200,
            'already_used' => 409,
            default => 404,
        };

        return response()->json($result, $statusCode);
    }

    public function undo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ticket_code' => 'required|string',
        ]);

        // Verify ticket belongs to organizer's events before undo
        $organizer = $request->user()->organizer;
        $ticket = Ticket::where('ticket_code', $validated['ticket_code'])->first();

        if ($ticket && ! $organizer->events()->where('id', $ticket->event_id)->exists()) {
            return response()->json(['status' => 'unauthorized', 'message' => 'This ticket does not belong to your events.'], 403);
        }

        $result = $this->checkinService->undoCheckin($validated['ticket_code'], $request->user());

        return response()->json($result, $result['status'] === 'undone' ? 200 : 404);
    }
}
