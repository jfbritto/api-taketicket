<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'platform_fee' => $this->platform_fee,
            'organizer_amount' => $this->organizer_amount,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
            'event' => new EventResource($this->whenLoaded('event')),
            'items' => $this->whenLoaded('items'),
            'payment' => $this->whenLoaded('payment'),
        ];
    }
}
