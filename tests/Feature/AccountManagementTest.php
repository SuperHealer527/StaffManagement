<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AccountInvitationNotification;
use App\Notifications\ActionLinkNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_employee_creates_login_account_and_sends_invitation(): void
    {
        Notification::fake();
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);

        $this->actingAs($employee)->postJson(route('ui-actions.store', 'account-create'), [
            'name' => '新規スタッフ',
            'email' => 'new-staff@example.com',
            'role' => 'staff',
        ])->assertOk();

        $createdUser = User::query()->where('email', 'new-staff@example.com')->firstOrFail();
        $this->assertSame('invited', $createdUser->account_status);
        Notification::assertSentTo($createdUser, AccountInvitationNotification::class);
        $this->assertDatabaseHas('secure_action_tokens', ['user_id' => $createdUser->id, 'purpose' => 'first_password']);
        $this->assertDatabaseHas('notification_logs', ['user_id' => $createdUser->id, 'status' => 'sent']);
    }

    public function test_employee_creates_temporary_account_and_sends_line_connection_link(): void
    {
        Notification::fake();
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);

        $this->actingAs($employee)->postJson(route('ui-actions.store', 'account-create'), [
            'name' => '単発スタッフ',
            'email' => 'temporary@example.com',
            'role' => 'temporary',
        ])->assertOk();

        $temporary = User::query()->where('email', 'temporary@example.com')->firstOrFail();
        $this->assertSame('no_login', $temporary->account_status);
        Notification::assertSentTo($temporary, ActionLinkNotification::class);
        $this->assertDatabaseMissing('secure_action_tokens', ['user_id' => $temporary->id, 'purpose' => 'first_password']);
        $this->assertDatabaseHas('secure_action_tokens', ['user_id' => $temporary->id, 'purpose' => 'line_connect']);
    }

    public function test_changing_temporary_account_to_login_role_sends_invitation(): void
    {
        Notification::fake();
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $temporary = User::factory()->create(['role' => 'temporary', 'account_status' => 'no_login']);

        $this->actingAs($employee)->postJson(route('ui-actions.store', 'account-role'), [
            'user_id' => $temporary->id,
            'role' => 'trainer',
        ])->assertOk();

        $temporary->refresh();
        $this->assertSame('trainer', $temporary->role);
        $this->assertSame('invited', $temporary->account_status);
        Notification::assertSentTo($temporary, AccountInvitationNotification::class);
    }

    public function test_employee_can_stop_and_resume_temporary_account(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $temporary = User::factory()->create(['role' => 'temporary', 'account_status' => 'no_login']);

        $this->actingAs($employee)->postJson(route('ui-actions.store', 'account-status'), [
            'user_id' => $temporary->id,
            'account_status' => 'suspended',
        ])->assertOk();
        $this->assertDatabaseHas('users', ['id' => $temporary->id, 'account_status' => 'suspended']);

        $this->actingAs($employee)->postJson(route('ui-actions.store', 'account-status'), [
            'user_id' => $temporary->id,
            'account_status' => 'no_login',
        ])->assertOk();
        $this->assertDatabaseHas('users', ['id' => $temporary->id, 'account_status' => 'no_login']);
    }

    public function test_employee_cannot_change_own_role_or_status(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);

        $this->actingAs($employee)->postJson(route('ui-actions.store', 'account-role'), [
            'user_id' => $employee->id,
            'role' => 'staff',
        ])->assertUnprocessable();

        $this->actingAs($employee)->postJson(route('ui-actions.store', 'account-status'), [
            'user_id' => $employee->id,
            'account_status' => 'suspended',
        ])->assertUnprocessable();
    }

    public function test_account_screen_renders_database_accounts_and_creation_form(): void
    {
        $employee = User::factory()->create(['name' => '管理社員', 'role' => 'employee', 'account_status' => 'active']);
        User::factory()->create(['name' => '画面確認スタッフ', 'role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($employee)->get(route('screens.show', 'accounts'))
            ->assertOk()
            ->assertSee('画面確認スタッフ')
            ->assertSee('data-account-create-form', false)
            ->assertSee('data-account-role', false)
            ->assertSee('data-account-state', false);
    }
}
