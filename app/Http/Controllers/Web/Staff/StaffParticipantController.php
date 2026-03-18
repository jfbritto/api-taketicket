<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffParticipantController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $query = Participant::query()
            ->whereHas('ticket', fn ($q) => $q->where('event_id', $event->id))
            ->with(['ticket.ticketType']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('participants.name', 'like', "%{$search}%")
                  ->orWhereHas('ticket', fn ($tq) => $tq->where('ticket_code', 'like', "%{$search}%"));
            });
        }

        $participants = $query->paginate(20)->withQueryString();

        return view('staff.participants', compact('event', 'participants'));
    }
}
