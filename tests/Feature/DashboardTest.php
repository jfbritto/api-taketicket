<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function setupOrganizerWithEvent(): array
    {
        $user = User::factory()->create();
        $organizer = Organizer::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        return [$user, $organizer, $event];
    }

    public function test_summary_returns_correct_counts_and_revenue(): void
    {
        [$user, $organizer, $event] = $this->setupOrganizerWithEvent();

        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'available' => 50,
        ]);

        // Create 2 paid orders with known amounts
        Order::factory()->paid()->create([
            'event_id' => $event->id,
            'total_amount' => 200.00,
            'platform_fee' => 10.00,
            'organizer_amount' => 190.00,
        ]);
        Order::factory()->paid()->create([
            'event_id' => $event->id,
            'total_amount' => 300.00,
            'platform_fee' => 15.00,
            'organizer_amount' => 285.00,
        ]);

        // Create 1 pending order (should not count)
        Order::factory()->create([
            'event_id' => $event->id,
            'total_amount' => 100.00,
            'organizer_amount' => 95.00,
        ]);

        // Create tickets: 2 valid, 1 used
        $orderItem = OrderItem::factory()->create(['ticket_type_id' => $ticketType->id]);

        Ticket::factory()->create([
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'order_item_id' => $orderItem->id,
            'status' => TicketStatus::VALID,
        ]);
        Ticket::factory()->create([
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'order_item_id' => $orderItem->id,
            'status' => TicketStatus::VALID,
        ]);
        Ticket::factory()->create([
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'order_item_id' => $orderItem->id,
            'status' => TicketStatus::USED,
        ]);

        $response = $this->actingAs($user)->getJson("/api/v1/organizer/events/{$event->id}/summary");

        $response->assertOk();
        $response->assertJson([
            'data' => [
                'total_orders' => 2,
                'total_revenue' => 500.0,
                'organizer_revenue' => 475.0,
                'total_tickets' => 3,
                'checked_in' => 1,
                'tickets_available' => 50,
            ],
        ]);

        // Verify numeric types (JSON may decode whole numbers as int)
        $data = $response->json('data');
        $this->assertIsNumeric($data['total_revenue']);
        $this->assertIsNumeric($data['organizer_revenue']);
    }

    public function test_orders_returns_paginated_list(): void
    {
        [$user, $organizer, $event] = $this->setupOrganizerWithEvent();

        Order::factory()->count(3)->paid()->create(['event_id' => $event->id]);

        $response = $this->actingAs($user)->getJson("/api/v1/organizer/events/{$event->id}/orders");

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'event_id', 'status', 'total_amount', 'platform_fee', 'organizer_amount', 'created_at'],
            ],
            'links',
            'meta',
        ]);
    }

    public function test_orders_are_sorted_latest_first(): void
    {
        [$user, $organizer, $event] = $this->setupOrganizerWithEvent();

        $older = Order::factory()->paid()->create([
            'event_id' => $event->id,
            'created_at' => now()->subHour(),
        ]);
        $newer = Order::factory()->paid()->create([
            'event_id' => $event->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson("/api/v1/organizer/events/{$event->id}/orders");

        $response->assertOk();
        $data = $response->json('data');
        $this->assertEquals($newer->id, $data[0]['id']);
        $this->assertEquals($older->id, $data[1]['id']);
    }

    public function test_participants_returns_paginated_list(): void
    {
        [$user, $organizer, $event] = $this->setupOrganizerWithEvent();

        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $orderItem = OrderItem::factory()->create(['ticket_type_id' => $ticketType->id]);

        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'order_item_id' => $orderItem->id,
        ]);

        Participant::factory()->count(3)->create(['ticket_id' => $ticket->id]);

        $response = $this->actingAs($user)->getJson("/api/v1/organizer/events/{$event->id}/participants");

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
    }

    public function test_tickets_returns_paginated_list(): void
    {
        [$user, $organizer, $event] = $this->setupOrganizerWithEvent();

        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $orderItem = OrderItem::factory()->create(['ticket_type_id' => $ticketType->id]);

        Ticket::factory()->count(3)->create([
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'order_item_id' => $orderItem->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/v1/organizer/events/{$event->id}/tickets");

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
    }

    public function test_other_organizer_cannot_access_dashboard(): void
    {
        [$user, $organizer, $event] = $this->setupOrganizerWithEvent();

        $otherUser = User::factory()->create();
        Organizer::factory()->create(['user_id' => $otherUser->id]);

        $endpoints = ['orders', 'participants', 'tickets', 'summary'];

        foreach ($endpoints as $endpoint) {
            $response = $this->actingAs($otherUser)->getJson("/api/v1/organizer/events/{$event->id}/{$endpoint}");
            $response->assertForbidden();
        }
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $event = Event::factory()->create();

        $response = $this->getJson("/api/v1/organizer/events/{$event->id}/summary");

        $response->assertUnauthorized();
    }
}
