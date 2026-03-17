<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_login_page_renders(): void
    {
        $response = $this->get('/login');
        $response->assertOk();
        $response->assertSee('Entrar');
    }

    public function test_register_page_renders(): void
    {
        $response = $this->get('/register');
        $response->assertOk();
        $response->assertSee('Criar sua conta');
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_login_redirects_to_intended_url(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password123')]);

        session()->put('url.intended', '/my-tickets');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/my-tickets');
    }
}
