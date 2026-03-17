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
            'user' => $this->when($this->relationLoaded('user'), fn () => [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            'event_id' => $this->event_id,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'platform_fee' => $this->platform_fee,
            'organizer_amount' => $this->organizer_amount,
            'expires_at' => $this->expires_at,
            'items' => $this->when($this->relationLoaded('items'), fn () => $this->items->map(fn ($item) => [
                'ticket_type_id' => $item->ticket_type_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ])),
            'payment' => $this->whenLoaded('payment'),
            'event' => new EventResource($this->whenLoaded('event')),
            'created_at' => $this->created_at,
        ];
    }
}
