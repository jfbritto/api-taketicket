<?php

namespace App\Repositories;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EventRepository
{
    public function listPublished(array $filters = []): LengthAwarePaginator
    {
        $query = Event::where('status', EventStatus::PUBLISHED)
            ->with(['ticketTypes', 'organizer']);

        if (isset($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (isset($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        if (isset($filters['date'])) {
            $query->whereDate('start_date', $filters['date']);
        }

        return $query->orderBy('start_date')->paginate(15);
    }

    public function findBySlug(string $slug): ?Event
    {
        return Event::where('slug', $slug)
            ->with(['ticketTypes', 'customFields', 'organizer'])
            ->first();
    }
}
