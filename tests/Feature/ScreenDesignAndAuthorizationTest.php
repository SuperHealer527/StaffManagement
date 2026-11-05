<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ScreenDesignAndAuthorizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_staff_dashboard_uses_the_client_mobile_sections(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($staff)->get('/dashboard')
            ->assertOk()
            ->assertSeeInOrder(['ホーム', '未回答の依頼', '出勤確認', '報告書の提出', '今後の予定']);
    }

    public function test_management_tables_use_the_client_design_columns(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);

        $this->actingAs($employee)->get('/screens/payments')
            ->assertOk()
            ->assertSeeInOrder(['実施日', 'イベント名', 'スタッフ名', '謝金（円）', '交通費（円）', '立替（円）', '源泉税（円）', '総支払額', '支払ステータス', '支払元', '支払日']);

        $this->actingAs($employee)->get('/screens/staff')
            ->assertOk()
            ->assertSeeInOrder(['名前', '最寄り駅', '携帯電話', '車', 'LINE', '今年の勤務', '資格の期限', '操作']);
    }

    public function test_staff_cannot_open_employee_or_management_screens(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($staff)->get('/screens/payments')->assertForbidden();
        $this->actingAs($staff)->get('/screens/assignments')->assertForbidden();
    }

    public function test_employee_can_mark_a_confirmed_assignment_as_paid(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $assignment = Assignment::factory()->create(['status' => 'confirmed', 'payment_status' => 'waiting']);

        $this->actingAs($employee)->patch(route('payments.update', $assignment), [
            'payment_status' => 'paid',
            'payment_source' => 'rakuten',
            'paid_at' => '2026-11-05',
            'reimbursement' => 1000,
            'withholding_tax' => 919,
        ])->assertRedirect();

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'payment_status' => 'paid',
            'payment_source' => 'rakuten',
            'reimbursement' => 1000,
        ]);
    }
}
