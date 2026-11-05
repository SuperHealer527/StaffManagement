<?php

namespace Database\Seeders;

use App\Models\InboundEmail;
use Illuminate\Database\Seeder;

class InboundEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $messages = [
            [
                'provider_message_id' => 'demo-unmatched-quotation',
                'sender_email' => 'unknown-customer@example.test',
                'recipient_emails' => ['soumu@highclass-inc.com'],
                'subject' => 'イベント運営のお見積りについて',
                'body_excerpt' => '来月開催予定のイベントについて、運営スタッフのお見積りをお願いします。',
                'received_at' => '2026-10-23 13:20:00',
            ],
            [
                'provider_message_id' => 'demo-unmatched-schedule',
                'sender_email' => 'event-office@example.test',
                'recipient_emails' => ['soumu@highclass-inc.com'],
                'subject' => '実施日程のご相談',
                'body_excerpt' => '候補日をお送りします。対応可能な日程をご確認ください。',
                'received_at' => '2026-10-22 16:45:00',
            ],
        ];

        foreach ($messages as $message) {
            InboundEmail::query()->updateOrCreate(
                ['provider_message_id' => $message['provider_message_id']],
                [...$message, 'status' => 'pending', 'client_id' => null, 'meeting_id' => null],
            );
        }
    }
}
