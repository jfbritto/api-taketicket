<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventCollaborator;
use App\Models\Organizer;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventCollaboratorFactory extends Factory
{
    protected $model = EventCollaborator::class;

    public function definition(): array
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        return [
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'invitee_email' => fake()->unique()->safeEmail(),
            'user_id' => null,
            'status' => 'pending',
            'expires_at' => now()->addDay(),
            'accepted_at' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active', 'accepted_at' => now()]);
    }

    public function revoked(): static
    {
        return $this->state(['status' => 'revoked']);
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }
}
