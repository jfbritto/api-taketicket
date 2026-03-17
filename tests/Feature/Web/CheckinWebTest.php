<?php

namespace Tests\Feature\Web;

use App\Enums\EventStatus;
use App\Enums\TicketStatus;
use App\Models\Checkin;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckinWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function setupCheckinScenario(): array
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => EventStatus::PUBLISHED,
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

        return [$organizer, $event, $ticket];
    }

    public function test_checkin_page_renders(): void
    {
        $organizer = Organizer::factory()->create();
        Event::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => EventStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($organizer->user)->get('/dashboard/checkin');

        $response->assertOk();
        $response->assertSee('Check-in');
    }

    public function test_can_validate_ticket(): void
    {
        [$organizer, $event, $ticket] = $this->setupCheckinScenario();

        $response = $this->actingAs($organizer->user)->post('/dashboard/checkin/validate', [
            'ticket_code' => $ticket->ticket_code,
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'valid']);
        $this->assertEquals(TicketStatus::USED, $ticket->fresh()->status);
    }

    public function test_can_undo_checkin(): void
    {
        [$organizer, $event, $ticket] = $this->setupCheckinScenario();

        // First check in
        $ticket->update(['status' => TicketStatus::USED, 'checked_in_at' => now()]);
        Checkin::create([
            'ticket_id' => $ticket->id,
            'checked_by' => $organizer->user->id,
            'checked_at' => now(),
        ]);

        // Then undo
        $response = $this->actingAs($organizer->user)->post('/dashboard/checkin/undo', [
            'ticket_code' => $ticket->ticket_code,
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'undone']);
        $this->assertEquals(TicketStatus::VALID, $ticket->fresh()->status);
    }
}
