<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\OrderItem;
use App\Models\Organizer;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTypeTest extends TestCase
{
    use RefreshDatabase;

    private function createEventWithOrganizer(): array
    {
        $user = User::factory()->create();
        $organizer = Organizer::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        return [$user, $event];
    }

    public function test_organizer_can_create_ticket_type(): void
    {
        [$user, $event] = $this->createEventWithOrganizer();

        $response = $this->actingAs($user)->postJson("/api/v1/organizer/events/{$event->id}/ticket-types", [
            'name' => 'VIP',
            'price' => 100.00,
            'quantity' => 50,
            'sale_start' => now()->toDateTimeString(),
            'sale_end' => now()->addMonth()->toDateTimeString(),
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'VIP', 'available' => 50]);
    }

    public function test_organizer_can_update_ticket_type(): void
    {
        [$user, $event] = $this->createEventWithOrganizer();
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($user)->putJson(
            "/api/v1/organizer/events/{$event->id}/ticket-types/{$ticketType->id}",
            ['name' => 'Super VIP']
        );

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Super VIP']);
    }

    public function test_organizer_can_delete_ticket_type_without_sales(): void
    {
        [$user, $event] = $this->createEventWithOrganizer();
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($user)->deleteJson(
            "/api/v1/organizer/events/{$event->id}/ticket-types/{$ticketType->id}"
        );

        $response->assertStatus(204);
        $this->assertDatabaseMissing('ticket_types', ['id' => $ticketType->id]);
    }

    public function test_cannot_delete_ticket_type_with_sales(): void
    {
        [$user, $event] = $this->createEventWithOrganizer();
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        OrderItem::factory()->create(['ticket_type_id' => $ticketType->id]);

        $response = $this->actingAs($user)->deleteJson(
            "/api/v1/organizer/events/{$event->id}/ticket-types/{$ticketType->id}"
        );

        $response->assertStatus(422);
    }
}
