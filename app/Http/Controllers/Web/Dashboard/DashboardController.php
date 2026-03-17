<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\DTO\CreateOrganizerDTO;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Ticket;
use App\Services\OrganizerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        $events = $organizer->events;
        $eventIds = $events->pluck('id');

        $totalEvents = $events->count();
        $totalSales = Order::whereIn('event_id', $eventIds)
            ->where('status', OrderStatus::PAID)
            ->sum('total_amount');
        $totalParticipants = Participant::whereHas('ticket', fn ($q) => $q->whereIn('event_id', $eventIds))->count();
        $totalTickets = Ticket::whereIn('event_id', $eventIds)->count();
        $checkedIn = Ticket::whereIn('event_id', $eventIds)
            ->where('status', TicketStatus::USED)->count();
        $checkinRate = $totalTickets > 0 ? round(($checkedIn / $totalTickets) * 100) : 0;

        $recentOrders = Order::whereIn('event_id', $eventIds)
            ->with('user', 'event')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalEvents', 'totalSales', 'totalParticipants', 'checkinRate', 'recentOrders'
        ));
    }

    public function onboarding(): View
    {
        return view('dashboard.onboarding');
    }

    public function storeOrganizer(Request $request, OrganizerService $organizerService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($request->user()->organizer) {
            return redirect('/dashboard');
        }

        $dto = CreateOrganizerDTO::fromRequest($validated);
        $organizerService->createOrganizer($request->user(), $dto);

        return redirect('/dashboard')->with('success', 'Organizer profile created!');
    }
}
