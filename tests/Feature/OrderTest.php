<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Organizer;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function setupEventWithTickets(): array
    {
        $organizer = Organizer::factory()->create(['asaas_account_id' => 'acc_123']);
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => EventStatus::PUBLISHED,
        ]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 100,
            'quantity' => 10,
            'available' => 10,
            'sale_start' => now()->subDay(),
            'sale_end' => now()->addMonth(),
        ]);

        return [$event, $ticketType];
    }

    public function test_can_create_order_with_participants(): void
    {
        Http::fake(['*' => Http::response(['id' => 'pay_123', 'status' => 'PENDING'], 200)]);

        [$event, $ticketType] = $this->setupEventWithTickets();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'event_id' => $event->id,
            'billing_type' => 'PIX',
            'items' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 2,
                    'participants' => [
                        ['name' => 'João', 'email' => 'joao@test.com'],
                        ['name' => 'Maria', 'email' => 'maria@test.com'],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => OrderStatus::AWAITING_PAYMENT->value,
            'total_amount' => 200,
        ]);

        // Stock decremented
        $this->assertEquals(8, $ticketType->fresh()->available);

        // Participants created
        $this->assertDatabaseHas('participants', ['name' => 'João']);
        $this->assertDatabaseHas('participants', ['name' => 'Maria']);
    }

    public function test_free_order_goes_directly_to_paid(): void
    {
        [$event, $ticketType] = $this->setupEventWithTickets();
        $ticketType->update(['price' => 0]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'event_id' => $event->id,
            'billing_type' => 'PIX',
            'items' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 1,
                    'participants' => [
                        ['name' => 'João', 'email' => 'joao@test.com'],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => OrderStatus::PAID->value,
        ]);
    }

    public function test_insufficient_stock_returns_422(): void
    {
        Http::fake(['*' => Http::response(['id' => 'pay_123'], 200)]);

        [$event, $ticketType] = $this->setupEventWithTickets();
        $ticketType->update(['available' => 1]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'event_id' => $event->id,
            'billing_type' => 'PIX',
            'items' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 2,
                    'participants' => [
                        ['name' => 'João', 'email' => 'joao@test.com'],
                        ['name' => 'Maria', 'email' => 'maria@test.com'],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_participants_count_must_match_quantity(): void
    {
        [$event, $ticketType] = $this->setupEventWithTickets();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/orders', [
            'event_id' => $event->id,
            'billing_type' => 'PIX',
            'items' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 2,
                    'participants' => [
                        ['name' => 'João', 'email' => 'joao@test.com'],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_can_list_my_orders(): void
    {
        $user = User::factory()->create();
        \App\Models\Order::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/v1/orders/my');

        $response->assertOk();
    }

    public function test_fee_calculation(): void
    {
        Http::fake(['*' => Http::response(['id' => 'pay_123', 'status' => 'PENDING'], 200)]);

        [$event, $ticketType] = $this->setupEventWithTickets();
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/api/v1/orders', [
            'event_id' => $event->id,
            'billing_type' => 'PIX',
            'items' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 1,
                    'participants' => [
                        ['name' => 'João', 'email' => 'joao@test.com'],
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('orders', [
            'total_amount' => 100,
            'platform_fee' => 5,
            'organizer_amount' => 95,
        ]);
    }
}
