<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfilePasswordUpdateTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_changes_own_password_with_current_password_confirmation(): void
    {
        $user = User::factory()->create([
            'account_status' => 'active',
            'password' => 'old-password-123',
        ]);

        $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'old-password-123',
            'password' => 'new-password-456',
            'password_confirmation' => 'new-password-456',
        ])->assertRedirect()->assertSessionHas('password_status');

        $this->assertTrue(Hash::check('new-password-456', $user->fresh()->password));
        $this->assertDatabaseHas('audit_logs', [
            'auditable_id' => $user->id,
            'action' => 'update_own_password',
        ]);
    }

    public function test_user_cannot_change_password_with_an_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'account_status' => 'active',
            'password' => 'old-password-123',
        ]);

        $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'incorrect-password',
            'password' => 'new-password-456',
            'password_confirmation' => 'new-password-456',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password-123', $user->fresh()->password));
    }
}
