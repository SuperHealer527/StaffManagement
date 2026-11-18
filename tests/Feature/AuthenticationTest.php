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
        $this->get('/login')
            ->assertOk()
            ->assertSee('ハイクラス業務管理システム')
            ->assertDontSee('ログアウト')
            ->assertSee('data-scroll-to-top', false);
    }

    public function test_active_user_can_log_in(): void
    {
        $user = User::factory()->create(['password' => 'password123', 'account_status' => 'active']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create(['account_status' => 'active']);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_suspended_user_cannot_log_in(): void
    {
        $user = User::factory()->create(['password' => 'password123', 'account_status' => 'suspended']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_temporary_worker_cannot_log_in_even_if_account_is_active(): void
    {
        $temporary = User::factory()->create([
            'role' => 'temporary',
            'password' => 'password123',
            'account_status' => 'active',
        ]);

        $this->post('/login', ['email' => $temporary->email, 'password' => 'password123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_suspended_authenticated_user_is_logged_out_on_the_next_request(): void
    {
        $user = User::factory()->create(['account_status' => 'active']);
        $this->actingAs($user);
        $user->update(['account_status' => 'suspended']);

        $this->get(route('dashboard'))->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
