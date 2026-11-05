<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Event;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ScheduledStatusSynchronizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_command_completes_past_events_and_assignments(): void
    {
        $event = Event::factory()->create([
            'status' => 'confirmed',
            'event_date' => today()->subDay(),
            'required_staff' => 1,
        ]);
        $confirmed = Assignment::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'payment_status' => 'unconfirmed',
        ]);
        $pending = Assignment::factory()->create([
            'event_id' => $event->id,
            'status' => 'requested',
        ]);

        $this->artisan('staff-management:synchronize-statuses')
            ->expectsOutput('イベント 1件、アサイン 2件を更新しました。')
            ->assertSuccessful();

        $this->assertDatabaseHas('events', ['id' => $event->id, 'status' => 'completed']);
        $this->assertDatabaseHas('assignments', [
            'id' => $confirmed->id,
            'status' => 'completed',
            'payment_status' => 'waiting',
        ]);
        $this->assertDatabaseHas('assignments', ['id' => $pending->id, 'status' => 'rejected']);
    }
}
