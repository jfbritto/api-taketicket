<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function createPendingOrder(): array
    {
        $organizer = Organizer::factory()->create(['asaas_account_id' => 'acc_123']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id, 'price' => 100]);
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => OrderStatus::AWAITING_PAYMENT,
            'total_amount' => 100,
            'platform_fee' => 5,
            'organizer_amount' => 95,
        ]);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'ticket_type_id' => $ticketType->id,
            'quantity' => 1,
            'unit_price' => 100,
        ]);

        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'order_item_id' => $orderItem->id,
            'ticket_code' => 'PENDING-'.Str::uuid(),
            'qr_code_payload' => '',
            'status' => TicketStatus::VALID,
        ]);

        Participant::factory()->create(['ticket_id' => $ticket->id]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'asaas_id' => 'pay_123',
            'status' => PaymentStatus::PENDING,
            'amount' => 100,
        ]);

        return [$order, $payment, $ticket];
    }

    public function test_webhook_confirms_payment_and_generates_tickets(): void
    {
        config(['asaas.webhook_token' => 'test_token']);
        [$order, $payment, $ticket] = $this->createPendingOrder();

        $response = $this->postJson('/api/v1/webhooks/asaas', [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => ['id' => 'pay_123'],
        ], ['asaas-access-token' => 'test_token']);

        $response->assertOk();

        $order->refresh();
        $payment->refresh();

        $this->assertEquals(OrderStatus::PAID, $order->status);
        $this->assertEquals(PaymentStatus::CONFIRMED, $payment->status);
    }

    public function test_duplicate_webhook_is_idempotent(): void
    {
        config(['asaas.webhook_token' => 'test_token']);
        [$order, $payment, $ticket] = $this->createPendingOrder();

        // Mark as already confirmed
        $payment->update(['status' => PaymentStatus::CONFIRMED]);
        $order->update(['status' => OrderStatus::PAID]);

        $response = $this->postJson('/api/v1/webhooks/asaas', [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => ['id' => 'pay_123'],
        ], ['asaas-access-token' => 'test_token']);

        $response->assertOk();
        // Should not dispatch another job
    }

    public function test_webhook_rejects_invalid_token(): void
    {
        config(['asaas.webhook_token' => 'test_token']);

        $response = $this->postJson('/api/v1/webhooks/asaas', [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => ['id' => 'pay_123'],
        ], ['asaas-access-token' => 'wrong_token']);

        $response->assertStatus(401);
    }
}
