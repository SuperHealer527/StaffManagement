<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Client;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_employee_creates_event_with_report_invoice_and_expense_data(): void
    {
        Storage::fake('local');
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $client = Client::factory()->create();

        $response = $this->actingAs($employee)->post(route('events.store'), [
            ...$this->validEventData($client),
            'items' => ['lunch', 'other'],
            'other_item' => '筆記用具',
            'emergency_contacts' => ['line' => true, 'employee_id' => $employee->id, 'leader' => true],
            'report_questions' => [[
                'type' => 'select',
                'question' => '無事終了しましたか？',
                'options' => ['はい', '報告事項あり'],
            ]],
            'invoice_items' => [['type' => 'work', 'memo' => '運営費', 'amount' => 50000]],
            'expenses' => [[
                'type' => 'supplies',
                'memo' => '名札',
                'amount' => 1000,
                'receipt' => UploadedFile::fake()->image('receipt.jpg'),
            ]],
        ]);

        $event = Event::query()->where('name', '機能テストイベント')->firstOrFail();
        $response->assertRedirect(route('events.edit', $event));
        $this->assertContains('other:筆記用具', $event->items);
        $this->assertDatabaseHas('report_questions', ['event_id' => $event->id, 'question' => '無事終了しましたか？']);
        $this->assertDatabaseHas('invoice_items', ['event_id' => $event->id, 'amount' => 50000]);
        $expense = $event->expenses()->firstOrFail();
        Storage::disk('local')->assertExists($expense->receipt_path);
    }

    public function test_event_list_filters_keyword_status_and_shortage(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $client = Client::factory()->create();
        $shortEvent = Event::factory()->create([
            'client_id' => $client->id,
            'name' => '不足対象イベント',
            'event_date' => today(),
            'required_staff' => 2,
            'status' => 'recruiting',
        ]);
        Assignment::factory()->create(['event_id' => $shortEvent->id, 'status' => 'confirmed']);
        Event::factory()->create([
            'client_id' => $client->id,
            'name' => '表示対象外イベント',
            'event_date' => today(),
            'required_staff' => 1,
            'status' => 'confirmed',
        ]);

        $this->actingAs($employee)->get(route('events.index', [
            'period' => 'today',
            'q' => '不足対象',
            'status' => 'recruiting',
            'shortage' => 1,
        ]))->assertOk()->assertSee('不足対象イベント')->assertDontSee('表示対象外イベント');
    }

    public function test_employee_duplicates_event_for_multiple_dates_without_assignments(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['created_by' => $employee->id]);
        $event->reportQuestions()->create(['type' => 'text', 'question' => '報告内容', 'display_order' => 0]);
        Assignment::factory()->create(['event_id' => $event->id]);

        $this->actingAs($employee)->post(route('events.duplicate', $event), [
            'dates' => ['2026-12-01', '2026-12-08'],
            'status' => 'draft',
            'copy_name' => 1,
            'copy_schedule' => 1,
            'copy_location' => 1,
            'copy_details' => 1,
            'copy_report' => 1,
        ])->assertSessionHasNoErrors()->assertRedirect();

        $duplicates = Event::query()->where('id', '!=', $event->id)->get();
        $this->assertCount(2, $duplicates);
        $this->assertEqualsCanonicalizing(['2026-12-01', '2026-12-08'], $duplicates->map(fn (Event $duplicate) => $duplicate->event_date->format('Y-m-d'))->all());
        foreach ($duplicates as $duplicate) {
            $this->assertSame(0, $duplicate->assignments()->count());
            $this->assertSame(1, $duplicate->reportQuestions()->count());
        }
    }

    public function test_cancelling_event_cancels_active_assignments_and_creates_notifications(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['status' => 'recruiting']);
        $assignment = Assignment::factory()->create(['event_id' => $event->id, 'status' => 'confirmed']);

        $this->actingAs($employee)->post(route('events.cancel', $event), ['confirmation' => '1'])
            ->assertRedirect(route('events.show', $event));

        $this->assertDatabaseHas('events', ['id' => $event->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('notification_logs', [
            'user_id' => $assignment->user_id,
            'type' => 'event_cancelled',
        ]);
    }

    public function test_event_edit_page_renders_database_values_and_real_nested_form_controls(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create([
            'created_by' => $employee->id,
            'name' => 'DB表示イベント',
            'details' => 'DBから表示する詳細',
        ]);
        $event->reportQuestions()->create(['type' => 'text', 'question' => 'DB設問', 'display_order' => 0]);
        $event->invoiceItems()->create(['type' => 'work', 'memo' => 'DB請求', 'amount' => 10000]);

        $this->actingAs($employee)->get(route('events.edit', $event))
            ->assertOk()
            ->assertSee('DB表示イベント')
            ->assertSee('DBから表示する詳細')
            ->assertSee('DB設問')
            ->assertSee('name="report_questions[0][question]"', false)
            ->assertSee('name="invoice_items[0][amount]"', false)
            ->assertSee('data-cancel-event-url', false);
    }

    public function test_expense_receipt_is_private_to_employee(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('expense-receipts/private.pdf', 'receipt');
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $semiEmployee = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['created_by' => $employee->id]);
        $expense = $event->expenses()->create([
            'type' => 'supplies',
            'amount' => 1000,
            'receipt_path' => 'expense-receipts/private.pdf',
        ]);

        $this->actingAs($employee)->get(route('events.expenses.receipt', [$event, $expense]))->assertOk();
        $this->actingAs($semiEmployee)->get(route('events.expenses.receipt', [$event, $expense]))->assertForbidden();
    }

    public function test_staff_can_view_only_events_assigned_to_them(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);
        $assignedEvent = Event::factory()->create(['name' => '本人のイベント']);
        $otherEvent = Event::factory()->create(['name' => '他人のイベント']);
        Assignment::factory()->create(['event_id' => $assignedEvent->id, 'user_id' => $staff->id]);

        $this->actingAs($staff)->get(route('events.show', $assignedEvent))
            ->assertOk()
            ->assertSee('本人のイベント')
            ->assertDontSee('アサインを開く');
        $this->actingAs($staff)->get(route('events.show', $otherEvent))->assertForbidden();
    }

    /** @return array<string, mixed> */
    private function validEventData(Client $client): array
    {
        return [
            'client_id' => $client->id,
            'business_name' => '地域事業',
            'name' => '機能テストイベント',
            'event_date' => '2026-12-20',
            'meeting_time' => '08:30',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'dismissal_time' => '17:30',
            'required_staff' => 5,
            'status' => 'recruiting',
            'report_required' => true,
        ];
    }
}
