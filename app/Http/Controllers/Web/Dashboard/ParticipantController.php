<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipantController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $this->authorize('manage', $event);

        $query = Participant::whereHas('ticket', fn ($q) => $q->where('event_id', $event->id))
            ->with('ticket.ticketType');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%");
            });
        }

        $participants = $query->paginate(15);

        return view('dashboard.events.participants', compact('event', 'participants'));
    }

    public function export(Request $request, Event $event): StreamedResponse
    {
        $this->authorize('manage', $event);

        $query = Participant::whereHas('ticket', fn ($q) => $q->where('event_id', $event->id))
            ->with('ticket.ticketType');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%");
            });
        }

        $participants = $query->get();

        return response()->streamDownload(function () use ($participants) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Phone', 'Document', 'Ticket Type', 'Ticket Status', 'Check-in']);
            foreach ($participants as $p) {
                fputcsv($handle, [
                    $p->name,
                    $p->email,
                    $p->phone,
                    $p->document,
                    $p->ticket->ticketType->name ?? '',
                    $p->ticket->status->value ?? '',
                    $p->ticket->checked_in_at?->format('d/m/Y H:i') ?? '',
                ]);
            }
            fclose($handle);
        }, "participants-{$event->slug}.csv", ['Content-Type' => 'text/csv']);
    }
}
