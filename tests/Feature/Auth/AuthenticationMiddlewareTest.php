<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_guests_cannot_check_results(): void
    {
        $response = $this->post('/check', [
            'pin' => '12345678',
            'serial_number' => 'SN12345678',
            'reg_number' => 'EX123456789',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_access_home(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }

    public function test_authenticated_users_redirected_from_guest_routes(): void
    {
        $user = User::factory()->create();

        $loginResponse = $this->actingAs($user)->get('/login');
        $registerResponse = $this->actingAs($user)->get('/register');

        $loginResponse->assertRedirect('/');
        $registerResponse->assertRedirect('/');
    }

    public function test_session_is_regenerated_on_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }
}
