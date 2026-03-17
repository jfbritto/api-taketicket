<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Checkin;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckinTest extends TestCase
{
    use RefreshDatabase;

    private function createTicketForUser(User $user, array $ticketAttrs = []): Ticket
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $order = Order::factory()->paid()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'ticket_type_id' => $ticketType->id,
        ]);

        $ticket = Ticket::factory()->create(array_merge([
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'order_item_id' => $orderItem->id,
            'status' => TicketStatus::VALID,
        ], $ticketAttrs));

        Participant::factory()->create(['ticket_id' => $ticket->id]);

        return $ticket;
    }

    public function test_valid_checkin_returns_200_with_participant_data(): void
    {
        $user = User::factory()->create();
        $ticket = $this->createTicketForUser($user);

        $response = $this->actingAs($user)->postJson('/api/v1/checkin', [
            'ticket_code' => $ticket->ticket_code,
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'valid')
            ->assertJsonStructure(['status', 'participant' => ['id', 'name', 'email']]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => TicketStatus::USED->value,
        ]);

        $this->assertDatabaseHas('checkins', [
            'ticket_id' => $ticket->id,
            'checked_by' => $user->id,
        ]);
    }

    public function test_already_used_ticket_returns_409(): void
    {
        $user = User::factory()->create();
        $ticket = $this->createTicketForUser($user, [
            'status' => TicketStatus::USED,
            'checked_in_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/checkin', [
            'ticket_code' => $ticket->ticket_code,
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('status', 'already_used')
            ->assertJsonStructure(['status', 'checked_in_at']);
    }

    public function test_invalid_ticket_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/checkin', [
            'ticket_code' => 'NONEXISTENT-CODE',
        ]);

        $response->assertNotFound()
            ->assertJsonPath('status', 'invalid');
    }

    public function test_cancelled_ticket_returns_404(): void
    {
        $user = User::factory()->create();
        $ticket = $this->createTicketForUser($user, [
            'status' => TicketStatus::CANCELLED,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/checkin', [
            'ticket_code' => $ticket->ticket_code,
        ]);

        $response->assertNotFound()
            ->assertJsonPath('status', 'invalid');
    }

    public function test_checkin_via_qr_payload_with_hmac_validation(): void
    {
        $user = User::factory()->create();
        $ticket = $this->createTicketForUser($user);

        // Generate a valid QR payload using the same logic as TicketService
        $ticketService = app(TicketService::class);
        $validPayload = $ticketService->generateQrPayload($ticket);

        $response = $this->actingAs($user)->postJson('/api/v1/checkin', [
            'qr_code_payload' => $validPayload,
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'valid')
            ->assertJsonStructure(['status', 'participant' => ['id', 'name', 'email']]);
    }

    public function test_checkin_via_invalid_qr_payload_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/checkin', [
            'qr_code_payload' => '999:invalidhmac',
        ]);

        $response->assertNotFound()
            ->assertJsonPath('status', 'invalid');
    }

    public function test_user_can_list_their_tickets(): void
    {
        $user = User::factory()->create();
        $this->createTicketForUser($user);
        $this->createTicketForUser($user);

        // Create a cancelled ticket that should be excluded
        $this->createTicketForUser($user, ['status' => TicketStatus::CANCELLED]);

        $response = $this->actingAs($user)->getJson('/api/v1/tickets/my');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_view_single_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = $this->createTicketForUser($user);

        $response = $this->actingAs($user)->getJson("/api/v1/tickets/{$ticket->id}");

        $response->assertOk()
            ->assertJsonPath('id', $ticket->id)
            ->assertJsonStructure(['id', 'ticket_code', 'status', 'participant']);
    }

    public function test_user_gets_403_for_other_users_ticket(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $ticket = $this->createTicketForUser($owner);

        $response = $this->actingAs($otherUser)->getJson("/api/v1/tickets/{$ticket->id}");

        $response->assertForbidden();
    }

    public function test_checkin_with_device_info(): void
    {
        $user = User::factory()->create();
        $ticket = $this->createTicketForUser($user);

        $response = $this->actingAs($user)->postJson('/api/v1/checkin', [
            'ticket_code' => $ticket->ticket_code,
            'device' => 'Scanner-01',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('checkins', [
            'ticket_id' => $ticket->id,
            'device' => 'Scanner-01',
        ]);
    }
}
