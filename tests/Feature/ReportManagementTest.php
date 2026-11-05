<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\ReportSubmission;
use App\Models\SecureActionToken;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ReportManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_representative_can_submit_dynamic_report_without_login(): void
    {
        $event = Event::factory()->create(['report_required' => true, 'name' => '報告対象イベント']);
        $question = $event->reportQuestions()->create(['type' => 'text', 'question' => '依頼先への報告', 'display_order' => 0]);
        $assignment = Assignment::factory()->create(['event_id' => $event->id, 'status' => 'confirmed', 'is_report_representative' => true]);
        $plainToken = 'report-submission-token';
        SecureActionToken::query()->create([
            'user_id' => $assignment->user_id,
            'actionable_type' => Assignment::class,
            'actionable_id' => $assignment->id,
            'purpose' => 'report_submission',
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addDay(),
        ]);

        $this->get(route('public-actions.show', $plainToken))
            ->assertOk()
            ->assertSee('報告対象イベント')
            ->assertSee('依頼先への報告');

        $this->post(route('public-actions.update', $plainToken), [
            'completion' => 'issue',
            'answers' => [(string) $question->id => '来場者が多い時間帯がありました。'],
        ])->assertRedirect(route('public-actions.complete', $plainToken));

        $submission = ReportSubmission::query()->firstOrFail();
        $this->assertSame('issue', $submission->answers['completion']);
        $this->assertSame('来場者が多い時間帯がありました。', $submission->answers[$question->id]);
        $this->assertNotNull($submission->submitted_at);
    }

    public function test_management_report_page_filters_and_displays_submissions(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $submitted = Event::factory()->create(['report_required' => true, 'name' => '報告済みイベント']);
        $submitted->reportQuestions()->create(['type' => 'text', 'question' => '社内報告', 'display_order' => 0]);
        ReportSubmission::factory()->create(['event_id' => $submitted->id, 'user_id' => User::factory(), 'answers' => ['completion' => 'incident', '1' => 'トラブル内容'], 'submitted_at' => now()]);
        Event::factory()->create(['report_required' => true, 'name' => '未報告イベント']);
        Event::factory()->create(['report_required' => false, 'name' => '対象外イベント']);

        $this->actingAs($employee)->get(route('reports.index', ['status' => 'submitted']))
            ->assertOk()
            ->assertSee('報告済みイベント')
            ->assertSee('トラブル等あり')
            ->assertDontSee('未報告イベント')
            ->assertDontSee('対象外イベント');
    }

    public function test_staff_cannot_open_management_report_page(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($staff)->get(route('reports.index'))->assertForbidden();
    }
}
