<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\OrderItem;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'ticket_type_id' => TicketType::factory(),
            'order_item_id' => OrderItem::factory(),
            'ticket_code' => 'TKT-'.strtoupper(Str::random(6)),
            'qr_code_payload' => Str::random(64),
            'status' => TicketStatus::VALID,
        ];
    }
}
