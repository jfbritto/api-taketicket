<?php

namespace Tests\Feature\Web;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialTest extends TestCase
{
    use RefreshDatabase;

    private function userWithOrganizer(): array
    {
        $user = User::factory()->create();
        $organizer = Organizer::factory()->create(['user_id' => $user->id]);
        return [$user, $organizer];
    }

    public function test_financial_page_renders(): void
    {
        [$user] = $this->userWithOrganizer();
        $response = $this->actingAs($user)->get('/dashboard/financeiro');
        $response->assertStatus(200);
        $response->assertSee('Financeiro');
    }

    public function test_unauthenticated_redirected(): void
    {
        $this->get('/dashboard/financeiro')->assertRedirect('/login');
    }

    public function test_financial_shows_correct_totals(): void
    {
        [$user, $organizer] = $this->userWithOrganizer();
        $buyer = User::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => 'published']);

        Order::factory()->create([
            'event_id' => $event->id,
            'user_id' => $buyer->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 100.00,
            'platform_fee' => 10.00,
            'organizer_amount' => 90.00,
        ]);
        Order::factory()->create([
            'event_id' => $event->id,
            'user_id' => $buyer->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 200.00,
            'platform_fee' => 20.00,
            'organizer_amount' => 180.00,
        ]);
        // Pending order — should NOT count
        Order::factory()->create([
            'event_id' => $event->id,
            'user_id' => $buyer->id,
            'status' => OrderStatus::PENDING,
            'total_amount' => 50.00,
            'platform_fee' => 5.00,
            'organizer_amount' => 45.00,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/financeiro');
        $response->assertStatus(200);
        $response->assertSee('270'); // organizer_amount total (90+180)
        $response->assertSee('30');  // platform_fee total (10+20)
    }

    public function test_only_own_organizer_orders_shown(): void
    {
        [$user] = $this->userWithOrganizer();
        $otherUser = User::factory()->create();
        $otherOrganizer = Organizer::factory()->create(['user_id' => $otherUser->id]);
        $otherEvent = Event::factory()->create(['organizer_id' => $otherOrganizer->id, 'status' => 'published']);

        Order::factory()->create([
            'event_id' => $otherEvent->id,
            'user_id' => $otherUser->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 999.00,
            'platform_fee' => 99.00,
            'organizer_amount' => 900.00,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/financeiro');
        $response->assertStatus(200);
        $response->assertDontSee('900');
    }
}
