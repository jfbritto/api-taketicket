<?php

namespace Tests\Feature\Web;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function setupEvent(): array
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

    public function test_can_create_order_via_checkout(): void
    {
        Http::fake(['*' => Http::response(['id' => 'pay_123', 'status' => 'PENDING'])]);
        [$event, $ticketType] = $this->setupEvent();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/checkout/order', [
            'event_id' => $event->id,
            'items' => [
                $ticketType->id => ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
            ],
        ]);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $response->assertRedirect("/checkout/{$order->id}");
    }

    public function test_checkout_shows_participant_forms(): void
    {
        Http::fake(['*' => Http::response(['id' => 'pay_123', 'status' => 'PENDING'])]);
        [$event, $ticketType] = $this->setupEvent();
        $user = User::factory()->create();

        $this->actingAs($user)->post('/checkout/order', [
            'event_id' => $event->id,
            'items' => [$ticketType->id => ['ticket_type_id' => $ticketType->id, 'quantity' => 1]],
        ]);

        $order = Order::where('user_id', $user->id)->first();
        $response = $this->actingAs($user)->get("/checkout/{$order->id}");

        $response->assertOk();
        $response->assertSee('Participant');
    }

    public function test_expired_order_redirects_to_event(): void
    {
        [$event, $ticketType] = $this->setupEvent();
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => OrderStatus::AWAITING_PAYMENT,
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($user)->get("/checkout/{$order->id}");

        $response->assertRedirect("/event/{$event->slug}");
    }

    public function test_cannot_access_other_users_order(): void
    {
        [$event, $ticketType] = $this->setupEvent();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user1->id, 'event_id' => $event->id]);

        $response = $this->actingAs($user2)->get("/checkout/{$order->id}");

        $response->assertForbidden();
    }

    public function test_checkout_status_returns_json(): void
    {
        [$event, $ticketType] = $this->setupEvent();
        $user = User::factory()->create();
        $order = Order::factory()->paid()->create(['user_id' => $user->id, 'event_id' => $event->id]);

        $response = $this->actingAs($user)->getJson("/checkout/{$order->id}/status");

        $response->assertOk();
        $response->assertJson(['status' => 'paid']);
    }

    public function test_unauthenticated_checkout_redirects_to_login(): void
    {
        $response = $this->post('/checkout/order', ['event_id' => 1]);

        $response->assertRedirect('/login');
    }
}
