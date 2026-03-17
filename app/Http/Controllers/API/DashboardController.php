<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardSummaryResource;
use App\Http\Resources\OrderResource;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardController extends Controller
{
    public function orders(Request $request, Event $event): AnonymousResourceCollection
    {
        $this->authorize('manage', $event);

        $orders = $event->orders()
            ->with(['user', 'items', 'payment'])
            ->latest()
            ->paginate(15);

        return OrderResource::collection($orders);
    }

    public function participants(Request $request, Event $event): AnonymousResourceCollection
    {
        $this->authorize('manage', $event);

        $participants = Participant::whereHas('ticket', fn ($q) => $q->where('event_id', $event->id))
            ->with(['ticket.ticketType', 'fieldValues.customField'])
            ->paginate(15);

        return JsonResource::collection($participants);
    }

    public function tickets(Request $request, Event $event): AnonymousResourceCollection
    {
        $this->authorize('manage', $event);

        $tickets = $event->tickets()
            ->with(['participant', 'ticketType'])
            ->paginate(15);

        return JsonResource::collection($tickets);
    }

    public function summary(Request $request, Event $event): DashboardSummaryResource
    {
        $this->authorize('manage', $event);

        return new DashboardSummaryResource($event);
    }
}
