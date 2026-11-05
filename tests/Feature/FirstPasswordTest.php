<?php

namespace Tests\Feature;

use App\Models\SecureActionToken;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FirstPasswordTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_invited_user_sets_password_and_is_redirected_to_profile(): void
    {
        $user = User::factory()->create(['role' => 'staff', 'account_status' => 'invited']);
        SecureActionToken::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'first_password',
            'token_hash' => hash('sha256', 'first-password-token'),
            'expires_at' => now()->addDay(),
        ]);

        $this->get(route('public-actions.show', 'first-password-token'))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee($user->email);

        $this->post(route('public-actions.update', 'first-password-token'), [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('screens.show', 'profile'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame('active', $user->fresh()->account_status);
        $this->assertTrue(Hash::check('password123', $user->fresh()->password));
    }

    public function test_action_link_expires_thirty_minutes_after_first_open(): void
    {
        $user = User::factory()->create(['role' => 'staff', 'account_status' => 'invited']);
        SecureActionToken::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'first_password',
            'token_hash' => hash('sha256', 'expired-open-token'),
            'expires_at' => now()->addDay(),
            'opened_at' => now()->subMinutes(31),
        ]);

        $this->get(route('public-actions.show', 'expired-open-token'))->assertGone();
        $this->post(route('public-actions.update', 'expired-open-token'), [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertForbidden();
    }
}
