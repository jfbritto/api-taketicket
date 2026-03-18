<?php

namespace Tests\Feature\Web;

use App\Models\Event;
use App\Models\EventCollaborator;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function makeSignedUrl(EventCollaborator $collaborator): string
    {
        return URL::temporarySignedRoute('invitation.accept', now()->addDays(7), ['collaborator' => $collaborator->id]);
    }

    public function test_expired_collaborator_shows_expired_view(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $collaborator = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'status' => 'pending',
            'expires_at' => now()->subDay(),
        ]);

        $url = $this->makeSignedUrl($collaborator);
        $response = $this->get($url);

        $response->assertSee('expirou');
    }

    public function test_revoked_collaborator_shows_revoked_view(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $collaborator = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'status' => 'revoked',
            'expires_at' => now()->addDay(),
        ]);

        $url = $this->makeSignedUrl($collaborator);
        $response = $this->get($url);

        $response->assertSee('cancelado');
    }

    public function test_pending_unauthenticated_sets_session_and_redirects_to_register(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $collaborator = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'invitee_email' => 'newuser@example.com',
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);

        $url = $this->makeSignedUrl($collaborator);
        $response = $this->get($url);

        $response->assertRedirect('/register');
        $response->assertSessionHas('pending_collaborator_id', $collaborator->id);
        $response->assertSessionHas('pending_collaborator_email', 'newuser@example.com');
    }

    public function test_pending_authenticated_shows_already_logged_in(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $collaborator = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);
        $user = User::factory()->create();

        $url = $this->makeSignedUrl($collaborator);
        $response = $this->actingAs($user)->get($url);

        $response->assertSee('logado');
    }

    public function test_active_authenticated_matching_email_redirects_to_checkin(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create(['email' => 'staff@example.com']);
        $collaborator = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'invitee_email' => 'staff@example.com',
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);

        $url = $this->makeSignedUrl($collaborator);
        $response = $this->actingAs($user)->get($url);

        $response->assertRedirect(route('staff.checkin', $event));
    }

    public function test_active_authenticated_wrong_email_shows_wrong_account(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $user = User::factory()->create(['email' => 'other@example.com']);
        $collaborator = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'invitee_email' => 'staff@example.com',
            'user_id' => null,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);

        $url = $this->makeSignedUrl($collaborator);
        $response = $this->actingAs($user)->get($url);

        $response->assertSee('outro endereço');
    }

    public function test_active_unauthenticated_sets_intended_and_redirects_to_login(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $collaborator = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'invitee_email' => 'staff@example.com',
            'user_id' => null,
            'status' => 'active',
            'expires_at' => now()->addDay(),
        ]);

        $url = $this->makeSignedUrl($collaborator);
        $response = $this->get($url);

        $response->assertRedirect('/login');
        $response->assertSessionHas('url.intended', route('staff.checkin', $event));
    }

    public function test_tampered_url_returns_403(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $collaborator = EventCollaborator::factory()->create([
            'event_id' => $event->id,
            'inviter_user_id' => $organizer->user_id,
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);

        // Access without signature
        $response = $this->get("/invitation/{$collaborator->id}");

        $response->assertForbidden();
    }
}
