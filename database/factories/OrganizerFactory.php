<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizerFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();
        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->paragraph(),
            'document' => fake()->numerify('###########'),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->randomElement(['SP', 'RJ', 'MG', 'BA', 'PR']),
            'postal_code' => fake()->postcode(),
        ];
    }
}
