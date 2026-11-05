<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\User;
use App\Notifications\ActionLinkNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AssignmentWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_confirming_required_staff_confirms_event_and_rejects_open_requests(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['status' => 'recruiting', 'required_staff' => 1, 'event_date' => today()->addDay()]);
        $confirmedAssignment = Assignment::factory()->create(['event_id' => $event->id, 'status' => 'accepted']);
        $overflowAssignment = Assignment::factory()->create(['event_id' => $event->id, 'status' => 'requested']);

        $this->actingAs($employee)->patch(route('assignments.update', $confirmedAssignment), [
            'status' => 'confirmed',
        ])->assertRedirect();

        $this->assertDatabaseHas('events', ['id' => $event->id, 'status' => 'confirmed']);
        $this->assertDatabaseHas('assignments', [
            'id' => $confirmedAssignment->id,
            'status' => 'confirmed',
            'payment_status' => 'unconfirmed',
        ]);
        $this->assertDatabaseHas('assignments', ['id' => $overflowAssignment->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('notification_logs', [
            'notifiable_context_id' => $overflowAssignment->id,
            'type' => 'assignment_rejected_capacity',
        ]);
    }

    public function test_cancelling_confirmed_staff_reopens_future_event_when_short(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['status' => 'confirmed', 'required_staff' => 1, 'event_date' => today()->addDay()]);
        $assignment = Assignment::factory()->create(['event_id' => $event->id, 'status' => 'confirmed']);

        $this->actingAs($employee)->patch(route('assignments.update', $assignment), [
            'status' => 'cancelled',
        ])->assertRedirect();

        $this->assertDatabaseHas('events', ['id' => $event->id, 'status' => 'recruiting']);
    }

    public function test_only_confirmed_assignment_can_be_completed(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['status' => 'recruiting', 'event_date' => today()->subDay()]);
        $assignment = Assignment::factory()->create(['event_id' => $event->id, 'status' => 'accepted']);

        $this->actingAs($employee)->patch(route('assignments.update', $assignment), [
            'status' => 'completed',
        ])->assertSessionHasErrors('status');

        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'status' => 'accepted']);
    }

    public function test_semi_employee_changes_status_without_changing_money(): void
    {
        $semiEmployee = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['status' => 'recruiting', 'required_staff' => 2]);
        $assignment = Assignment::factory()->create([
            'event_id' => $event->id,
            'status' => 'accepted',
            'fee' => 9000,
            'transportation' => 1000,
        ]);

        $this->actingAs($semiEmployee)->patch(route('assignments.update', $assignment), [
            'status' => 'confirmed',
            'fee' => 50000,
            'transportation' => 50000,
        ])->assertRedirect();

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'status' => 'confirmed',
            'fee' => 9000,
            'transportation' => 1000,
        ]);
    }

    public function test_first_confirmation_for_temporary_worker_sends_confirmation_and_profile_link(): void
    {
        Notification::fake();
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $temporary = User::factory()->create([
            'role' => 'temporary',
            'account_status' => 'no_login',
            'address' => null,
            'bank_details' => null,
        ]);
        $event = Event::factory()->create(['event_date' => today()->addDays(10), 'required_staff' => 2]);
        $assignment = Assignment::factory()->create([
            'event_id' => $event->id,
            'user_id' => $temporary->id,
            'status' => 'accepted',
        ]);

        $this->actingAs($employee)->patch(route('assignments.update', $assignment), ['status' => 'confirmed'])->assertRedirect();

        Notification::assertSentToTimes($temporary, ActionLinkNotification::class, 2);
        $this->assertDatabaseHas('notification_logs', ['user_id' => $temporary->id, 'type' => 'assignment_confirmed']);
        $this->assertDatabaseHas('notification_logs', ['user_id' => $temporary->id, 'type' => 'temporary_profile']);
        $this->assertDatabaseHas('secure_action_tokens', ['user_id' => $temporary->id, 'purpose' => 'temporary_profile']);
    }
}
