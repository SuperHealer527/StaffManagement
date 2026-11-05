<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Interview;
use App\Models\PhoneLog;
use App\Models\SecureActionToken;
use App\Models\User;
use App\Notifications\ActionLinkNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OperationsManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_employee_views_real_filtered_proposal_board(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        Client::factory()->create(['name' => '提案対象法人', 'type' => 'corporation', 'proposal_status' => 'proposing']);
        Client::factory()->create(['name' => '対象外行政', 'type' => 'government', 'proposal_status' => 'won']);

        $this->actingAs($employee)->get(route('sales.proposals', ['keyword' => '対象', 'type' => 'corporation']))
            ->assertOk()->assertSee('提案対象法人')->assertDontSee('対象外行政')->assertSee('提案中');
    }

    public function test_employee_creates_interview_with_three_candidates_and_sends_link(): void
    {
        Notification::fake();
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $subject = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);
        $candidateTimes = [now()->addDays(2)->format('Y-m-d H:i:s'), now()->addDays(3)->format('Y-m-d H:i:s'), now()->addDays(4)->format('Y-m-d H:i:s')];

        $this->actingAs($employee)->post(route('interviews.store'), [
            'subject_user_id' => $subject->id,
            'manager_id' => $employee->id,
            'type' => 'individual_guidance',
            'method' => 'in_person',
            'location' => '事務所',
            'candidate_times' => $candidateTimes,
        ])->assertRedirect();

        $interview = Interview::query()->firstOrFail();
        $this->assertCount(3, $interview->candidate_times);
        $this->assertDatabaseHas('secure_action_tokens', ['actionable_id' => $interview->id, 'purpose' => 'interview_selection']);
        $this->assertDatabaseHas('notification_logs', ['notifiable_context_id' => $interview->id, 'type' => 'interview_candidates', 'status' => 'sent']);
        Notification::assertSentTo($subject, ActionLinkNotification::class);
    }

    public function test_interview_rejects_more_than_three_candidate_times(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $subject = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($employee)->post(route('interviews.store'), [
            'subject_user_id' => $subject->id,
            'manager_id' => $employee->id,
            'type' => 'regular',
            'method' => 'online',
            'candidate_times' => collect(range(1, 4))->map(fn ($day) => now()->addDays($day)->format('Y-m-d H:i:s'))->all(),
        ])->assertSessionHasErrors('candidate_times');
    }

    public function test_public_interview_candidate_selection_updates_schedule(): void
    {
        $interview = Interview::factory()->create(['candidate_times' => [now()->addDays(2)->toDateTimeString(), now()->addDays(3)->toDateTimeString()], 'scheduled_at' => null]);
        $token = 'interview-choice-token';
        SecureActionToken::query()->create([
            'user_id' => $interview->subject_user_id,
            'actionable_type' => Interview::class,
            'actionable_id' => $interview->id,
            'purpose' => 'interview_selection',
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDay(),
        ]);

        $selected = $interview->candidate_times[1];
        $this->get(route('public-actions.show', $token))->assertOk()->assertSee($interview->manager->name);
        $this->post(route('public-actions.update', $token), ['selected_at' => $selected])->assertRedirect();
        $this->assertSame($selected, $interview->fresh()->scheduled_at->toDateTimeString());
    }

    public function test_employee_updates_interview_record_and_view_is_audited(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $interview = Interview::factory()->create();

        $this->actingAs($employee)->get(route('interviews.index', ['interview_id' => $interview->id]))->assertOk();
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $interview->id, 'action' => 'view_interview_record']);

        $this->actingAs($employee)->patch(route('interviews.update', $interview), [
            'type' => 'evaluation', 'method' => 'online', 'location' => 'Zoom',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'content' => '面談内容', 'next_tasks' => '次回課題', 'next_timing' => '来月',
        ])->assertRedirect();
        $this->assertDatabaseHas('interviews', ['id' => $interview->id, 'content' => '面談内容']);
    }

    public function test_semi_employee_creates_filters_and_completes_phone_log(): void
    {
        $semi = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);
        $recipient = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);

        $this->actingAs($semi)->post(route('phone-logs.store'), [
            'called_at' => now()->format('Y-m-d H:i:s'), 'caller_name' => '森 様', 'company' => '確認会社',
            'phone' => '03-1234-5678', 'purpose' => '問い合わせ', 'content' => '折り返し希望',
            'recipient_id' => $recipient->id, 'status' => 'callback_required',
        ])->assertRedirect();
        $phoneLog = PhoneLog::query()->firstOrFail();

        $this->actingAs($recipient)->get(route('phone-logs.index', ['filter' => 'mine']))->assertOk()->assertSee('確認会社');
        $this->actingAs($semi)->patch(route('phone-logs.complete', $phoneLog))->assertRedirect();
        $this->assertDatabaseHas('phone_logs', ['id' => $phoneLog->id, 'status' => 'completed']);
    }

    public function test_phone_message_to_semi_employee_is_sent_by_their_notification_method(): void
    {
        Notification::fake();
        $receiver = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $recipient = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);

        $this->actingAs($receiver)->post(route('phone-logs.store'), [
            'called_at' => now()->format('Y-m-d H:i:s'),
            'caller_name' => '連絡者',
            'purpose' => '問い合わせ',
            'content' => '折り返してください。',
            'recipient_id' => $recipient->id,
            'status' => 'callback_required',
        ])->assertRedirect();

        Notification::assertSentTo($recipient, ActionLinkNotification::class);
        $this->assertDatabaseHas('notification_logs', ['user_id' => $recipient->id, 'type' => 'phone_message', 'status' => 'sent']);
    }
}
