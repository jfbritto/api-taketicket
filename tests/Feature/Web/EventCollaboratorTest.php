<?php

namespace Tests\Feature\Web;

use App\Models\Event;
use App\Models\EventCollaborator;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCollaboratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_active_scope_filters_by_status_and_expiry(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create();

        // Active, not expired
        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);

        // Active but expired
        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->subDay(),
        ]);

        // Pending
        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'invitee_email' => 'other@example.com',
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);

        $this->assertCount(1, EventCollaborator::active()->get());
    }

    public function test_is_expired_returns_correct_value(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $notExpired = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);

        $expired = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'status' => 'active',
            'expires_at' => now()->subDay(),
        ]);

        $this->assertFalse($notExpired->isExpired());
        $this->assertTrue($expired->isExpired());
    }

    public function test_user_has_collaborations_relationship(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create();

        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);

        $this->assertCount(1, $user->collaborations);
    }

    public function test_event_has_collaborators_relationship(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);

        $this->assertCount(1, $event->collaborators);
    }

    public function test_ensure_has_organizer_redirects_collaborator_to_staff(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create();

        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);

        // /dashboard is behind EnsureHasOrganizer; collaborator-only user should go to /staff
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/staff');
    }

    public function test_ensure_has_organizer_redirects_plain_user_to_onboarding(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/dashboard/onboarding');
    }

    public function test_ensure_is_event_collaborator_passes_for_active(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create();

        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);

        // staff.checkin route is behind ensure.collaborator - needs Task 3 routes to exist
        $response = $this->actingAs($user)->get("/staff/events/{$event->id}/checkin");

        $response->assertOk();
    }

    public function test_ensure_is_event_collaborator_blocks_revoked(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create();

        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'revoked',
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)->get("/staff/events/{$event->id}/checkin");

        $response->assertForbidden();
    }

    public function test_ensure_is_event_collaborator_blocks_expired(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create();

        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->get("/staff/events/{$event->id}/checkin");

        $response->assertForbidden();
    }
}
