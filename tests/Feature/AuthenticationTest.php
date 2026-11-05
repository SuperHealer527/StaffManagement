<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk()->assertSee('ハイクラス業務管理システム');
    }

    public function test_active_user_can_log_in(): void
    {
        $user = User::factory()->create(['password' => 'password123', 'account_status' => 'active']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_suspended_user_cannot_log_in(): void
    {
        $user = User::factory()->create(['password' => 'password123', 'account_status' => 'suspended']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
