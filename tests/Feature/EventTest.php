<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\Organizer;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    private function createOrganizerUser(): array
    {
        $user = User::factory()->create();
        $organizer = Organizer::factory()->create(['user_id' => $user->id]);
        return [$user, $organizer];
    }

    public function test_can_list_published_events(): void
    {
        Event::factory()->create(['status' => EventStatus::PUBLISHED]);
        Event::factory()->create(['status' => EventStatus::DRAFT]);

        $response = $this->getJson('/api/v1/events');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_get_event_by_slug(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::PUBLISHED]);

        $response = $this->getJson("/api/v1/events/{$event->slug}");

        $response->assertOk()
            ->assertJsonFragment(['title' => $event->title]);
    }

    public function test_organizer_can_create_event(): void
    {
        [$user, $organizer] = $this->createOrganizerUser();

        $response = $this->actingAs($user)->postJson('/api/v1/organizer/events', [
            'title' => 'Corrida 5km',
            'description' => 'Uma corrida incrível',
            'city' => 'São Paulo',
            'state' => 'SP',
            'start_date' => now()->addWeek()->toDateTimeString(),
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Corrida 5km', 'status' => 'draft']);
    }

    public function test_organizer_can_update_event(): void
    {
        [$user, $organizer] = $this->createOrganizerUser();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $response = $this->actingAs($user)->putJson("/api/v1/organizer/events/{$event->id}", [
            'title' => 'Novo Título',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Novo Título']);
    }

    public function test_cannot_publish_without_ticket_types(): void
    {
        [$user, $organizer] = $this->createOrganizerUser();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $response = $this->actingAs($user)->patchJson("/api/v1/organizer/events/{$event->id}/publish");

        $response->assertStatus(422);
    }

    public function test_cannot_manage_other_organizer_event(): void
    {
        $otherUser = User::factory()->create();
        $otherOrganizer = Organizer::factory()->create(['user_id' => $otherUser->id]);
        $event = Event::factory()->create(['organizer_id' => $otherOrganizer->id]);

        [$user, $organizer] = $this->createOrganizerUser();

        $response = $this->actingAs($user)->getJson("/api/v1/organizer/events/{$event->id}");

        $response->assertStatus(403);
    }

    public function test_can_cancel_event_without_paid_orders(): void
    {
        [$user, $organizer] = $this->createOrganizerUser();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => EventStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($user)->patchJson("/api/v1/organizer/events/{$event->id}/cancel");

        $response->assertOk()
            ->assertJsonFragment(['status' => 'cancelled']);
    }
}
