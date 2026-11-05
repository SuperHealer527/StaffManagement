<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_login_page_links_to_password_reset_request(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('password.request'));
    }

    public function test_active_login_user_receives_password_reset_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create(['account_status' => 'active', 'role' => 'staff']);

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_suspended_and_temporary_users_do_not_receive_password_reset_notification(): void
    {
        Notification::fake();
        $suspended = User::factory()->create(['account_status' => 'suspended', 'role' => 'staff']);
        $temporary = User::factory()->create(['account_status' => 'no_login', 'role' => 'temporary']);

        $this->post(route('password.email'), ['email' => $suspended->email])->assertSessionHas('status');
        $this->post(route('password.email'), ['email' => $temporary->email])->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_valid_token_resets_password_and_invalidates_token(): void
    {
        $user = User::factory()->create(['account_status' => 'active', 'role' => 'staff']);
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('newpass123', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_password_requires_letters_numbers_and_confirmation(): void
    {
        $user = User::factory()->create(['account_status' => 'active', 'role' => 'staff']);

        $this->from(route('password.reset', ['token' => 'invalid', 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => 'invalid',
                'email' => $user->email,
                'password' => '12345678',
                'password_confirmation' => 'different1',
            ])
            ->assertSessionHasErrors('password');
    }
}
