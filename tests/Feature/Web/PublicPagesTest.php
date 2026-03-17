<?php

namespace Tests\Feature\Web;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_home_page_shows_published_events(): void
    {
        Event::factory()->create(['status' => EventStatus::PUBLISHED, 'title' => 'Public Event']);
        Event::factory()->create(['status' => EventStatus::DRAFT, 'title' => 'Draft Event']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Public Event');
        $response->assertDontSee('Draft Event');
    }

    public function test_event_page_shows_event_details(): void
    {
        $event = Event::factory()->create([
            'status' => EventStatus::PUBLISHED,
            'title' => 'Concert Night',
        ]);
        TicketType::factory()->create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 150,
            'available' => 50,
            'sale_start' => now()->subDay(),
            'sale_end' => now()->addMonth(),
        ]);

        $response = $this->get('/event/'.$event->slug);

        $response->assertOk();
        $response->assertSee('Concert Night');
        $response->assertSee('VIP');
        $response->assertSee('150');
    }

    public function test_draft_event_returns_404(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::DRAFT]);

        $response = $this->get('/event/'.$event->slug);

        $response->assertNotFound();
    }
}
