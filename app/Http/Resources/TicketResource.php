<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'ticket_code' => $this->ticket_code,
            'qr_code_payload' => $this->qr_code_payload,
            'status' => $this->status,
            'checked_in_at' => $this->checked_in_at,
            'created_at' => $this->created_at,
            'event' => new EventResource($this->whenLoaded('event')),
            'ticket_type' => $this->whenLoaded('ticketType'),
            'participant' => new ParticipantResource($this->whenLoaded('participant')),
        ];
    }
}
