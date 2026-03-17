<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'ticket_type_id' => TicketType::factory(),
            'quantity' => 1,
            'unit_price' => fake()->randomFloat(2, 10, 500),
        ];
    }
}
