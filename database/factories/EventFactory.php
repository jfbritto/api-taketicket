<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Organizer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'organizer_id' => Organizer::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->paragraphs(2, true),
            'location' => fake()->company(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->randomElement(['SP', 'RJ', 'MG', 'BA', 'PR']),
            'start_date' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'end_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'status' => EventStatus::DRAFT,
        ];
    }

    public function published(): static
    {
        return $this->state(['status' => EventStatus::PUBLISHED]);
    }
}
