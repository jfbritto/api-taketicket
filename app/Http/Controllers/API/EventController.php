<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Resources\EventDetailResource;
use App\Repositories\EventRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(private readonly EventRepository $eventRepository) {}

    public function index(Request $request): JsonResponse
    {
        $events = $this->eventRepository->listPublished($request->only('city', 'state', 'date'));
        return response()->json(EventResource::collection($events)->response()->getData(true));
    }

    public function show(string $slug): JsonResponse
    {
        $event = $this->eventRepository->findBySlug($slug);
        abort_unless($event, 404, 'Event not found');
        return response()->json(new EventDetailResource($event));
    }
}
