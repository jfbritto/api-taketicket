<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'location' => $this->location,
            'city' => $this->city,
            'state' => $this->state,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'banner' => $this->banner,
            'status' => $this->status,
            'organizer' => new OrganizerResource($this->whenLoaded('organizer')),
            'created_at' => $this->created_at,
        ];
    }
}
