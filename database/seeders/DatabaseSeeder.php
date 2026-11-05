<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Client;
use App\Models\Event;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::parse('2026-10-23 14:05:00');
        $users = [
            ['山田 太郎', 'admin@staffmanagement.test', 'employee', 'active'],
            ['田村 優子', 'tamura@highclass-inc.com', 'employee', 'active'],
            ['佐々木 恵', 'sasaki@highclass-inc.com', 'employee', 'active'],
            ['鈴木 一郎', 'semi@staffmanagement.test', 'semi_employee', 'active'],
            ['佐藤 花子', 'staff@staffmanagement.test', 'staff', 'active'],
            ['田中 健', 'tanaka@staffmanagement.test', 'staff', 'active'],
            ['中村 翔', 'nakamura@staffmanagement.test', 'trainer', 'active'],
            ['鈴木 次郎', 'spot@staffmanagement.test', 'temporary', 'no_login'],
            ['伊藤 愛', 'ito@example.com', 'staff', 'active'],
            ['高橋 美咲', 'takahashi@example.com', 'staff', 'active'],
            ['小林 さくら', 'kobayashi@example.com', 'staff', 'active'],
            ['加藤 陽太', 'kato@example.com', 'staff', 'active'],
            ['山本 大輔', 'yamamoto@example.com', 'staff', 'active'],
            ['西田 涼', 'nishida@example.com', 'staff', 'invited'],
            ['前田 由紀', 'maeda@example.com', 'staff', 'active'],
            ['森 健二', 'mori@example.com', 'staff', 'suspended'],
            ['小川 拓也', 'ogawa@example.com', 'temporary', 'no_login'],
            ['岡本 直人', 'okamoto@example.com', 'temporary', 'no_login'],
            ['石井 美保', 'ishii@example.com', 'staff', 'suspended'],
            ['青木 一郎', 'aoki@example.com', 'staff', 'suspended'],
        ];

        foreach ($users as $index => [$name, $email, $role, $status]) {
            $usesLine = in_array($name, ['佐藤 花子', '鈴木 一郎', '田中 健'], true);
            User::query()->updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => Hash::make('password123'),
                'role' => $role,
                'account_status' => $status,
                'nearest_station' => ['新宿駅', '池袋駅', '渋谷駅', '横浜駅'][$index % 4],
                'phone' => sprintf('090-1000-%04d', $index + 1),
                'base_fee' => $role === 'employee' ? 12000 : 9000,
                'notification_method' => $usesLine ? 'line' : 'email',
                'line_user_id' => $usesLine ? 'demo-line-'.md5($email) : null,
                'line_linked_at' => $usesLine ? $now : null,
                'email_verified_at' => $now,
                'invited_at' => $status === 'invited' ? $now->copy()->subDay() : null,
                'suspended_at' => $status === 'suspended' ? $now->copy()->subMonths(3) : null,
            ]);
        }

        $admin = User::query()->where('email', 'admin@staffmanagement.test')->firstOrFail();
        $clients = [
            ['サンプル商事', 'corporation', '地域連携部', 'proposing'],
            ['サンプル観光協会', 'organization', null, 'won'],
            ['サンプル町会', 'organization', null, 'won'],
            ['サンプル市', 'government', '健康推進課', 'approaching'],
            ['サンプル大学', 'corporation', '入試課', 'proposing'],
            ['サンプル商店会', 'organization', null, 'uncontacted'],
        ];

        foreach ($clients as $index => [$name, $type, $department, $status]) {
            Client::query()->updateOrCreate(['name' => $name], [
                'type' => $type,
                'department' => $department,
                'phone' => sprintf('03-%04d-%04d', 1234 + $index, 5678 + $index),
                'address' => '東京都新宿区西新宿1-1-1',
                'proposal_status' => $status,
                'next_action_date' => '2026-10-28',
                'proposal_notes' => '来年度の事業について提案・調整中です。',
                'sales_owner_id' => $admin->id,
            ]);
        }

        $primaryClient = Client::query()->where('name', 'サンプル商事')->firstOrFail();
        DB::table('client_contacts')->updateOrInsert(
            ['client_id' => $primaryClient->id, 'email' => 'mori@example.test'],
            ['position' => '課長', 'name' => '森 一樹', 'mobile' => '090-7777-7777', 'is_retired' => false, 'created_at' => $now, 'updated_at' => $now],
        );

        $events = [
            ['秋季健康フェア', 'サンプル商事', '地域イベント運営', '2026-10-21', 6, 'confirmed'],
            ['ハロウィンマルシェ', 'サンプル観光協会', '催事場 販売補助', '2026-10-24', 8, 'recruiting'],
            ['市民マラソン', 'サンプル市', '市民大会 運営補助', '2026-10-25', 12, 'recruiting'],
            ['企業説明会', 'サンプル大学', '企業説明会 会場案内', '2026-10-28', 5, 'confirmed'],
            ['体力測定会', 'サンプル市', '体力測定会 年', '2026-11-03', 10, 'recruiting'],
            ['健診会場 受付', 'サンプル商事', '健診会場 受付', '2026-11-07', 4, 'confirmed'],
        ];

        foreach ($events as [$name, $clientName, $businessName, $date, $requiredStaff, $status]) {
            Event::query()->updateOrCreate(['name' => $name], [
                'client_id' => Client::query()->where('name', $clientName)->value('id'),
                'business_name' => $businessName,
                'details' => '来場者の受付・検温・会場案内を担当します。',
                'work_content' => '受付、誘導、会場運営',
                'event_date' => $date,
                'meeting_time' => '08:30',
                'start_time' => '09:00',
                'end_time' => '17:00',
                'dismissal_time' => '17:30',
                'required_staff' => $requiredStaff,
                'meeting_place' => 'サンプル公民館 1階ロビー',
                'venue' => 'サンプル公民館 2階 大ホール',
                'items' => ['昼食', '室内履き', 'ハイクラスTシャツ', '筆記用具'],
                'emergency_contacts' => ['ハイクラスLINE', '山田 太郎'],
                'notes' => '公共交通機関でお越しください。',
                'status' => $status,
                'report_required' => true,
                'created_by' => $admin->id,
            ]);
        }

        $event = Event::query()->where('name', '秋季健康フェア')->firstOrFail();
        $assignments = [
            ['鈴木 一郎', 'confirmed', 12000, 840, true],
            ['田中 健', 'confirmed', 9500, 620, false],
            ['伊藤 愛', 'accepted', 9000, 700, false],
            ['高橋 美咲', 'unknown', 8500, 560, false],
            ['佐藤 花子', 'requested', 9000, 480, false],
            ['中村 翔', 'declined', 11000, 900, false],
        ];

        foreach ($assignments as [$name, $status, $fee, $transportation, $isLeader]) {
            Assignment::query()->updateOrCreate(
                ['event_id' => $event->id, 'user_id' => User::query()->where('name', $name)->value('id')],
                [
                    'status' => $status,
                    'special_notes' => $isLeader ? 'リーダーをお願いします。集合は8:00(30分早め)です。' : null,
                    'fee' => $fee,
                    'transportation' => $transportation,
                    'payment_status' => 'waiting',
                    'is_day_leader' => $isLeader,
                    'is_report_representative' => $isLeader,
                    'requested_at' => $now->copy()->subDays(4),
                ],
            );
        }

        $staff = User::query()->where('name', '佐藤 花子')->firstOrFail();
        $this->seedRelatedData($now, $admin, $staff, $event);
        $this->seedActionTokens($now, $staff, $event);
    }

    private function seedRelatedData(Carbon $now, User $admin, User $staff, Event $event): void
    {
        DB::table('qualifications')->updateOrInsert(['user_id' => $staff->id, 'name' => '普通自動車免許'], ['expires_at' => '2029-10-23', 'created_at' => $now, 'updated_at' => $now]);
        DB::table('qualifications')->updateOrInsert(['user_id' => $staff->id, 'name' => '防災士'], ['expires_at' => '2026-11-17', 'created_at' => $now, 'updated_at' => $now]);
        DB::table('invoice_items')->updateOrInsert(['event_id' => $event->id, 'type' => 'work_fee'], ['memo' => '運営スタッフ費', 'amount' => 90000, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('expenses')->updateOrInsert(['event_id' => $event->id, 'type' => 'supplies'], ['memo' => '消耗品', 'amount' => 4000, 'created_at' => $now, 'updated_at' => $now]);

        $phoneLogs = [
            ['2026-10-23 11:20:00', '森 様', 'サンプル観光協会', 'お仕事の依頼・相談', '見積の件で折り返しがほしい。午後は16時まで在席', 'callback_required'],
            ['2026-10-23 09:45:00', '中村 翔', 'スタッフ', 'スタッフからの連絡', '10/24の集合場所の確認。地図をLINEで送って解決', 'completed'],
            ['2026-10-22 16:10:00', '西田 様', 'サンプル大学', '問い合わせ', 'オープンキャンパスの人数を2人増やせるか', 'callback_required'],
            ['2026-10-22 13:30:00', '(名乗らず)', '求人広告の会社', '営業・売り込み', '求人広告の案内。不要と回答', 'completed'],
            ['2026-10-21 10:05:00', '大野 様', 'サンプル町会', 'お仕事の依頼・相談', '来年の秋祭りの日程が決まったとの連絡', 'message_only'],
        ];
        foreach ($phoneLogs as [$calledAt, $callerName, $company, $purpose, $content, $status]) {
            DB::table('phone_logs')->updateOrInsert(['called_at' => $calledAt, 'caller_name' => $callerName], [
                'company' => $company, 'phone' => '0XX-XXX-XXXX', 'purpose' => $purpose, 'content' => $content,
                'recipient_id' => $admin->id, 'received_by' => $admin->id, 'status' => $status, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        foreach (['サンプル商事', 'サンプル観光協会', 'サンプル市'] as $index => $clientName) {
            DB::table('meetings')->updateOrInsert(
                ['client_id' => Client::query()->where('name', $clientName)->value('id'), 'meeting_date' => '2026-10-'.(16 + $index)],
                ['recorded_by' => $admin->id, 'type' => $index === 0 ? 'meeting' : 'sales', 'method' => $index === 0 ? '訪問' : '電話', 'participants' => '森 様 ほか', 'content' => '来年度の事業について実施時期と概算費用を確認しました。', 'created_at' => $now, 'updated_at' => $now],
            );
        }

        Interview::query()->updateOrCreate(['subject_user_id' => $staff->id, 'manager_id' => $admin->id, 'type' => '定期面談'], [
            'method' => '対面', 'location' => 'ハイクラス 事務所',
            'candidate_times' => ['2026-11-28 18:00:00', '2026-11-29 18:00:00', '2026-12-01 10:00:00'],
            'content' => '最近の勤務状況と今後担当したい仕事について確認しました。', 'next_tasks' => '研修を案内する。', 'next_timing' => '3か月後',
        ]);
        DB::table('report_submissions')->updateOrInsert(['event_id' => $event->id, 'user_id' => $staff->id], ['answers' => json_encode(['completion' => 'completed', 'notes' => '予定どおり終了しました。'], JSON_UNESCAPED_UNICODE), 'submitted_at' => $now, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('notification_logs')->updateOrInsert(['user_id' => $staff->id, 'type' => 'assignment_request'], ['channel' => 'line', 'destination' => $staff->line_user_id, 'status' => 'sent', 'subject' => 'お仕事のご依頼', 'body' => '秋季健康フェアのお仕事のご依頼です。', 'sent_at' => $now, 'created_at' => $now, 'updated_at' => $now]);
        $tanaka = User::query()->where('name', '田中 健')->firstOrFail();
        DB::table('notification_logs')->updateOrInsert(['user_id' => $tanaka->id, 'type' => 'attendance_confirmation'], ['channel' => 'email', 'destination' => $tanaka->email, 'status' => 'failed', 'subject' => '本日の出勤確認', 'body' => '本日の集合時間と場所をご確認ください。', 'error_message' => 'メールアドレスへの配信に失敗しました。', 'sent_at' => null, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('availabilities')->updateOrInsert(['user_id' => $staff->id, 'weekday' => 6, 'time_slot' => '8時前'], ['availability' => 'available', 'created_at' => $now, 'updated_at' => $now]);
    }

    private function seedActionTokens(Carbon $now, User $staff, Event $event): void
    {
        $assignment = Assignment::query()->where('event_id', $event->id)->where('user_id', $staff->id)->firstOrFail();
        $interview = Interview::query()->where('subject_user_id', $staff->id)->firstOrFail();
        $temporary = User::query()->where('email', 'spot@staffmanagement.test')->firstOrFail();
        $tokens = [
            ['demo-response', $staff->id, Assignment::class, $assignment->id, 'assignment_response'],
            ['demo-attendance', $staff->id, Assignment::class, $assignment->id, 'attendance_confirmation'],
            ['demo-report', $staff->id, Assignment::class, $assignment->id, 'report_submission'],
            ['demo-interview', $staff->id, Interview::class, $interview->id, 'interview_selection'],
            ['demo-profile', $temporary->id, User::class, $temporary->id, 'temporary_profile'],
            ['demo-password', $temporary->id, User::class, $temporary->id, 'first_password'],
            ['demo-line', $temporary->id, User::class, $temporary->id, 'line_connect'],
        ];

        foreach ($tokens as [$token, $userId, $type, $id, $purpose]) {
            DB::table('secure_action_tokens')->updateOrInsert(['token_hash' => hash('sha256', $token)], [
                'user_id' => $userId, 'actionable_type' => $type, 'actionable_id' => $id, 'purpose' => $purpose,
                'expires_at' => '2027-12-31 23:59:59', 'used_at' => null, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
