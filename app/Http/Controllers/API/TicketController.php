<?php

namespace App\Http\Controllers\API;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function myTickets(Request $request): JsonResponse
    {
        $tickets = Ticket::whereHas('orderItem.order', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->where('status', '!=', TicketStatus::CANCELLED)
            ->with('participant', 'event', 'ticketType')
            ->latest()
            ->paginate(15);

        return response()->json(TicketResource::collection($tickets)->response()->getData(true));
    }

    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        abort_unless(
            $ticket->orderItem && $ticket->orderItem->order && $ticket->orderItem->order->user_id === $request->user()->id,
            403
        );

        $ticket->load('participant', 'event', 'ticketType');

        return response()->json(new TicketResource($ticket));
    }
}
