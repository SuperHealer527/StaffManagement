<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\ReportSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class StaffPortalTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_staff_dashboard_redirects_to_dynamic_portal_home(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);
        $event = Event::factory()->create(['name' => '本人向け依頼']);
        Assignment::factory()->create(['event_id' => $event->id, 'user_id' => $staff->id, 'status' => 'requested']);

        $this->actingAs($staff)->get(route('dashboard'))->assertOk()->assertSee('本人向け依頼');
        $this->actingAs($staff)->get(route('my.home'))
            ->assertOk()
            ->assertSee('本人向け依頼')
            ->assertSee('未回答の依頼');
    }

    public function test_staff_can_answer_and_change_open_assignment_response(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);
        $assignment = Assignment::factory()->create(['user_id' => $staff->id, 'status' => 'requested']);

        $this->actingAs($staff)->patch(route('my.assignments.respond', $assignment), ['response' => 'accepted'])->assertRedirect();
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'status' => 'accepted']);

        $this->actingAs($staff)->patch(route('my.assignments.respond', $assignment), ['response' => 'unknown'])->assertRedirect();
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'status' => 'unknown']);
    }

    public function test_other_user_cannot_answer_or_confirm_assignment(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);
        $other = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);
        $assignment = Assignment::factory()->create(['user_id' => $staff->id, 'status' => 'confirmed']);

        $this->actingAs($other)->post(route('my.assignments.attendance', [$assignment, 'seven-day']))->assertForbidden();
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'seven_day_confirmation' => 'not_sent']);
    }

    public function test_staff_confirms_attendance_and_sees_it_on_home(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);
        $event = Event::factory()->create(['event_date' => today()->addDays(3), 'status' => 'confirmed']);
        $assignment = Assignment::factory()->create(['event_id' => $event->id, 'user_id' => $staff->id, 'status' => 'confirmed', 'seven_day_confirmation' => 'sent']);

        $this->actingAs($staff)->post(route('my.assignments.attendance', [$assignment, 'seven-day']))->assertRedirect();
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'seven_day_confirmation' => 'confirmed']);
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $assignment->id, 'action' => 'confirm_attendance']);
    }

    public function test_report_representative_submits_report_from_authenticated_portal(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);
        $event = Event::factory()->create(['event_date' => today()->subDay(), 'status' => 'completed', 'report_required' => true]);
        $question = $event->reportQuestions()->create(['type' => 'text', 'question' => '社内への報告', 'display_order' => 0]);
        $assignment = Assignment::factory()->create(['event_id' => $event->id, 'user_id' => $staff->id, 'status' => 'completed', 'is_report_representative' => true]);

        $this->actingAs($staff)->get(route('my.reports.edit', $assignment))->assertOk()->assertSee('社内への報告');
        $this->actingAs($staff)->post(route('my.reports.store', $assignment), [
            'completion' => 'completed',
            'answers' => [$question->id => '問題ありません。'],
        ])->assertRedirect(route('my.home'));

        $submission = ReportSubmission::query()->firstOrFail();
        $this->assertSame('問題ありません。', $submission->answers[$question->id]);
    }

    public function test_payment_statement_only_contains_own_eligible_assignments(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);
        $ownEvent = Event::factory()->create(['name' => '自分の支払対象', 'event_date' => today()]);
        Assignment::factory()->create(['event_id' => $ownEvent->id, 'user_id' => $staff->id, 'status' => 'completed', 'fee' => 10000, 'transportation' => 500]);
        Assignment::factory()->create(['status' => 'completed', 'fee' => 999999]);
        Assignment::factory()->create(['event_id' => Event::factory()->create(['name' => '辞退案件'])->id, 'user_id' => $staff->id, 'status' => 'declined']);

        $this->actingAs($staff)->get(route('my.payments'))
            ->assertOk()
            ->assertSee('自分の支払対象')
            ->assertSee('10,500')
            ->assertDontSee('999,999')
            ->assertDontSee('辞退案件');
    }
}
