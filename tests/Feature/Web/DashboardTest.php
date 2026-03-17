<?php

namespace Tests\Feature\Web;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_user_without_organizer_redirected_to_onboarding(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/dashboard/onboarding');
    }

    public function test_onboarding_page_renders(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/onboarding');

        $response->assertOk();
        $response->assertSee('Create Organizer Profile');
    }

    public function test_can_create_organizer_via_onboarding(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/dashboard/onboarding', [
            'name' => 'My Events Co',
            'document' => '12345678000190',
            'phone' => '11999999999',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('organizers', ['user_id' => $user->id, 'name' => 'My Events Co']);
    }

    public function test_dashboard_shows_summary(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => EventStatus::PUBLISHED,
        ]);
        Order::factory()->count(3)->create([
            'event_id' => $event->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 100,
        ]);

        $response = $this->actingAs($organizer->user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('300'); // total revenue
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_events_list_shows_organizer_events(): void
    {
        $organizer = Organizer::factory()->create();
        Event::factory()->create(['organizer_id' => $organizer->id, 'title' => 'My Event']);

        $response = $this->actingAs($organizer->user)->get('/dashboard/events');

        $response->assertOk();
        $response->assertSee('My Event');
    }

    public function test_create_event_page_renders(): void
    {
        $organizer = Organizer::factory()->create();

        $response = $this->actingAs($organizer->user)->get('/dashboard/events/create');

        $response->assertOk();
        $response->assertSee('Create Event');
    }

    public function test_can_create_event(): void
    {
        $organizer = Organizer::factory()->create();

        $response = $this->actingAs($organizer->user)->post('/dashboard/events', [
            'title' => 'New Event',
            'description' => 'A test event',
            'location' => 'Convention Center',
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'start_date' => now()->addMonth()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect('/dashboard/events');
        $this->assertDatabaseHas('events', ['title' => 'New Event']);
    }

    public function test_can_update_event(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $response = $this->actingAs($organizer->user)->put('/dashboard/events/' . $event->id, [
            'title' => 'Updated Title',
            'start_date' => now()->addMonth()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Updated Title']);
    }

    public function test_can_publish_event(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => EventStatus::DRAFT]);
        \App\Models\TicketType::factory()->create(['event_id' => $event->id]);
        \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response(['id' => 'acc_123'])]);

        $response = $this->actingAs($organizer->user)->patch('/dashboard/events/' . $event->id . '/publish');

        $response->assertRedirect();
        $this->assertEquals(EventStatus::PUBLISHED, $event->fresh()->status);
    }

    public function test_cannot_manage_other_organizer_event(): void
    {
        $organizer1 = Organizer::factory()->create();
        $organizer2 = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer1->id]);

        $response = $this->actingAs($organizer2->user)->get('/dashboard/events/' . $event->id . '/edit');

        $response->assertForbidden();
    }
}
