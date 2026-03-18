<?php

namespace Tests\Feature\Web;

use App\Enums\EventStatus;
use App\Enums\TicketStatus;
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

class StaffParticipantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function setupCollaborator(): array
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

        return [$event, $staffUser, $organizer];
    }

    private function createParticipant(Event $event, string $name): Participant
    {
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $order = Order::factory()->paid()->create(['event_id' => $event->id]);
        $orderItem = OrderItem::factory()->create(['order_id' => $order->id, 'ticket_type_id' => $ticketType->id]);
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'order_item_id' => $orderItem->id,
            'status' => TicketStatus::VALID,
        ]);
        return Participant::factory()->create(['ticket_id' => $ticket->id, 'name' => $name]);
    }

    public function test_staff_can_view_participants(): void
    {
        [$event, $staffUser] = $this->setupCollaborator();
        $this->createParticipant($event, 'Ana Souza');

        $response = $this->actingAs($staffUser)->get(route('staff.participants', $event));

        $response->assertOk();
        $response->assertSee('Ana Souza');
    }

    public function test_staff_can_search_participants_by_name(): void
    {
        [$event, $staffUser] = $this->setupCollaborator();
        $this->createParticipant($event, 'Ana Souza');
        $this->createParticipant($event, 'Carlos Lima');

        $response = $this->actingAs($staffUser)->get(route('staff.participants', $event) . '?q=Ana');

        $response->assertOk();
        $response->assertSee('Ana Souza');
        $response->assertDontSee('Carlos Lima');
    }

    public function test_non_collaborator_cannot_view_participants(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('staff.participants', $event));

        $response->assertForbidden();
    }
}
