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

    public function test_client_mockup_workflows_are_present_on_their_matching_screens(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);

        $expectedScreenContent = [
            'assignments' => ['社員プレビュー', '実施前', '候補を探して依頼する', '当日の交代を追加する'],
            'event-form' => ['イベントの状態', '場所・持ち物・緊急連絡先・備考', '質問を追加', '見込利益'],
            'profile' => ['LINE連携・通知方法', '対応可能時間', '保有資格', '振込先口座', '契約書'],
            'reports' => ['提出状況：すべて', '終了状況：すべて', '報告書はまだ提出されていません'],
            'client-detail' => ['基本情報', '提案状況', '担当者', '打合せを記録'],
            'staff-detail' => ['社員表示', '対応可能時間', '基本の謝金', '振込先口座', '勤務・依頼の履歴'],
            'notifications' => ['送信履歴', '結果：すべて', '種類：すべて', '送り方：すべて'],
            'proposal-board' => ['すべて', '企業', '自治体', '団体', '提案状況'],
            'meeting-history' => ['今月', '過去3か月', '期間指定', '振り分け待ちのメール'],
            'interviews' => ['担当社員', '候補1 日付', '候補2 日付', '候補3 日付', '面談記録の詳細'],
            'phone-log' => ['受電日時', '折り返し期限', '電話を記録する', '記録'],
        ];

        foreach ($expectedScreenContent as $screen => $content) {
            $response = $this->actingAs($employee)->get(route('screens.show', $screen));

            $response->assertOk();
            foreach ($content as $text) {
                $response->assertSee($text);
            }
        }
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
