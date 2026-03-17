<?php

namespace Tests\Feature\Web;

use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private function userWithOrganizer(): User
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        Organizer::factory()->create(['user_id' => $user->id]);
        return $user;
    }

    public function test_settings_page_renders(): void
    {
        $user = $this->userWithOrganizer();
        $response = $this->actingAs($user)->get('/dashboard/settings');
        $response->assertStatus(200);
        $response->assertSee('Configurações');
    }

    public function test_unauthenticated_redirected(): void
    {
        $this->get('/dashboard/settings')->assertRedirect('/login');
    }

    public function test_organizer_profile_can_be_updated(): void
    {
        $user = $this->userWithOrganizer();
        $response = $this->actingAs($user)->put('/dashboard/settings/organizer', [
            'name' => 'Novo Nome',
            'description' => 'Descrição atualizada',
            'phone' => '11999999999',
            'document' => '12345678000199',
            'address' => 'Rua Nova, 100',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01310100',
        ]);
        $response->assertRedirect('/dashboard/settings');
        $this->assertDatabaseHas('organizers', ['name' => 'Novo Nome', 'city' => 'São Paulo']);
    }

    public function test_password_can_be_changed(): void
    {
        $user = $this->userWithOrganizer();
        $response = $this->actingAs($user)->put('/dashboard/settings/password', [
            'current_password' => 'password',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);
        $response->assertRedirect('/dashboard/settings');
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_wrong_current_password_rejected(): void
    {
        $user = $this->userWithOrganizer();
        $response = $this->actingAs($user)->put('/dashboard/settings/password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);
        $response->assertSessionHasErrors('current_password');
    }
}
