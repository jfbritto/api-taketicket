<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class GlobalParticipantController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        $eventIds = $organizer->events()->pluck('id');
        $events = $organizer->events()->orderBy('start_date', 'desc')->get();

        $participants = Participant::whereHas('ticket', fn ($q) => $q->whereIn('event_id', $eventIds))
            ->with(['ticket.event', 'ticket.ticketType'])
            ->when($request->search, fn ($q, $s) =>
                $q->where(fn ($q) => $q
                    ->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('document', 'like', "%{$s}%")
                )
            )
            ->when($request->event_id, fn ($q, $id) =>
                $q->whereHas('ticket', fn ($q) => $q->where('event_id', $id))
            )
            ->orderBy('name')
            ->paginate(20);

        return view('dashboard.participantes', compact('participants', 'events'));
    }

    public function export(Request $request): StreamedResponse
    {
        $organizer = $request->user()->organizer;
        $eventIds = $organizer->events()->pluck('id');

        $participants = Participant::whereHas('ticket', fn ($q) => $q->whereIn('event_id', $eventIds))
            ->with(['ticket.event', 'ticket.ticketType'])
            ->when($request->search, fn ($q, $s) =>
                $q->where(fn ($q) => $q
                    ->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('document', 'like', "%{$s}%")
                )
            )
            ->when($request->event_id, fn ($q, $id) =>
                $q->whereHas('ticket', fn ($q) => $q->where('event_id', $id))
            )
            ->orderBy('name')
            ->get();

        return response()->stream(function () use ($participants) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nome', 'E-mail', 'Telefone', 'Documento', 'Evento', 'Tipo de Ingresso', 'Check-in']);
            foreach ($participants as $p) {
                $checkin = $p->ticket?->checked_in_at?->format('d/m/Y H:i') ?? '';
                fputcsv($handle, [
                    $p->name,
                    $p->email,
                    $p->phone,
                    $p->document,
                    $p->ticket?->event?->title,
                    $p->ticket?->ticketType?->name,
                    $checkin,
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="participantes.csv"',
        ]);
    }
}
