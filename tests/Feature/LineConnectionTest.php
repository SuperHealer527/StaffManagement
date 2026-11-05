<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LineConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_connects_and_disconnects_line_from_profile(): void
    {
        config([
            'services.line.login_channel_id' => 'channel-id',
            'services.line.login_channel_secret' => 'channel-secret',
        ]);
        $user = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($user)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('LINE連携・通知方法')
            ->assertSee('LINEと連携する');

        $this->actingAs($user)->post(route('line.connect.start'))
            ->assertRedirectContains('https://access.line.me/oauth2/v2.1/authorize');
        $connection = session('line_connection');

        Http::fakeSequence()
            ->push(['access_token' => 'access-token'], 200)
            ->push(['userId' => 'line-user-123', 'displayName' => '利用者'], 200);
        $this->actingAs($user)->get(route('line.connect.callback', [
            'state' => $connection['state'],
            'code' => 'authorization-code',
        ]))->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'line_user_id' => 'line-user-123',
            'notification_method' => 'line',
        ]);

        $this->actingAs($user)->delete(route('line.connect.disconnect'))->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'line_user_id' => null,
            'notification_method' => 'email',
        ]);
    }

    public function test_line_connection_shows_configuration_error_when_credentials_are_missing(): void
    {
        $user = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($user)->from(route('profile.edit'))->post(route('line.connect.start'))
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('line');
    }
}
