<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_staff_login_screen_can_be_rendered()
    {
        $response = $this->get('/staff-login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_internet_id_and_mocked_service()
    {
        Http::fake([
            '*' => Http::response(base64_encode(json_encode(['error_code' => 0, 'message' => 'Success'])), 200),
        ]);

        $user = User::factory()->create([
            'internet_id' => '10000001',
        ]);

        $response = $this->post('/login', [
            'internet_id' => '10000001',
            'password' => 'secret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_users_cannot_authenticate_with_nonexistent_internet_id()
    {
        $response = $this->post('/login', [
            'internet_id' => '99999999',
            'password' => 'secret123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('internet_id');
    }

    public function test_users_cannot_authenticate_with_invalid_password()
    {
        Http::fake([
            '*' => Http::response(base64_encode(json_encode(['error_code' => 1, 'message' => 'Invalid password'])), 200),
        ]);

        $user = User::factory()->create([
            'internet_id' => '10000002',
        ]);

        $response = $this->post('/login', [
            'internet_id' => '10000002',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('password');
    }

    public function test_users_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
