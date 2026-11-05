<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\SecureActionToken;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PublicActionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_assignment_request_can_be_viewed_and_answered_without_login(): void
    {
        $assignment = Assignment::factory()->create(['status' => 'requested']);
        $plainToken = 'safe-response-token';
        SecureActionToken::query()->create([
            'user_id' => $assignment->user_id,
            'actionable_type' => Assignment::class,
            'actionable_id' => $assignment->id,
            'purpose' => 'assignment_response',
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addDay(),
        ]);

        $this->get(route('public-actions.show', $plainToken))
            ->assertOk()
            ->assertSee('お仕事の依頼')
            ->assertSee($assignment->event->name)
            ->assertSee('このお仕事を引き受けられますか?');

        $this->post(route('public-actions.update', $plainToken), ['response' => 'accepted'])
            ->assertRedirect(route('public-actions.complete', $plainToken));

        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'status' => 'accepted']);
        $this->assertNull(SecureActionToken::query()->firstOrFail()->used_at);

        $this->post(route('public-actions.update', $plainToken), ['response' => 'unknown'])
            ->assertRedirect(route('public-actions.complete', $plainToken));
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'status' => 'unknown']);
    }

    public function test_expired_public_action_link_is_rejected(): void
    {
        $assignment = Assignment::factory()->create();
        SecureActionToken::query()->create([
            'user_id' => $assignment->user_id,
            'actionable_type' => Assignment::class,
            'actionable_id' => $assignment->id,
            'purpose' => 'attendance_confirmation',
            'token_hash' => hash('sha256', 'expired-token'),
            'expires_at' => now()->subMinute(),
        ]);

        $this->get(route('public-actions.show', 'expired-token'))->assertStatus(410);
    }

    public function test_public_action_link_expires_thirty_minutes_after_it_is_opened(): void
    {
        $assignment = Assignment::factory()->create(['status' => 'requested']);
        SecureActionToken::query()->create([
            'user_id' => $assignment->user_id,
            'actionable_type' => Assignment::class,
            'actionable_id' => $assignment->id,
            'purpose' => 'assignment_response',
            'token_hash' => hash('sha256', 'opened-token'),
            'expires_at' => now()->addDay(),
        ]);

        $this->get(route('public-actions.show', 'opened-token'))->assertOk();
        $this->travel(31)->minutes();
        $this->get(route('public-actions.show', 'opened-token'))->assertGone();
    }

    public function test_first_password_link_activates_the_invited_account(): void
    {
        $user = User::factory()->create(['account_status' => 'invited']);
        $plainToken = 'password-token';
        SecureActionToken::query()->create([
            'user_id' => $user->id,
            'actionable_type' => User::class,
            'actionable_id' => $user->id,
            'purpose' => 'first_password',
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addDay(),
        ]);

        $this->post(route('public-actions.update', $plainToken), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect(route('screens.show', 'profile'));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'account_status' => 'active']);
        $this->assertAuthenticatedAs($user);
    }
}
