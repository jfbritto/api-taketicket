<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketTypeRequest;
use App\Http\Requests\UpdateTicketTypeRequest;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\JsonResponse;

class TicketTypeController extends Controller
{
    public function store(StoreTicketTypeRequest $request, Event $event): JsonResponse
    {
        $this->authorize('manage', $event);

        $ticketType = $event->ticketTypes()->create(array_merge(
            $request->validated(),
            ['available' => $request->quantity]
        ));

        return response()->json($ticketType, 201);
    }

    public function update(UpdateTicketTypeRequest $request, Event $event, TicketType $ticketType): JsonResponse
    {
        $this->authorize('manage', $event);
        abort_unless($ticketType->event_id === $event->id, 404);

        $ticketType->update($request->validated());

        return response()->json($ticketType->fresh());
    }

    public function destroy(Event $event, TicketType $ticketType): JsonResponse
    {
        $this->authorize('manage', $event);
        abort_unless($ticketType->event_id === $event->id, 404);
        abort_if($ticketType->orderItems()->exists(), 422, 'Cannot delete ticket type with sales');

        $ticketType->delete();

        return response()->json(null, 204);
    }
}
