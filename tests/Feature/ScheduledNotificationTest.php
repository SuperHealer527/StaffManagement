<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\Interview;
use App\Models\NotificationLog;
use App\Models\SecureActionToken;
use App\Models\User;
use App\Notifications\ActionLinkNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ScheduledNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_seven_day_attendance_notification_is_sent_once_and_link_confirms_correct_period(): void
    {
        Carbon::setTestNow('2026-11-05 09:00:00 Asia/Tokyo');
        Notification::fake();
        $staff = User::factory()->create(['notification_method' => 'email']);
        $event = Event::factory()->create(['event_date' => '2026-11-12']);
        $assignment = Assignment::factory()->create([
            'event_id' => $event->id,
            'user_id' => $staff->id,
            'seven_day_confirmation' => 'not_sent',
            'same_day_confirmation' => 'not_sent',
        ]);

        $this->artisan('staff-management:send-due-notifications')->assertSuccessful();
        $this->artisan('staff-management:send-due-notifications')->assertSuccessful();

        Notification::assertSentToTimes($staff, ActionLinkNotification::class, 1);
        $this->assertDatabaseCount('notification_logs', 1);
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'seven_day_confirmation' => 'sent']);
        $this->assertSame('attendance_seven_day', SecureActionToken::query()->sole()->purpose);

        $plainToken = $this->actionTokenFromLog(NotificationLog::query()->sole());
        $this->post(route('public-actions.update', $plainToken))->assertRedirect();
        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'seven_day_confirmation' => 'confirmed',
            'same_day_confirmation' => 'not_sent',
        ]);
    }

    public function test_same_day_and_report_notifications_are_sent_when_their_times_arrive(): void
    {
        Carbon::setTestNow('2026-11-05 18:00:00 Asia/Tokyo');
        Notification::fake();
        $staff = User::factory()->create(['notification_method' => 'email']);
        $event = Event::factory()->create([
            'event_date' => '2026-11-05',
            'meeting_time' => '08:30',
            'dismissal_time' => '17:30',
            'report_required' => true,
        ]);
        $assignment = Assignment::factory()->create([
            'event_id' => $event->id,
            'user_id' => $staff->id,
            'status' => 'completed',
            'same_day_confirmation' => 'not_sent',
            'is_report_representative' => true,
        ]);

        $this->artisan('staff-management:send-due-notifications')->assertSuccessful();

        Notification::assertSentToTimes($staff, ActionLinkNotification::class, 2);
        $this->assertDatabaseHas('notification_logs', ['notifiable_context_id' => $assignment->id, 'type' => 'attendance_same_day', 'status' => 'sent']);
        $this->assertDatabaseHas('notification_logs', ['notifiable_context_id' => $assignment->id, 'type' => 'report_request', 'status' => 'sent']);
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'same_day_confirmation' => 'sent']);
    }

    public function test_interview_reminder_is_sent_on_the_previous_day(): void
    {
        Carbon::setTestNow('2026-11-05 09:00:00 Asia/Tokyo');
        Notification::fake();
        $interview = Interview::factory()->create(['scheduled_at' => '2026-11-06 13:30:00']);

        $this->artisan('staff-management:send-due-notifications')->assertSuccessful();

        Notification::assertSentTo($interview->subject, ActionLinkNotification::class);
        $this->assertDatabaseHas('notification_logs', [
            'notifiable_context_type' => $interview->getMorphClass(),
            'notifiable_context_id' => $interview->id,
            'type' => 'interview_reminder',
            'status' => 'sent',
        ]);
    }

    public function test_employee_can_filter_history_and_retry_a_failed_line_notification(): void
    {
        config(['services.line.channel_access_token' => 'test-token']);
        Http::fakeSequence()
            ->push(['message' => 'failure'], 500)
            ->push([], 200);
        Carbon::setTestNow('2026-11-05 09:00:00 Asia/Tokyo');
        $staff = User::factory()->create(['notification_method' => 'line', 'line_user_id' => 'line-user-id']);
        $event = Event::factory()->create(['event_date' => '2026-11-12']);
        Assignment::factory()->create(['event_id' => $event->id, 'user_id' => $staff->id]);

        $this->artisan('staff-management:send-due-notifications')->assertSuccessful();
        $log = NotificationLog::query()->sole();
        $this->assertSame('failed', $log->status);

        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $this->actingAs($employee)
            ->get(route('notifications.index', ['status' => 'failed', 'period' => 'all']))
            ->assertOk()
            ->assertSee($staff->name)
            ->assertSee('再送する');

        $this->actingAs($employee)->post(route('notifications.retry', $log))->assertRedirect();
        $this->assertSame('sent', $log->fresh()->status, (string) $log->fresh()->error_message);
    }

    private function actionTokenFromLog(NotificationLog $log): string
    {
        preg_match('/\/action\/([^\s]+)/', (string) $log->body, $matches);

        return $matches[1];
    }
}
