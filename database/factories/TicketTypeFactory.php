<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketTypeFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(50, 500);
        return [
            'event_id' => Event::factory(),
            'name' => fake()->randomElement(['Standard', 'VIP', 'Premium', 'Corrida 5km', 'Corrida 10km']),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 500),
            'quantity' => $quantity,
            'available' => $quantity,
            'sale_start' => now(),
            'sale_end' => now()->addMonth(),
            'max_per_user' => 10,
        ];
    }

    public function free(): static
    {
        return $this->state(['price' => 0]);
    }
}
