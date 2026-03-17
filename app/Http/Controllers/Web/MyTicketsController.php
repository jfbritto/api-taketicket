<?php

namespace App\Http\Controllers\Web;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyTicketsController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::whereHas('orderItem.order', fn ($q) => $q->where('user_id', $request->user()->id))
            ->where('status', '!=', TicketStatus::CANCELLED)
            ->with(['participant', 'event', 'ticketType'])
            ->get()
            ->groupBy('event_id');

        return view('my-tickets.index', compact('tickets'));
    }

    public function show(Request $request, Ticket $ticket): View
    {
        abort_unless($ticket->orderItem->order->user_id === $request->user()->id, 403);

        $ticket->load('participant', 'event', 'ticketType');

        return view('my-tickets.show', compact('ticket'));
    }
}
