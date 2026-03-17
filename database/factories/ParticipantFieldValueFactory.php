<?php

namespace Database\Factories;

use App\Models\CustomField;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipantFieldValueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'participant_id' => Participant::factory(),
            'custom_field_id' => CustomField::factory(),
            'value' => fake()->word(),
        ];
    }
}
