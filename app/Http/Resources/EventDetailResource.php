<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'location' => $this->location,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'banner' => $this->banner,
            'status' => $this->status,
            'organizer' => new OrganizerResource($this->whenLoaded('organizer')),
            'ticket_types' => $this->whenLoaded('ticketTypes'),
            'custom_fields' => $this->whenLoaded('customFields'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
