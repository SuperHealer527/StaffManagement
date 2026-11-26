<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Availability;
use App\Models\Event;
use App\Models\NotificationLog;
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
        foreach (['8〜10時', '10〜12時', '12〜13時', '13〜15時', '15〜17時', '17〜19時'] as $timeSlot) {
            Availability::query()->create([
                'user_id' => $matching->id,
                'weekday' => $event->event_date->dayOfWeek,
                'time_slot' => $timeSlot,
                'availability' => 'available',
            ]);
        }

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
        $event->update(['business_name' => '健康づくり支援事業']);
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
        $body = NotificationLog::query()->where('notifiable_context_id', $assignment->id)->value('body');
        $this->assertStringContainsString('事業名：健康づくり支援事業', (string) $body);
        Notification::assertSentTo($candidate, ActionLinkNotification::class);
        $notification = Notification::sent($candidate, ActionLinkNotification::class)->first();
        $this->assertInstanceOf(ActionLinkNotification::class, $notification);
        $this->assertSame(
            preg_split('/\R+/', trim($notification->body)),
            $notification->toMail($candidate)->introLines,
        );
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

    public function test_any_confirmed_assignee_can_be_leader_or_report_representative(): void
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
            'report_representative_assignment_id' => $staff->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('assignments', ['id' => $leader->id, 'is_day_leader' => true, 'is_report_representative' => false]);
        $this->assertDatabaseHas('assignments', ['id' => $staff->id, 'is_day_leader' => false, 'is_report_representative' => true]);

        $this->actingAs($employee)->patch(route('events.assignments.leaders', $event), [
            'day_leader_assignment_id' => $staff->id,
            'representative_same_as_leader' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('assignments', [
            'id' => $staff->id,
            'is_day_leader' => true,
            'is_report_representative' => true,
        ]);
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

    public function test_accepted_assignment_reduces_the_shortage_count(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['name' => '承諾反映イベント', 'required_staff' => 4]);
        Assignment::factory()->create(['event_id' => $event->id, 'status' => 'accepted']);

        $this->actingAs($employee)->get(route('shortages.index'))
            ->assertOk()
            ->assertSee('承諾反映イベント')
            ->assertSee('1 / 4人')
            ->assertSee('あと3人');

        $this->actingAs($employee)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('1 / 4人')
            ->assertSee('あと3人');
    }
}
