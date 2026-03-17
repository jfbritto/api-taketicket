<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Models\CustomField;
use App\Models\Event;
use App\Models\Organizer;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $ownerUser;

    private Organizer $ownerOrganizer;

    private Event $event;

    private User $intruderUser;

    private Organizer $intruderOrganizer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create organizer A (owner)
        $this->ownerUser = User::factory()->create();
        $this->ownerOrganizer = Organizer::factory()->create(['user_id' => $this->ownerUser->id]);
        $this->event = Event::factory()->create([
            'organizer_id' => $this->ownerOrganizer->id,
            'status' => EventStatus::DRAFT,
        ]);

        // Create organizer B (intruder)
        $this->intruderUser = User::factory()->create();
        $this->intruderOrganizer = Organizer::factory()->create(['user_id' => $this->intruderUser->id]);
    }

    // --- Event management policies ---

    public function test_other_organizer_cannot_update_event(): void
    {
        $response = $this->actingAs($this->intruderUser)->putJson(
            "/api/v1/organizer/events/{$this->event->id}",
            ['title' => 'Hacked Title']
        );

        $response->assertForbidden();
        $this->assertDatabaseMissing('events', ['title' => 'Hacked Title']);
    }

    public function test_other_organizer_cannot_publish_event(): void
    {
        // Add a ticket type so publish would succeed if authorized
        TicketType::factory()->create(['event_id' => $this->event->id]);

        $response = $this->actingAs($this->intruderUser)->patchJson(
            "/api/v1/organizer/events/{$this->event->id}/publish"
        );

        $response->assertForbidden();
        $this->assertEquals(EventStatus::DRAFT, $this->event->fresh()->status);
    }

    public function test_other_organizer_cannot_cancel_event(): void
    {
        $this->event->update(['status' => EventStatus::PUBLISHED]);

        $response = $this->actingAs($this->intruderUser)->patchJson(
            "/api/v1/organizer/events/{$this->event->id}/cancel"
        );

        $response->assertForbidden();
        $this->assertEquals(EventStatus::PUBLISHED, $this->event->fresh()->status);
    }

    public function test_other_organizer_cannot_view_event_details(): void
    {
        $response = $this->actingAs($this->intruderUser)->getJson(
            "/api/v1/organizer/events/{$this->event->id}"
        );

        $response->assertForbidden();
    }

    // --- Ticket type policies ---

    public function test_other_organizer_cannot_create_ticket_type(): void
    {
        $response = $this->actingAs($this->intruderUser)->postJson(
            "/api/v1/organizer/events/{$this->event->id}/ticket-types",
            [
                'name' => 'Unauthorized Ticket',
                'price' => 50,
                'quantity' => 10,
                'sale_start' => now()->toDateTimeString(),
                'sale_end' => now()->addMonth()->toDateTimeString(),
            ]
        );

        $response->assertForbidden();
        $this->assertDatabaseMissing('ticket_types', ['name' => 'Unauthorized Ticket']);
    }

    public function test_other_organizer_cannot_update_ticket_type(): void
    {
        $ticketType = TicketType::factory()->create(['event_id' => $this->event->id]);

        $response = $this->actingAs($this->intruderUser)->putJson(
            "/api/v1/organizer/events/{$this->event->id}/ticket-types/{$ticketType->id}",
            ['name' => 'Hacked Ticket']
        );

        $response->assertForbidden();
        $this->assertDatabaseMissing('ticket_types', ['name' => 'Hacked Ticket']);
    }

    public function test_other_organizer_cannot_delete_ticket_type(): void
    {
        $ticketType = TicketType::factory()->create(['event_id' => $this->event->id]);

        $response = $this->actingAs($this->intruderUser)->deleteJson(
            "/api/v1/organizer/events/{$this->event->id}/ticket-types/{$ticketType->id}"
        );

        $response->assertForbidden();
        $this->assertDatabaseHas('ticket_types', ['id' => $ticketType->id]);
    }

    // --- Custom field policies ---

    public function test_other_organizer_cannot_list_custom_fields(): void
    {
        CustomField::factory()->count(2)->create(['event_id' => $this->event->id]);

        $response = $this->actingAs($this->intruderUser)->getJson(
            "/api/v1/organizer/events/{$this->event->id}/custom-fields"
        );

        $response->assertForbidden();
    }

    public function test_other_organizer_cannot_create_custom_field(): void
    {
        $response = $this->actingAs($this->intruderUser)->postJson(
            "/api/v1/organizer/events/{$this->event->id}/custom-fields",
            [
                'label' => 'Unauthorized Field',
                'type' => 'text',
                'required' => false,
                'position' => 1,
            ]
        );

        $response->assertForbidden();
        $this->assertDatabaseMissing('custom_fields', ['label' => 'Unauthorized Field']);
    }

    public function test_other_organizer_cannot_update_custom_field(): void
    {
        $field = CustomField::factory()->create(['event_id' => $this->event->id]);

        $response = $this->actingAs($this->intruderUser)->putJson(
            "/api/v1/organizer/events/{$this->event->id}/custom-fields/{$field->id}",
            ['label' => 'Hacked Field']
        );

        $response->assertForbidden();
        $this->assertDatabaseMissing('custom_fields', ['label' => 'Hacked Field']);
    }

    public function test_other_organizer_cannot_delete_custom_field(): void
    {
        $field = CustomField::factory()->create(['event_id' => $this->event->id]);

        $response = $this->actingAs($this->intruderUser)->deleteJson(
            "/api/v1/organizer/events/{$this->event->id}/custom-fields/{$field->id}"
        );

        $response->assertForbidden();
        $this->assertDatabaseHas('custom_fields', ['id' => $field->id]);
    }

    // --- Dashboard policies ---

    public function test_other_organizer_cannot_access_dashboard_orders(): void
    {
        $response = $this->actingAs($this->intruderUser)->getJson(
            "/api/v1/organizer/events/{$this->event->id}/orders"
        );

        $response->assertForbidden();
    }

    public function test_other_organizer_cannot_access_dashboard_participants(): void
    {
        $response = $this->actingAs($this->intruderUser)->getJson(
            "/api/v1/organizer/events/{$this->event->id}/participants"
        );

        $response->assertForbidden();
    }

    public function test_other_organizer_cannot_access_dashboard_tickets(): void
    {
        $response = $this->actingAs($this->intruderUser)->getJson(
            "/api/v1/organizer/events/{$this->event->id}/tickets"
        );

        $response->assertForbidden();
    }

    public function test_other_organizer_cannot_access_dashboard_summary(): void
    {
        $response = $this->actingAs($this->intruderUser)->getJson(
            "/api/v1/organizer/events/{$this->event->id}/summary"
        );

        $response->assertForbidden();
    }

    // --- Owner can do everything ---

    public function test_owner_can_manage_own_event(): void
    {
        // Owner can view
        $response = $this->actingAs($this->ownerUser)->getJson(
            "/api/v1/organizer/events/{$this->event->id}"
        );
        $response->assertOk();

        // Owner can update
        $response = $this->actingAs($this->ownerUser)->putJson(
            "/api/v1/organizer/events/{$this->event->id}",
            ['title' => 'Updated Title']
        );
        $response->assertOk();
    }

    public function test_owner_can_manage_ticket_types(): void
    {
        $response = $this->actingAs($this->ownerUser)->postJson(
            "/api/v1/organizer/events/{$this->event->id}/ticket-types",
            [
                'name' => 'Owner Ticket',
                'price' => 25,
                'quantity' => 50,
                'sale_start' => now()->toDateTimeString(),
                'sale_end' => now()->addMonth()->toDateTimeString(),
            ]
        );
        $response->assertStatus(201);
    }

    public function test_owner_can_manage_custom_fields(): void
    {
        $response = $this->actingAs($this->ownerUser)->postJson(
            "/api/v1/organizer/events/{$this->event->id}/custom-fields",
            [
                'label' => 'Owner Field',
                'type' => 'text',
                'required' => false,
                'position' => 1,
            ]
        );
        $response->assertStatus(201);
    }

    // --- User without organizer profile ---

    public function test_user_without_organizer_cannot_manage_events(): void
    {
        $plainUser = User::factory()->create();

        $response = $this->actingAs($plainUser)->getJson(
            "/api/v1/organizer/events/{$this->event->id}"
        );

        $response->assertForbidden();
    }
}
