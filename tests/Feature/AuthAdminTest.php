<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret-password'),
        ]);
    }

    public function test_guests_are_redirected_from_admin_to_login(): void
    {
        $this->get(route('admin.index'))->assertRedirect(route('login'));
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Sign in');
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = $this->admin();

        $response = $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('admin.index'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->admin();

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_admin_sees_submitted_messages(): void
    {
        $admin = $this->admin();

        ContactMessage::create([
            'name' => 'Katherine Johnson',
            'email' => 'katherine@example.com',
            'message' => 'Loved the site — let us talk.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.index'))
            ->assertOk()
            ->assertSee('Katherine Johnson')
            ->assertSee('katherine@example.com');
    }

    public function test_admin_can_logout(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
