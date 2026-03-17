<?php

namespace Tests\Feature\Web;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_event_show_page_renders(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $response = $this->actingAs($organizer->user)->get("/dashboard/events/{$event->id}");

        $response->assertOk();
        $response->assertSee($event->title);
    }

    public function test_cannot_view_other_organizer_event_show_page(): void
    {
        $organizer1 = Organizer::factory()->create();
        $organizer2 = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer1->id]);

        $response = $this->actingAs($organizer2->user)->get("/dashboard/events/{$event->id}");

        $response->assertForbidden();
    }
}
