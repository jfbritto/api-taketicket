<?php

namespace Tests\Feature;

use App\Models\CustomField;
use App\Models\Event;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\ParticipantFieldValue;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomFieldTest extends TestCase
{
    use RefreshDatabase;

    private function createEventWithOrganizer(): array
    {
        $user = User::factory()->create();
        $organizer = Organizer::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        return [$user, $event];
    }

    public function test_organizer_can_create_custom_field(): void
    {
        [$user, $event] = $this->createEventWithOrganizer();

        $response = $this->actingAs($user)->postJson("/api/v1/organizer/events/{$event->id}/custom-fields", [
            'label' => 'Tamanho da camiseta',
            'type' => 'select',
            'required' => true,
            'options' => ['P', 'M', 'G', 'GG'],
            'position' => 1,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['label' => 'Tamanho da camiseta']);
    }

    public function test_organizer_can_list_custom_fields(): void
    {
        [$user, $event] = $this->createEventWithOrganizer();
        CustomField::factory()->count(3)->create(['event_id' => $event->id]);

        $response = $this->actingAs($user)->getJson("/api/v1/organizer/events/{$event->id}/custom-fields");

        $response->assertOk();
        $this->assertCount(3, $response->json());
    }

    public function test_organizer_can_delete_custom_field_without_values(): void
    {
        [$user, $event] = $this->createEventWithOrganizer();
        $field = CustomField::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($user)->deleteJson(
            "/api/v1/organizer/events/{$event->id}/custom-fields/{$field->id}"
        );

        $response->assertStatus(204);
    }

    public function test_cannot_delete_custom_field_with_values(): void
    {
        [$user, $event] = $this->createEventWithOrganizer();
        $field = CustomField::factory()->create(['event_id' => $event->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);
        $participant = Participant::factory()->create(['ticket_id' => $ticket->id]);
        ParticipantFieldValue::factory()->create([
            'participant_id' => $participant->id,
            'custom_field_id' => $field->id,
            'value' => 'G',
        ]);

        $response = $this->actingAs($user)->deleteJson(
            "/api/v1/organizer/events/{$event->id}/custom-fields/{$field->id}"
        );

        $response->assertStatus(422);
    }
}
