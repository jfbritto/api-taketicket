<?php

namespace App\Http\Controllers\API;

use App\DTO\CreateEventDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventDetailResource;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizerEventController extends Controller
{
    public function __construct(private readonly EventService $eventService) {}

    public function index(Request $request): JsonResponse
    {
        $organizer = $request->user()->organizer;
        abort_unless($organizer, 403, 'User is not an organizer');

        $events = $organizer->events()->latest()->paginate(15);

        return response()->json(EventResource::collection($events)->response()->getData(true));
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $organizer = $request->user()->organizer;
        abort_unless($organizer, 403, 'User is not an organizer');

        $dto = CreateEventDTO::fromRequest($request->validated());
        $event = $this->eventService->createEvent($organizer, $dto);

        return response()->json(new EventDetailResource($event), 201);
    }

    public function show(Request $request, Event $event): JsonResponse
    {
        $this->authorize('manage', $event);

        return response()->json(new EventDetailResource($event->load('ticketTypes', 'customFields')));
    }

    public function update(UpdateEventRequest $request, Event $event): JsonResponse
    {
        $this->authorize('manage', $event);
        $dto = CreateEventDTO::fromRequest($request->validated());
        $event = $this->eventService->updateEvent($event, $dto);

        return response()->json(new EventDetailResource($event));
    }

    public function publish(Request $request, Event $event): JsonResponse
    {
        $this->authorize('manage', $event);
        $event = $this->eventService->publishEvent($event);

        return response()->json(new EventDetailResource($event));
    }

    public function cancel(Request $request, Event $event): JsonResponse
    {
        $this->authorize('manage', $event);
        $event = $this->eventService->cancelEvent($event);

        return response()->json(new EventDetailResource($event));
    }
}
