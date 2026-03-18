<?php

namespace Tests\Feature\Web;

use App\Mail\CollaboratorAddedMail;
use App\Mail\CollaboratorInvitedMail;
use App\Models\Event;
use App\Models\EventCollaborator;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

    public function test_organizer_can_invite_new_user_as_pending(): void
    {
        Mail::fake();

        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(8),
        ]);

        $response = $this->actingAs($organizer->user)
            ->post(route('dashboard.collaborators.store', $event), [
                'email' => 'staff@example.com',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('event_collaborators', [
            'event_id' => $event->id,
            'invitee_email' => 'staff@example.com',
            'status' => 'pending',
        ]);
        Mail::assertQueued(CollaboratorInvitedMail::class);
    }

    public function test_organizer_can_invite_existing_user_as_active(): void
    {
        Mail::fake();

        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(8),
        ]);
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($organizer->user)
            ->post(route('dashboard.collaborators.store', $event), [
                'email' => 'existing@example.com',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('event_collaborators', [
            'event_id' => $event->id,
            'invitee_email' => 'existing@example.com',
            'user_id' => $existingUser->id,
            'status' => 'active',
        ]);
        Mail::assertQueued(CollaboratorAddedMail::class);
    }

    public function test_cannot_invite_to_past_event(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'start_date' => now()->subDays(2),
            'end_date' => now()->subDay(),
        ]);

        $response = $this->actingAs($organizer->user)
            ->post(route('dashboard.collaborators.store', $event), [
                'email' => 'staff@example.com',
            ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('event_collaborators', ['event_id' => $event->id]);
    }

    public function test_cannot_invite_organizers_own_email(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
        ]);

        $response = $this->actingAs($organizer->user)
            ->post(route('dashboard.collaborators.store', $event), [
                'email' => $organizer->user->email,
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_cannot_invite_already_invited_email(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
        ]);

        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'invitee_email' => 'staff@example.com',
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($organizer->user)
            ->post(route('dashboard.collaborators.store', $event), [
                'email' => 'staff@example.com',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_non_organizer_cannot_invite(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $otherOrganizer = Organizer::factory()->create();

        $response = $this->actingAs($otherOrganizer->user)
            ->post(route('dashboard.collaborators.store', $event), [
                'email' => 'staff@example.com',
            ]);

        $response->assertForbidden();
    }

    public function test_organizer_can_revoke_collaborator(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $collaborator = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($organizer->user)
            ->delete(route('dashboard.collaborators.destroy', [$event, $collaborator]));

        $response->assertRedirect();
        $this->assertDatabaseHas('event_collaborators', [
            'id' => $collaborator->id,
            'status' => 'revoked',
        ]);
    }

    public function test_login_redirects_organizer_to_dashboard(): void
    {
        $organizer = Organizer::factory()->create();

        $response = $this->post('/login', [
            'email' => $organizer->user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
    }

    public function test_login_redirects_single_collaborator_to_staff_checkin(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create(['password' => \Illuminate\Support\Facades\Hash::make('password')]);

        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('staff.checkin', $event));
    }

    public function test_login_redirects_multiple_collaborations_to_staff_index(): void
    {
        $user = User::factory()->create(['password' => \Illuminate\Support\Facades\Hash::make('password')]);

        foreach (range(1, 2) as $_) {
            $organizer = Organizer::factory()->create();
            $event = Event::factory()->create(['organizer_id' => $organizer->id]);
            EventCollaborator::factory()->create([
                'event_id' => $event->id,
                'inviter_user_id' => $organizer->user_id,
                'user_id' => $user->id,
                'status' => 'active',
                'expires_at' => now()->addDay(),
            ]);
        }

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('staff.index'));
    }

    public function test_login_with_no_access_redirects_home_with_error(): void
    {
        $user = User::factory()->create(['password' => \Illuminate\Support\Facades\Hash::make('password')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated(); // user stays logged in
    }

    public function test_login_respects_url_intended(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create(['password' => \Illuminate\Support\Facades\Hash::make('password')]);

        EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);

        $intendedUrl = route('staff.checkin', $event);

        $response = $this->withSession(['url.intended' => $intendedUrl])
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $response->assertRedirect($intendedUrl);
    }

    public function test_register_activates_pending_collaborator(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $collaborator = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'invitee_email' => 'newstaff@example.com',
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->withSession([
            'pending_collaborator_id' => $collaborator->id,
            'pending_collaborator_email' => 'newstaff@example.com',
        ])->post('/register', [
            'name' => 'New Staff',
            'email' => 'newstaff@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('staff.checkin', $event));
        $this->assertDatabaseHas('event_collaborators', [
            'id' => $collaborator->id,
            'status' => 'active',
        ]);
    }

    public function test_register_without_pending_collaborator_goes_to_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
    }

    public function test_staff_index_redirects_organizer_to_dashboard(): void
    {
        $organizer = Organizer::factory()->create();

        $response = $this->actingAs($organizer->user)->get('/staff');

        $response->assertRedirect('/dashboard');
    }

    public function test_staff_index_shows_active_events_for_collaborator(): void
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

        $response = $this->actingAs($user)->get('/staff');

        $response->assertOk();
        $response->assertSee($event->title);
    }
}
