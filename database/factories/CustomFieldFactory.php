<?php

namespace Database\Factories;

use App\Enums\CustomFieldType;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomFieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'label' => fake()->words(2, true),
            'type' => CustomFieldType::TEXT,
            'required' => false,
            'position' => 0,
        ];
    }
}
