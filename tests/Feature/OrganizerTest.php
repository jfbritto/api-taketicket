<?php

namespace Tests\Feature;

use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_organizer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/organizers', [
            'name' => 'Minha Empresa',
            'description' => 'Organizadora de eventos',
            'document' => '12345678000190',
            'phone' => '11999999999',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Minha Empresa']);

        $this->assertDatabaseHas('organizers', ['user_id' => $user->id, 'name' => 'Minha Empresa']);
    }

    public function test_user_cannot_create_two_organizers(): void
    {
        $user = User::factory()->create();
        Organizer::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson('/api/v1/organizers', [
            'name' => 'Outra Empresa',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_get_organizer_profile(): void
    {
        $user = User::factory()->create();
        $organizer = Organizer::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/v1/organizers/me');

        $response->assertOk()
            ->assertJsonFragment(['name' => $organizer->name]);
    }

    public function test_user_can_update_organizer(): void
    {
        $user = User::factory()->create();
        Organizer::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson('/api/v1/organizers/me', [
            'name' => 'Nome Atualizado',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Nome Atualizado']);
    }

    public function test_create_organizer_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/organizers', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_user_without_organizer_gets_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/organizers/me');

        $response->assertStatus(404);
    }
}
