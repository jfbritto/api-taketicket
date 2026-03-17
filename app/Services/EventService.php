<?php

namespace App\Services;

use App\DTO\CreateEventDTO;
use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Organizer;

class EventService
{
    public function __construct(private readonly OrganizerService $organizerService) {}

    public function createEvent(Organizer $organizer, CreateEventDTO $dto): Event
    {
        return Event::create([
            'organizer_id' => $organizer->id,
            'title' => $dto->title,
            'description' => $dto->description,
            'location' => $dto->location,
            'address' => $dto->address,
            'city' => $dto->city,
            'state' => $dto->state,
            'start_date' => $dto->startDate,
            'end_date' => $dto->endDate,
            'banner' => $dto->banner,
            'status' => EventStatus::DRAFT,
        ]);
    }

    public function updateEvent(Event $event, CreateEventDTO $dto): Event
    {
        $event->update(array_filter([
            'title' => $dto->title,
            'description' => $dto->description,
            'location' => $dto->location,
            'address' => $dto->address,
            'city' => $dto->city,
            'state' => $dto->state,
            'start_date' => $dto->startDate,
            'end_date' => $dto->endDate,
            'banner' => $dto->banner,
        ], fn ($v) => $v !== null));

        return $event->fresh();
    }

    public function publishEvent(Event $event): Event
    {
        abort_if($event->status !== EventStatus::DRAFT, 422, 'Only draft events can be published');
        abort_if($event->ticketTypes()->count() === 0, 422, 'Event must have at least one ticket type');

        $this->organizerService->ensureAsaasAccount($event->organizer);

        $event->update(['status' => EventStatus::PUBLISHED]);

        return $event->fresh();
    }

    public function cancelEvent(Event $event): Event
    {
        abort_if(
            $event->orders()->where('status', OrderStatus::PAID)->exists(),
            422,
            'Cannot cancel event with paid orders'
        );

        $event->update(['status' => EventStatus::CANCELLED]);

        return $event->fresh();
    }
}
