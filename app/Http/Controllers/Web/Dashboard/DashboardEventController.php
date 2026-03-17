<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\DTO\CreateEventDTO;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardEventController extends Controller
{
    public function __construct(private readonly EventService $eventService) {}

    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        $query = $organizer->events()->withCount(['orders' => fn ($q) => $q->where('status', 'paid')]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->latest()->paginate(15);

        return view('dashboard.events.index', compact('events'));
    }

    public function show(Request $request, Event $event): View
    {
        $organizer = $request->user()->organizer;
        abort_if($event->organizer_id !== $organizer->id, 403);

        $ticketTypes = $event->ticketTypes->map(function ($type) {
            $type->sold_count = $type->orderItems()
                ->whereHas('order', fn ($q) => $q->where('status', OrderStatus::PAID))
                ->sum('quantity');
            return $type;
        });

        $totalSold = $event->tickets()
            ->whereHas('orderItem.order', fn ($q) => $q->where('status', OrderStatus::PAID))
            ->count();

        $totalRevenue = $event->orders()
            ->where('status', OrderStatus::PAID)
            ->sum('organizer_amount');

        $totalCheckins = $event->tickets()
            ->whereNotNull('checked_in_at')
            ->count();

        $totalCapacity = $event->ticketTypes()->sum('quantity');

        $recentOrders = $event->orders()
            ->where('status', OrderStatus::PAID)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.events.show', compact(
            'event',
            'ticketTypes',
            'totalSold',
            'totalRevenue',
            'totalCheckins',
            'totalCapacity',
            'recentOrders'
        ));
    }

    public function create(): View
    {
        return view('dashboard.events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'start_date' => 'required|date|after:now',
            'end_date' => 'nullable|date|after:start_date',
            'banner' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $dto = CreateEventDTO::fromRequest($validated);
        $this->eventService->createEvent($request->user()->organizer, $dto);

        return redirect()->route('dashboard.events')->with('success', 'Evento criado como rascunho.');
    }

    public function edit(Request $request, Event $event): View
    {
        $this->authorize('manage', $event);

        $event->load('ticketTypes', 'customFields');

        return view('dashboard.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'banner' => 'nullable|image|max:2048',
            // Ticket types
            'ticket_types' => 'nullable|array',
            'ticket_types.*.id' => 'nullable|exists:ticket_types,id',
            'ticket_types.*.name' => 'required|string|max:255',
            'ticket_types.*.price' => 'required|numeric|min:0',
            'ticket_types.*.quantity' => 'required|integer|min:1',
            'ticket_types.*.sale_start' => 'required|date',
            'ticket_types.*.sale_end' => 'required|date|after:ticket_types.*.sale_start',
            // Custom fields
            'custom_fields' => 'nullable|array',
            'custom_fields.*.id' => 'nullable|exists:custom_fields,id',
            'custom_fields.*.label' => 'required|string|max:255',
            'custom_fields.*.type' => 'required|in:text,number,select,checkbox',
            'custom_fields.*.required' => 'nullable|boolean',
            'custom_fields.*.options' => 'nullable|string',
            'custom_fields.*.position' => 'nullable|integer',
        ]);

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $dto = CreateEventDTO::fromRequest($validated);
        $this->eventService->updateEvent($event, $dto);

        // Sync ticket types
        $existingIds = [];
        foreach ($validated['ticket_types'] ?? [] as $ttData) {
            if (! empty($ttData['id'])) {
                $tt = TicketType::findOrFail($ttData['id']);
                $tt->update([
                    'name' => $ttData['name'],
                    'price' => $ttData['price'],
                    'quantity' => $ttData['quantity'],
                    'available' => $ttData['quantity'] - ($tt->quantity - $tt->available),
                    'sale_start' => $ttData['sale_start'],
                    'sale_end' => $ttData['sale_end'],
                ]);
                $existingIds[] = $tt->id;
            } else {
                $tt = TicketType::create([
                    'event_id' => $event->id,
                    'name' => $ttData['name'],
                    'price' => $ttData['price'],
                    'quantity' => $ttData['quantity'],
                    'available' => $ttData['quantity'],
                    'sale_start' => $ttData['sale_start'],
                    'sale_end' => $ttData['sale_end'],
                ]);
                $existingIds[] = $tt->id;
            }
        }
        // Delete removed ticket types (only those without sales)
        $event->ticketTypes()->whereNotIn('id', $existingIds)
            ->whereDoesntHave('orderItems')
            ->delete();

        // Sync custom fields
        $existingFieldIds = [];
        foreach ($validated['custom_fields'] ?? [] as $index => $cfData) {
            if (! empty($cfData['id'])) {
                $cf = CustomField::findOrFail($cfData['id']);
                $cf->update([
                    'label' => $cfData['label'],
                    'type' => $cfData['type'],
                    'required' => $cfData['required'] ?? false,
                    'options' => $cfData['type'] === 'select' && ! empty($cfData['options'])
                        ? array_map('trim', explode(',', $cfData['options']))
                        : null,
                    'position' => $cfData['position'] ?? $index,
                ]);
                $existingFieldIds[] = $cf->id;
            } else {
                $cf = CustomField::create([
                    'event_id' => $event->id,
                    'label' => $cfData['label'],
                    'type' => $cfData['type'],
                    'required' => $cfData['required'] ?? false,
                    'options' => $cfData['type'] === 'select' && ! empty($cfData['options'])
                        ? array_map('trim', explode(',', $cfData['options']))
                        : null,
                    'position' => $cfData['position'] ?? $index,
                ]);
                $existingFieldIds[] = $cf->id;
            }
        }
        $event->customFields()->whereNotIn('id', $existingFieldIds)
            ->whereDoesntHave('values')
            ->delete();

        return redirect()->route('dashboard.events.edit', $event)->with('success', 'Evento atualizado com sucesso.');
    }

    public function publish(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        try {
            $this->eventService->publishEvent($event);

            return redirect()->back()->with('success', 'Evento publicado com sucesso!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        try {
            $this->eventService->cancelEvent($event);

            return redirect()->back()->with('success', 'Evento cancelado.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
