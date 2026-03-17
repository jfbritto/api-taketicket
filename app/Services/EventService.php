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
        abort_if($event->status !== EventStatus::DRAFT, 422, 'Somente eventos em rascunho podem ser publicados.');
        abort_if($event->ticketTypes()->count() === 0, 422, 'O evento precisa ter pelo menos um tipo de ingresso antes de ser publicado.');

        if (config('asaas.api_key')) {
            $this->organizerService->ensureAsaasAccount($event->organizer);
        }

        $event->update(['status' => EventStatus::PUBLISHED]);

        return $event->fresh();
    }

    public function cancelEvent(Event $event): Event
    {
        abort_if(
            $event->orders()->where('status', OrderStatus::PAID)->exists(),
            422,
            'Não é possível cancelar um evento com pedidos pagos.'
        );

        $event->update(['status' => EventStatus::CANCELLED]);

        return $event->fresh();
    }
}
