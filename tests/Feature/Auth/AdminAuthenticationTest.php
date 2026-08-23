<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_screen_can_be_rendered()
    {
        $response = $this->get('/admin-login');

        $response->assertStatus(200);
    }

    public function test_admin_can_authenticate_with_valid_credentials()
    {
        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@ru.ac.bd',
            'password' => Hash::make('secret_admin_pass'),
        ]);

        $response = $this->post('/admin-login', [
            'email' => 'admin@ru.ac.bd',
            'password' => 'secret_admin_pass',
        ]);

        $this->assertAuthenticatedAs($admin, 'admin');
        $response->assertRedirect('/admin-dashboard');
    }

    public function test_admin_cannot_authenticate_with_invalid_password()
    {
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@ru.ac.bd',
            'password' => Hash::make('secret_admin_pass'),
        ]);

        $response = $this->post('/admin-login', [
            'email' => 'admin@ru.ac.bd',
            'password' => 'wrong_pass',
        ]);

        $this->assertGuest('admin');
        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_logout()
    {
        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@ru.ac.bd',
            'password' => Hash::make('secret_admin_pass'),
        ]);

        $response = $this->actingAs($admin, 'admin')->post('/admin-logout');

        $this->assertGuest('admin');
        $response->assertRedirect('/admin-login');
    }
}
