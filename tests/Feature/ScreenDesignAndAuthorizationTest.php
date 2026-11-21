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
            ->assertSeeInOrder(['実施日', 'イベント名', 'スタッフ名', '謝金', '交通費', '立替', '源泉税', '総支払額', '支払状態', '支払元', '支払日']);

        $this->actingAs($employee)->get('/screens/staff')
            ->assertOk()
            ->assertSeeInOrder(['名前', '最寄り駅', '携帯電話', '車', 'LINE', '今年の勤務', '資格の期限', '操作']);
    }

    public function test_staff_cannot_open_employee_or_management_screens(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($staff)->get('/screens/payments')->assertForbidden();
        $this->actingAs($staff)->get('/screens/assignments')->assertForbidden();
        $this->actingAs($staff)->get('/screens/assignment-response')->assertForbidden();
        $this->actingAs($staff)->get('/screens/temporary-bank')->assertForbidden();
    }

    public function test_staff_mock_profile_is_scoped_to_the_authenticated_user(): void
    {
        User::factory()->create([
            'name' => '別のスタッフ',
            'role' => 'staff',
            'account_status' => 'active',
            'address' => '東京都他人区1-1',
        ]);
        $staff = User::factory()->create([
            'name' => '本人スタッフ',
            'role' => 'staff',
            'account_status' => 'active',
            'address' => '東京都本人区2-2',
        ]);

        $this->actingAs($staff)->get(route('screens.show', 'profile'))
            ->assertOk()
            ->assertSee('本人スタッフ')
            ->assertSee('東京都本人区2-2')
            ->assertDontSee('東京都他人区1-1');
    }

    public function test_client_mockup_workflows_are_present_on_their_matching_screens(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);

        $expectedScreenContent = [
            'assignments' => ['社員として見る', '実施前(10/19)', '候補を探して依頼する', '当日の交代を追加する'],
            'event-form' => ['基本情報', '場所・持ち物・緊急連絡先・備考', '報告書', '請求と収支'],
            'profile' => ['LINE連携・通知方法', '対応可能時間', '資格', '振込先口座', '契約書'],
            'reports' => ['イベント一覧', '防災フェア', '依頼先への報告用'],
            'client-detail' => ['基本情報', '提案状況', '担当者'],
            'staff-detail' => ['社員として見る', '対応可能時間', '基本の謝金', '振込先口座', '勤務・依頼の履歴'],
            'notifications' => ['送信履歴', '送れなかったものだけ見る', '連絡の種類', '送り方', '結果'],
            'proposal-board' => ['種別:すべて', '法人', '行政', '団体', '提案状況'],
            'meeting-history' => ['振り分け待ちのメール', '種別で絞り込み', '打合せ・メール'],
            'interviews' => ['面談を設定', 'これからの面談', '面談の記録', '次回までの課題・約束'],
            'phone-log' => ['日時', '相手の名前', '電話を記録する', '記録'],
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
