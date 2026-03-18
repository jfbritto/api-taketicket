<?php

namespace Tests\Feature\Web;

use App\Enums\EventStatus;
use App\Enums\TicketStatus;
use App\Models\Checkin;
use App\Models\Event;
use App\Models\EventCollaborator;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffCheckinTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function setupScenario(): array
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => EventStatus::PUBLISHED,
        ]);
        $staffUser = User::factory()->create();
        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'user_id' => $staffUser->id,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $order = Order::factory()->paid()->create(['event_id' => $event->id]);
        $orderItem = OrderItem::factory()->create(['order_id' => $order->id, 'ticket_type_id' => $ticketType->id]);
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'order_item_id' => $orderItem->id,
            'status' => TicketStatus::VALID,
        ]);
        Participant::factory()->create(['ticket_id' => $ticket->id]);

        return [$event, $staffUser, $ticket];
    }

    public function test_staff_checkin_page_renders(): void
    {
        [$event, $staffUser] = $this->setupScenario();

        $response = $this->actingAs($staffUser)->get(route('staff.checkin', $event));

        $response->assertOk();
        $response->assertSee('Check-in');
    }

    public function test_staff_can_validate_ticket(): void
    {
        [$event, $staffUser, $ticket] = $this->setupScenario();

        $response = $this->actingAs($staffUser)
            ->post(route('staff.checkin.validate', $event), [
                'ticket_code' => $ticket->ticket_code,
            ]);

        $response->assertOk();
        $response->assertJson(['status' => 'valid']);
        $response->assertJsonPath('participant.ticket_code', $ticket->ticket_code);
        $this->assertEquals(TicketStatus::USED, $ticket->fresh()->status);
    }

    public function test_staff_cannot_validate_ticket_from_other_event(): void
    {
        [$event, $staffUser] = $this->setupScenario();

        // Ticket from a different event
        $otherOrganizer = Organizer::factory()->create();
        $otherEvent = Event::factory()->create(['organizer_id' => $otherOrganizer->id]);
        $otherTicketType = TicketType::factory()->create(['event_id' => $otherEvent->id]);
        $otherOrder = Order::factory()->paid()->create(['event_id' => $otherEvent->id]);
        $otherOrderItem = OrderItem::factory()->create(['order_id' => $otherOrder->id, 'ticket_type_id' => $otherTicketType->id]);
        $otherTicket = Ticket::factory()->create([
            'event_id' => $otherEvent->id,
            'ticket_type_id' => $otherTicketType->id,
            'order_item_id' => $otherOrderItem->id,
            'status' => TicketStatus::VALID,
        ]);

        $response = $this->actingAs($staffUser)
            ->post(route('staff.checkin.validate', $event), [
                'ticket_code' => $otherTicket->ticket_code,
            ]);

        $response->assertJson(['status' => 'invalid']);
        $this->assertEquals(TicketStatus::VALID, $otherTicket->fresh()->status);
    }

    public function test_staff_can_undo_checkin(): void
    {
        [$event, $staffUser, $ticket] = $this->setupScenario();

        // Check in first
        $ticket->update(['status' => TicketStatus::USED, 'checked_in_at' => now()]);
        Checkin::create([
            'ticket_id' => $ticket->id,
            'checked_by' => $staffUser->id,
            'checked_at' => now(),
        ]);

        $response = $this->actingAs($staffUser)
            ->post(route('staff.checkin.undo', $event), [
                'ticket_code' => $ticket->ticket_code,
            ]);

        $response->assertOk();
        $response->assertJson(['status' => 'undone']);
        $this->assertEquals(TicketStatus::VALID, $ticket->fresh()->status);
    }

    public function test_non_collaborator_cannot_access_staff_checkin(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('staff.checkin', $event));

        $response->assertForbidden();
    }
}
