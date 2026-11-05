<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\SecureActionToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_uses_the_exact_static_screen(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('HIGHCLASS')
            ->assertSee('ハイクラス業務管理システム')
            ->assertSee('ログイン');
    }

    public function test_employee_can_open_every_authenticated_static_screen(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $screens = [
            'staff-home', 'semi-home', 'assignments', 'event-form', 'event-copy', 'event-detail',
            'assignment-response', 'attendance-confirmed', 'report-submit', 'payments', 'profit-loss',
            'payment-statement', 'profile', 'reports', 'clients', 'client-detail', 'events', 'staff',
            'staff-detail', 'shortages', 'accounts', 'notifications', 'my-requests', 'first-password',
            'mobile-areas', 'sales-home', 'proposal-board', 'meeting-history', 'interviews',
            'interview-select', 'phone-log', 'line-connect', 'temporary-bank',
        ];

        foreach ($screens as $screen) {
            $this->actingAs($employee)->get(route('screens.show', $screen))->assertOk();
        }
    }

    public function test_secure_assignment_response_uses_static_screen_and_saves_response(): void
    {
        $assignment = Assignment::factory()->create(['status' => 'requested']);
        SecureActionToken::query()->create([
            'user_id' => $assignment->user_id,
            'actionable_type' => Assignment::class,
            'actionable_id' => $assignment->id,
            'purpose' => 'assignment_response',
            'token_hash' => hash('sha256', 'response-token'),
            'expires_at' => now()->addDay(),
        ]);

        $this->get(route('public-actions.show', 'response-token'))
            ->assertOk()
            ->assertSee('このお仕事を引き受けられますか?')
            ->assertSee('承諾する');

        $this->post(route('public-actions.update', 'response-token'), ['response' => 'accepted'])
            ->assertRedirect(route('public-actions.complete', 'response-token'));

        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'status' => 'accepted']);
    }

    public function test_employee_can_create_and_complete_a_phone_log(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $recipient = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);

        $response = $this->actingAs($employee)->postJson(route('ui-actions.store', 'phone-log-create'), [
            'called_at' => now()->toIso8601String(),
            'caller_name' => '森 様',
            'company' => 'サンプル観光協会',
            'phone' => '03-1234-5678',
            'purpose' => 'お仕事の依頼・相談',
            'content' => '折り返し希望です。',
            'recipient_id' => $recipient->id,
            'status' => 'callback_required',
        ]);

        $response->assertOk()->assertJson(['message' => '保存しました。']);
        $phoneLogId = (int) $this->app['db']->table('phone_logs')->value('id');

        $this->actingAs($employee)->postJson(route('ui-actions.store', 'phone-log-complete'), [
            'phone_log_id' => $phoneLogId,
        ])->assertOk();

        $this->assertDatabaseHas('phone_logs', ['id' => $phoneLogId, 'status' => 'completed']);
    }
}
