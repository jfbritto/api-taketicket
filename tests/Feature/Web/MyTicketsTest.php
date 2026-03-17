<?php

namespace Tests\Feature\Web;

use App\Enums\TicketStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyTicketsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_my_tickets_page_renders(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/my-tickets');

        $response->assertOk();
    }

    public function test_my_tickets_shows_user_tickets(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->paid()->create(['user_id' => $user->id]);
        $orderItem = OrderItem::factory()->create(['order_id' => $order->id]);
        $ticket = Ticket::factory()->create([
            'order_item_id' => $orderItem->id,
            'event_id' => $order->event_id,
            'status' => TicketStatus::VALID,
        ]);

        $response = $this->actingAs($user)->get('/my-tickets');

        $response->assertOk();
    }

    public function test_cannot_view_other_users_ticket(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $order = Order::factory()->paid()->create(['user_id' => $user1->id]);
        $orderItem = OrderItem::factory()->create(['order_id' => $order->id]);
        $ticket = Ticket::factory()->create([
            'order_item_id' => $orderItem->id,
            'event_id' => $order->event_id,
        ]);

        $response = $this->actingAs($user2)->get("/my-tickets/{$ticket->id}");

        $response->assertForbidden();
    }
}
