<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\Ticket;
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
        Order::factory()->count(3)->create(['user_id' => $user->id]);

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

    public function test_full_purchase_flow_integration(): void
    {
        // Fake all Asaas API calls
        Http::fake(['*' => Http::response(['id' => 'pay_integration_123', 'status' => 'PENDING'], 200)]);

        // Step 1: Register organizer user via API
        $registerResponse = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'testflow@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '11999999999',
            'document' => '12345678901',
        ]);
        $registerResponse->assertStatus(201);
        $organizerUser = User::where('email', 'testflow@example.com')->first();

        // Step 2: Create organizer profile
        $organizerResponse = $this->actingAs($organizerUser)->postJson('/api/v1/organizers', [
            'name' => 'Test Organizer',
            'document' => '12345678000100',
            'phone' => '11999999999',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);
        $organizerResponse->assertStatus(201);

        // Set asaas_account_id on the organizer (simulating Asaas sub-account creation)
        $organizerUser->refresh();
        $organizerUser->organizer->update(['asaas_account_id' => 'acc_integration_123']);

        // Step 3: Create event
        $eventResponse = $this->actingAs($organizerUser)->postJson('/api/v1/organizer/events', [
            'title' => 'Integration Test Event',
            'description' => 'A test event for integration testing',
            'city' => 'São Paulo',
            'state' => 'SP',
            'start_date' => now()->addWeek()->toDateTimeString(),
        ]);
        $eventResponse->assertStatus(201);
        $eventId = $eventResponse->json('data.id') ?? $eventResponse->json('id');
        $this->assertNotNull($eventId, 'Event ID should not be null');

        // Step 4: Add ticket type
        $ticketTypeResponse = $this->actingAs($organizerUser)->postJson(
            "/api/v1/organizer/events/{$eventId}/ticket-types",
            [
                'name' => 'General',
                'price' => 50.00,
                'quantity' => 100,
                'sale_start' => now()->subDay()->toDateTimeString(),
                'sale_end' => now()->addMonth()->toDateTimeString(),
            ]
        );
        $ticketTypeResponse->assertStatus(201);
        $ticketTypeId = $ticketTypeResponse->json('id');

        // Step 5: Publish event
        $publishResponse = $this->actingAs($organizerUser)->patchJson(
            "/api/v1/organizer/events/{$eventId}/publish"
        );
        $publishResponse->assertOk();
        $publishResponse->assertJsonFragment(['status' => 'published']);

        // Step 6: Register a buyer user
        $buyerResponse = $this->postJson('/api/v1/auth/register', [
            'name' => 'Buyer User',
            'email' => 'buyer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $buyerResponse->assertStatus(201);
        $buyerUser = User::where('email', 'buyer@example.com')->first();

        // Step 7: Create order
        $orderResponse = $this->actingAs($buyerUser)->postJson('/api/v1/orders', [
            'event_id' => $eventId,
            'billing_type' => 'PIX',
            'items' => [
                [
                    'ticket_type_id' => $ticketTypeId,
                    'quantity' => 2,
                    'participants' => [
                        ['name' => 'Alice', 'email' => 'alice@example.com'],
                        ['name' => 'Bob', 'email' => 'bob@example.com'],
                    ],
                ],
            ],
        ]);
        $orderResponse->assertStatus(201);
        $orderId = $orderResponse->json('data.id') ?? $orderResponse->json('id');

        // Verify order is awaiting payment
        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => OrderStatus::AWAITING_PAYMENT->value,
            'total_amount' => 100,
        ]);

        // Verify stock was decremented
        $this->assertEquals(98, TicketType::find($ticketTypeId)->available);

        // Verify participants were created
        $this->assertDatabaseHas('participants', ['name' => 'Alice', 'email' => 'alice@example.com']);
        $this->assertDatabaseHas('participants', ['name' => 'Bob', 'email' => 'bob@example.com']);

        // Verify tickets exist but with pending codes
        $order = Order::find($orderId);
        $tickets = Ticket::whereIn('order_item_id', $order->items->pluck('id'))->get();
        $this->assertCount(2, $tickets);
        foreach ($tickets as $ticket) {
            $this->assertStringStartsWith('PENDING-', $ticket->ticket_code);
            $this->assertEquals('', $ticket->qr_code_payload);
        }

        // Step 8: Simulate Asaas webhook for payment confirmation
        config(['asaas.webhook_token' => 'integration_test_token']);

        $webhookResponse = $this->postJson('/api/v1/webhooks/asaas', [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => ['id' => 'pay_integration_123', 'status' => 'CONFIRMED'],
        ], ['asaas-access-token' => 'integration_test_token']);
        $webhookResponse->assertOk();

        // Step 9: Verify order is now paid
        $order->refresh();
        $this->assertEquals(OrderStatus::PAID, $order->status);

        // Step 10: Verify tickets were generated with proper codes and QR payloads
        $tickets = Ticket::whereIn('order_item_id', $order->items->pluck('id'))->get();
        $this->assertCount(2, $tickets);
        foreach ($tickets as $ticket) {
            $freshTicket = $ticket->fresh();
            $this->assertStringStartsNotWith('PENDING-', $freshTicket->ticket_code, 'Ticket code should be generated after payment');
            $this->assertNotEmpty($freshTicket->qr_code_payload, 'QR code payload should be generated after payment');
            $this->assertStringContainsString(':', $freshTicket->qr_code_payload, 'QR payload should contain colon separator (id:hmac)');
        }

        // Step 11: Verify buyer can see their orders and tickets
        $myOrdersResponse = $this->actingAs($buyerUser)->getJson('/api/v1/orders/my');
        $myOrdersResponse->assertOk();
        $this->assertGreaterThanOrEqual(1, count($myOrdersResponse->json('data')));

        $myTicketsResponse = $this->actingAs($buyerUser)->getJson('/api/v1/tickets/my');
        $myTicketsResponse->assertOk();
    }
}
