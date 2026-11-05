<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MeetingManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_employee_creates_updates_and_deletes_meeting(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $client = Client::factory()->create();

        $this->actingAs($employee)->post(route('clients.meetings.store', $client), [
            'type' => 'sales',
            'meeting_date' => '2026-11-10',
            'method' => 'visit',
            'participants' => '担当者A',
            'content' => '初回の打合せを実施しました。',
        ])->assertRedirect();

        $meeting = Meeting::query()->where('client_id', $client->id)->firstOrFail();
        $this->assertSame($employee->id, $meeting->recorded_by);

        $this->actingAs($employee)->patch(route('clients.meetings.update', [$client, $meeting]), [
            'type' => 'follow_up',
            'meeting_date' => '2026-11-11',
            'method' => 'online',
            'participants' => '担当者B',
            'content' => '内容を更新しました。',
        ])->assertRedirect();

        $this->assertDatabaseHas('meetings', [
            'id' => $meeting->id,
            'type' => 'follow_up',
            'method' => 'online',
            'content' => '内容を更新しました。',
        ]);

        $this->actingAs($employee)
            ->delete(route('clients.meetings.destroy', [$client, $meeting]))
            ->assertRedirect();
        $this->assertDatabaseMissing('meetings', ['id' => $meeting->id]);
    }

    public function test_non_employee_cannot_mutate_meetings(): void
    {
        $semiEmployee = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);
        $client = Client::factory()->create();

        $this->actingAs($semiEmployee)->post(route('clients.meetings.store', $client), [
            'type' => 'sales',
            'meeting_date' => '2026-11-10',
            'method' => 'visit',
            'content' => '作成できない内容',
        ])->assertForbidden();

        $this->assertDatabaseCount('meetings', 0);
    }

    public function test_client_detail_renders_database_meeting_and_event_history(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $client = Client::factory()->create();
        Meeting::factory()->create([
            'client_id' => $client->id,
            'recorded_by' => $employee->id,
            'content' => 'DBに保存された打合せ内容',
        ]);

        $this->actingAs($employee)->get(route('clients.edit', $client))
            ->assertOk()
            ->assertSee('DBに保存された打合せ内容')
            ->assertSee('data-edit-meeting', false)
            ->assertSee('data-confirm-delete', false);
    }
}
