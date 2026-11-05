<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Client;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $users = [
            ['name' => '山田 太郎', 'email' => 'admin@staffmanagement.test', 'role' => 'employee', 'nearest_station' => '新宿駅', 'phone' => '090-1111-1111', 'base_fee' => 12000],
            ['name' => '鈴木 一郎', 'email' => 'semi@staffmanagement.test', 'role' => 'semi_employee', 'nearest_station' => '池袋駅', 'phone' => '080-2222-2222', 'base_fee' => 10000],
            ['name' => '佐藤 花子', 'email' => 'staff@staffmanagement.test', 'role' => 'staff', 'nearest_station' => '池袋駅', 'phone' => '090-3333-3333', 'base_fee' => 9000, 'is_student' => true, 'line_user_id' => 'demo-line-staff', 'line_linked_at' => $now],
            ['name' => '田中 健', 'email' => 'tanaka@staffmanagement.test', 'role' => 'staff', 'nearest_station' => '渋谷駅', 'phone' => '090-4444-4444', 'base_fee' => 9500, 'has_car' => true, 'car_number' => '品川 な 2040'],
            ['name' => '中村 翔', 'email' => 'nakamura@staffmanagement.test', 'role' => 'trainer', 'nearest_station' => '横浜駅', 'phone' => '090-5555-5555', 'base_fee' => 11000],
            ['name' => '鈴木 次郎', 'email' => 'spot@staffmanagement.test', 'role' => 'temporary', 'nearest_station' => '上野駅', 'phone' => '080-6666-6666', 'base_fee' => 9000],
        ];

        foreach ($users as $attributes) {
            User::query()->create(array_merge([
                'password' => Hash::make('password123'),
                'account_status' => $attributes['role'] === 'temporary' ? 'no_login' : 'active',
                'notification_method' => isset($attributes['line_user_id']) ? 'line' : 'email',
                'email_verified_at' => $now,
            ], $attributes));
        }

        $admin = User::query()->where('role', 'employee')->firstOrFail();
        $clients = [
            ['name' => 'サンプル商事 株式会社', 'type' => 'corporation', 'department' => '地域連携部', 'phone' => '03-1234-5678', 'address' => '東京都新宿区西新宿1-1-1', 'proposal_status' => 'proposing', 'next_action_date' => now()->addDays(9), 'proposal_notes' => '来年度の健康づくり事業を提案中。', 'sales_owner_id' => $admin->id],
            ['name' => 'サンプル町会', 'type' => 'organization', 'phone' => '03-2345-6789', 'proposal_status' => 'won', 'sales_owner_id' => $admin->id],
            ['name' => 'サンプル市', 'type' => 'government', 'department' => '健康推進課', 'phone' => '03-3456-7890', 'proposal_status' => 'approaching', 'sales_owner_id' => $admin->id],
            ['name' => 'サンプル商店会', 'type' => 'organization', 'phone' => '03-4567-8901', 'proposal_status' => 'uncontacted', 'sales_owner_id' => $admin->id],
        ];

        foreach ($clients as $attributes) {
            Client::query()->create($attributes);
        }

        $primaryClient = Client::query()->firstOrFail();
        DB::table('client_contacts')->insert(['client_id' => $primaryClient->id, 'position' => '課長', 'name' => '森 一樹', 'mobile' => '090-7777-7777', 'email' => 'mori@example.test', 'created_at' => $now, 'updated_at' => $now]);

        foreach (['秋季健康フェア', 'ハロウィンマルシェ', '市民マラソン', '企業説明会', '文化祭'] as $index => $eventName) {
            $event = Event::query()->create([
                'client_id' => Client::query()->skip($index % 4)->firstOrFail()->id,
                'business_name' => $index === 0 ? '地域イベント運営' : 'イベント運営スタッフ派遣',
                'name' => $eventName, 'details' => '来場者の受付・検温・会場案内を担当します。', 'work_content' => '受付、誘導、会場運営',
                'event_date' => now()->addDays(2 + ($index * 4))->toDateString(),
                'meeting_time' => '08:30', 'start_time' => '09:00', 'end_time' => '17:00', 'dismissal_time' => '17:30',
                'required_staff' => 5 + ($index % 3), 'meeting_place' => 'サンプル公民館 1階ロビー', 'venue' => 'サンプル公民館 2階 大ホール',
                'items' => ['昼食', '室内履き', 'ハイクラスTシャツ', '筆記用具'], 'emergency_contacts' => ['ハイクラスLINE', '山田 太郎'],
                'notes' => '駐車場はありません。公共交通機関でお越しください。', 'status' => $index === 0 ? 'recruiting' : 'confirmed',
                'report_required' => true, 'created_by' => $admin->id,
            ]);

            foreach (User::query()->whereIn('role', ['staff', 'semi_employee', 'trainer', 'temporary'])->limit(3)->get() as $staffIndex => $staff) {
                Assignment::query()->create([
                    'event_id' => $event->id, 'user_id' => $staff->id, 'status' => $staffIndex < 2 ? 'confirmed' : 'requested',
                    'special_notes' => $staffIndex === 0 ? '受付の担当です。' : null, 'fee' => $staff->base_fee, 'transportation' => 480,
                    'payment_status' => 'unconfirmed', 'is_day_leader' => $staffIndex === 0, 'is_report_representative' => $staffIndex === 0, 'requested_at' => $now,
                ]);
            }
        }

        DB::table('qualifications')->insert([
            ['user_id' => 3, 'name' => '普通自動車免許', 'expires_at' => now()->addYears(3), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => 3, 'name' => '防災士', 'expires_at' => now()->addDays(25), 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('invoice_items')->insert(['event_id' => 1, 'type' => 'work_fee', 'memo' => '運営スタッフ費', 'amount' => 90000, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('expenses')->insert(['event_id' => 1, 'type' => 'supplies', 'memo' => '消耗品', 'amount' => 4000, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('notification_logs')->insert(['user_id' => 3, 'type' => 'assignment_request', 'channel' => 'line', 'destination' => 'demo-line-staff', 'status' => 'sent', 'subject' => 'お仕事のご依頼', 'body' => '秋季健康フェアのお仕事のご依頼です。', 'sent_at' => $now, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('phone_logs')->insert(['called_at' => $now, 'caller_name' => '森 様', 'company' => 'サンプル商事', 'phone' => '03-1234-5678', 'purpose' => 'お仕事の依頼・相談', 'content' => '次回イベントについて折り返し希望。', 'recipient_id' => $admin->id, 'received_by' => 2, 'status' => 'callback_required', 'created_at' => $now, 'updated_at' => $now]);
    }
}
