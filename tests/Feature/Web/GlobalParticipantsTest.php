<?php

namespace Tests\Feature\Web;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalParticipantsTest extends TestCase
{
    use RefreshDatabase;

    private function userWithOrganizer(): array
    {
        $user = User::factory()->create();
        $organizer = Organizer::factory()->create(['user_id' => $user->id]);
        return [$user, $organizer];
    }

    public function test_participants_page_renders(): void
    {
        [$user] = $this->userWithOrganizer();
        $response = $this->actingAs($user)->get('/dashboard/participantes');
        $response->assertStatus(200);
        $response->assertSee('Participantes');
    }

    public function test_unauthenticated_redirected(): void
    {
        $this->get('/dashboard/participantes')->assertRedirect('/login');
    }

    public function test_shows_participants_from_own_events(): void
    {
        [$user, $organizer] = $this->userWithOrganizer();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => 'published']);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'ticket_type_id' => $ticketType->id]);
        $participant = Participant::factory()->create(['ticket_id' => $ticket->id, 'name' => 'João Silva']);

        $response = $this->actingAs($user)->get('/dashboard/participantes');
        $response->assertSee('João Silva');
    }

    public function test_does_not_show_other_organizer_participants(): void
    {
        [$user] = $this->userWithOrganizer();
        $otherUser = User::factory()->create();
        $otherOrganizer = Organizer::factory()->create(['user_id' => $otherUser->id]);
        $otherEvent = Event::factory()->create(['organizer_id' => $otherOrganizer->id, 'status' => 'published']);
        $ticketType = TicketType::factory()->create(['event_id' => $otherEvent->id]);
        $ticket = Ticket::factory()->create(['event_id' => $otherEvent->id, 'ticket_type_id' => $ticketType->id]);
        Participant::factory()->create(['ticket_id' => $ticket->id, 'name' => 'Outro Participante']);

        $response = $this->actingAs($user)->get('/dashboard/participantes');
        $response->assertDontSee('Outro Participante');
    }

    public function test_search_filters_by_name(): void
    {
        [$user, $organizer] = $this->userWithOrganizer();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => 'published']);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $ticket1 = Ticket::factory()->create(['event_id' => $event->id, 'ticket_type_id' => $ticketType->id]);
        $ticket2 = Ticket::factory()->create(['event_id' => $event->id, 'ticket_type_id' => $ticketType->id]);
        Participant::factory()->create(['ticket_id' => $ticket1->id, 'name' => 'Maria Souza']);
        Participant::factory()->create(['ticket_id' => $ticket2->id, 'name' => 'Carlos Lima']);

        $response = $this->actingAs($user)->get('/dashboard/participantes?search=Maria');
        $response->assertSee('Maria Souza');
        $response->assertDontSee('Carlos Lima');
    }

    public function test_csv_export_works(): void
    {
        [$user, $organizer] = $this->userWithOrganizer();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => 'published']);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'ticket_type_id' => $ticketType->id]);
        Participant::factory()->create(['ticket_id' => $ticket->id, 'name' => 'Ana Teste']);

        $response = $this->actingAs($user)->get('/dashboard/participantes/export');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Ana Teste', $response->streamedContent());
    }
}
