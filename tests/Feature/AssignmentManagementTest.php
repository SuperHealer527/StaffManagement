<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Availability;
use App\Models\Event;
use App\Models\User;
use App\Notifications\ActionLinkNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AssignmentManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_employee_can_search_candidates_and_open_dynamic_assignment_page(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create();
        $matching = User::factory()->create(['name' => '候補 太郎', 'role' => 'staff', 'account_status' => 'active']);
        User::factory()->create(['name' => '対象外 花子', 'role' => 'staff', 'account_status' => 'active']);
        Availability::query()->create(['user_id' => $matching->id, 'weekday' => $event->event_date->dayOfWeek, 'time_slot' => 'morning', 'availability' => 'available']);

        $this->actingAs($employee)
            ->get(route('events.assignments.index', ['event' => $event, 'keyword' => '候補', 'available' => 1]))
            ->assertOk()
            ->assertSee('候補 太郎')
            ->assertDontSee('対象外 花子')
            ->assertSee('候補を探して依頼する');
    }

    public function test_temporary_account_without_login_is_available_as_assignment_candidate(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['status' => 'recruiting']);
        $temporary = User::factory()->create([
            'name' => '単発 候補者',
            'role' => 'temporary',
            'account_status' => 'no_login',
        ]);

        $this->actingAs($employee)->get(route('events.assignments.index', $event))
            ->assertOk()
            ->assertSee('単発 候補者');

        Notification::fake();
        $this->actingAs($employee)->post(route('events.assignments.store', $event), [
            'user_id' => $temporary->id,
            'fee' => 10000,
            'transportation' => 500,
        ])->assertRedirect();
        $this->assertDatabaseHas('assignments', ['event_id' => $event->id, 'user_id' => $temporary->id]);
    }

    public function test_employee_sends_assignment_request_with_secure_response_link(): void
    {
        Notification::fake();
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['status' => 'recruiting']);
        $candidate = User::factory()->create(['role' => 'staff', 'account_status' => 'active', 'notification_method' => 'email']);

        $this->actingAs($employee)->post(route('events.assignments.store', $event), [
            'user_id' => $candidate->id,
            'special_notes' => '受付担当',
            'fee' => 10000,
            'transportation' => 800,
        ])->assertRedirect();

        $assignment = Assignment::query()->whereBelongsTo($event)->whereBelongsTo($candidate)->firstOrFail();
        $this->assertSame('requested', $assignment->status);
        $this->assertDatabaseHas('secure_action_tokens', ['actionable_id' => $assignment->id, 'purpose' => 'assignment_response']);
        $this->assertDatabaseHas('notification_logs', ['notifiable_context_id' => $assignment->id, 'type' => 'assignment_request', 'status' => 'sent']);
        Notification::assertSentTo($candidate, ActionLinkNotification::class);
    }

    public function test_semi_employee_cannot_send_request_or_add_direct_assignment(): void
    {
        $semiEmployee = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);
        $event = Event::factory()->create();
        $candidate = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($semiEmployee)->post(route('events.assignments.store', $event), [
            'user_id' => $candidate->id,
            'fee' => 9000,
            'transportation' => 500,
        ])->assertForbidden();

        $this->assertDatabaseCount('assignments', 0);
    }

    public function test_employee_can_add_same_day_replacement_to_completed_event(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['status' => 'completed', 'event_date' => today()->subDay()]);
        $candidate = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($employee)->post(route('events.assignments.store', $event), [
            'user_id' => $candidate->id,
            'fee' => 12000,
            'transportation' => 1000,
            'confirmed_directly' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('assignments', ['event_id' => $event->id, 'user_id' => $candidate->id, 'status' => 'confirmed']);
        $this->assertDatabaseCount('secure_action_tokens', 0);
        $this->assertDatabaseHas('assignments', ['event_id' => $event->id, 'seven_day_confirmation' => 'not_applicable']);
    }

    public function test_only_confirmed_employee_or_semi_employee_can_be_leader(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['required_staff' => 3]);
        $leader = Assignment::factory()->create([
            'event_id' => $event->id,
            'user_id' => User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active'])->id,
            'status' => 'confirmed',
        ]);
        $staff = Assignment::factory()->create(['event_id' => $event->id, 'status' => 'confirmed']);

        $this->actingAs($employee)->patch(route('events.assignments.leaders', $event), [
            'day_leader_assignment_id' => $leader->id,
            'representative_same_as_leader' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('assignments', ['id' => $leader->id, 'is_day_leader' => true, 'is_report_representative' => true]);

        $this->actingAs($employee)->patch(route('events.assignments.leaders', $event), [
            'day_leader_assignment_id' => $staff->id,
        ])->assertSessionHasErrors('day_leader_assignment_id');
    }

    public function test_shortage_page_only_lists_upcoming_understaffed_events(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $short = Event::factory()->create(['name' => '不足イベント', 'required_staff' => 2]);
        Assignment::factory()->create(['event_id' => $short->id, 'status' => 'confirmed']);
        $full = Event::factory()->create(['name' => '充足イベント', 'required_staff' => 1]);
        Assignment::factory()->create(['event_id' => $full->id, 'status' => 'confirmed']);
        Event::factory()->create(['name' => '下書きイベント', 'status' => 'draft']);

        $this->actingAs($employee)->get(route('shortages.index'))
            ->assertOk()
            ->assertSee('不足イベント')
            ->assertDontSee('充足イベント')
            ->assertDontSee('下書きイベント')
            ->assertSee('あと1人');
    }
}
