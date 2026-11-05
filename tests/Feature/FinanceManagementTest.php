<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\User;
use App\Notifications\ActionLinkNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FinanceManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_employee_filters_and_updates_payment_amounts(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $staff = User::factory()->create(['name' => '支払 太郎', 'role' => 'staff', 'account_status' => 'active']);
        $assignment = Assignment::factory()->create(['user_id' => $staff->id, 'status' => 'completed', 'payment_status' => 'waiting']);

        $this->actingAs($employee)->get(route('finance.payments', ['name' => '支払', 'status' => 'waiting']))
            ->assertOk()->assertSee('支払 太郎');

        $this->actingAs($employee)->patch(route('payments.update', $assignment), [
            'fee' => 12000,
            'transportation' => 800,
            'reimbursement' => 500,
            'withholding_tax' => 1000,
            'payment_status' => 'paid',
            'payment_source' => 'rakuten',
            'paid_at' => today()->format('Y-m-d'),
        ])->assertRedirect();

        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'fee' => 12000, 'reimbursement' => 500, 'payment_status' => 'paid', 'payment_source' => 'rakuten']);
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $assignment->id, 'action' => 'update_payment']);
    }

    public function test_employee_bulk_marks_selected_eligible_assignments_paid(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $first = Assignment::factory()->create(['status' => 'completed', 'payment_status' => 'waiting']);
        $second = Assignment::factory()->create(['status' => 'completed', 'payment_status' => 'waiting']);

        $this->actingAs($employee)->patch(route('finance.payments.bulk-update'), [
            'assignment_ids' => [$first->id, $second->id],
            'payment_status' => 'paid',
            'payment_source' => 'gmo',
            'paid_at' => '2026-11-05',
        ])->assertRedirect();

        $this->assertDatabaseHas('assignments', ['id' => $first->id, 'payment_status' => 'paid', 'payment_source' => 'gmo']);
        $this->assertDatabaseHas('assignments', ['id' => $second->id, 'payment_status' => 'paid', 'payment_source' => 'gmo']);
    }

    public function test_paying_temporary_worker_sends_payment_notice(): void
    {
        Notification::fake();
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $temporary = User::factory()->create(['role' => 'temporary', 'account_status' => 'no_login']);
        $assignment = Assignment::factory()->create([
            'user_id' => $temporary->id,
            'status' => 'completed',
            'payment_status' => 'waiting',
        ]);

        $this->actingAs($employee)->patch(route('payments.update', $assignment), [
            'fee' => 10000,
            'transportation' => 500,
            'reimbursement' => 0,
            'withholding_tax' => 0,
            'payment_status' => 'paid',
            'payment_source' => 'rakuten',
            'paid_at' => today()->format('Y-m-d'),
        ])->assertRedirect();

        Notification::assertSentTo($temporary, ActionLinkNotification::class);
        $this->assertDatabaseHas('notification_logs', [
            'user_id' => $temporary->id,
            'type' => 'payment_notice',
            'status' => 'sent',
        ]);
    }

    public function test_profit_page_calculates_invoice_cost_profit_and_excludes_withholding(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['name' => '収支テスト', 'event_date' => '2026-11-10']);
        $event->invoiceItems()->create(['type' => 'work', 'amount' => 30000]);
        $event->expenses()->create(['type' => 'supplies', 'amount' => 1000]);
        Assignment::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'fee' => 10000,
            'transportation' => 1000,
            'reimbursement' => 500,
            'withholding_tax' => 999,
        ]);

        $this->actingAs($employee)->get(route('finance.profit', ['month' => '2026-11']))
            ->assertOk()
            ->assertSee('収支テスト')
            ->assertSee('30,000')
            ->assertSee('12,500')
            ->assertSee('17,500');
    }

    public function test_profit_csv_download_contains_real_event(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        Event::factory()->create(['name' => 'CSV対象イベント', 'event_date' => '2026-11-10']);

        $response = $this->actingAs($employee)->get(route('finance.profit.csv', ['month' => '2026-11']));
        $response->assertOk()->assertDownload('profit-2026-11.csv');
        $this->assertStringContainsString('CSV対象イベント', $response->streamedContent());
    }

    public function test_semi_employee_cannot_access_finance_pages(): void
    {
        $semi = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);

        $this->actingAs($semi)->get(route('finance.payments'))->assertForbidden();
        $this->actingAs($semi)->get(route('finance.profit'))->assertForbidden();
    }
}
