<?php

namespace App\Http\Controllers\Web;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\View\View;

class PublicEventController extends Controller
{
    public function show(string $slug): View
    {
        $event = Event::where('slug', $slug)
            ->where('status', EventStatus::PUBLISHED)
            ->with(['ticketTypes' => fn ($q) => $q->orderBy('price'), 'customFields'])
            ->firstOrFail();

        return view('public.event-show', compact('event'));
    }
}
