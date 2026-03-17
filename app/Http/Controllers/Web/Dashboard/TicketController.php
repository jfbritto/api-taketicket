<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $this->authorize('manage', $event);

        $query = $event->tickets()->with('participant', 'ticketType');

        if ($request->filled('search')) {
            $query->where('ticket_code', 'like', '%'.$request->search.'%');
        }

        $tickets = $query->paginate(15);

        return view('dashboard.events.tickets', compact('event', 'tickets'));
    }
}
