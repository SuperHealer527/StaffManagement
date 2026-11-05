<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\SecureActionToken;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class LineWebhookController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function __invoke(Request $request): Response
    {
        $body = $request->getContent();
        $secret = (string) config('services.line.channel_secret');
        abort_if($secret === '', 503, 'LINE channel secret is not configured.');
        $expectedSignature = base64_encode(hash_hmac('sha256', $body, $secret, true));
        $receivedSignature = (string) $request->header('X-Line-Signature');
        abort_unless($receivedSignature !== '' && hash_equals($expectedSignature, $receivedSignature), 403);

        foreach ((array) $request->input('events', []) as $event) {
            if (data_get($event, 'type') !== 'postback') {
                continue;
            }

            parse_str((string) data_get($event, 'postback.data'), $data);
            $message = $this->processPostback($data);
            $replyToken = data_get($event, 'replyToken');
            if (is_string($replyToken) && $replyToken !== '') {
                $this->reply($replyToken, $message);
            }
        }

        return response()->noContent();
    }

    /** @param array<string, mixed> $data */
    private function processPostback(array $data): string
    {
        $plainToken = $data['token'] ?? null;
        if (! is_string($plainToken) || $plainToken === '') {
            return 'この操作は受け付けられませんでした。';
        }

        $actionToken = SecureActionToken::query()
            ->with(['user', 'actionable'])
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();
        if ($actionToken === null || ! $actionToken->isUsable() || ! $actionToken->actionable instanceof Assignment) {
            return 'リンクの有効期限が切れています。担当者にご連絡ください。';
        }

        if ($actionToken->opened_at === null) {
            $actionToken->update(['opened_at' => now()]);
        }

        $assignment = $actionToken->actionable;
        if ($actionToken->purpose === 'assignment_response') {
            $response = $data['response'] ?? null;
            if (! is_string($response)
                || ! in_array($response, ['accepted', 'unknown', 'declined'], true)
                || ! in_array($assignment->status, ['requested', 'unknown', 'accepted'], true)) {
                return '確定済みまたは終了済みのため、回答は変更できません。';
            }

            $oldStatus = $assignment->status;
            $assignment->update(['status' => $response, 'responded_at' => now()]);
            $this->auditService->record($assignment, 'line_assignment_response', ['status' => $oldStatus], ['status' => $response]);

            return ['accepted' => '承諾で回答しました。', 'unknown' => '未定で回答しました。', 'declined' => '辞退で回答しました。'][$response];
        }

        if (in_array($actionToken->purpose, ['attendance_confirmation', 'attendance_seven_day', 'attendance_same_day'], true)) {
            $field = $actionToken->purpose === 'attendance_seven_day' ? 'seven_day_confirmation' : 'same_day_confirmation';
            $assignment->update([$field => 'confirmed']);
            $actionToken->update(['used_at' => now()]);
            $this->auditService->record($assignment, 'line_attendance_confirmation', null, [$field => 'confirmed']);

            return '出勤を確認しました。ありがとうございます。';
        }

        return 'この操作はLINE上では受け付けられません。リンクを開いてください。';
    }

    private function reply(string $replyToken, string $message): void
    {
        if (blank(config('services.line.channel_access_token'))) {
            return;
        }

        Http::withToken((string) config('services.line.channel_access_token'))
            ->post('https://api.line.me/v2/bot/message/reply', [
                'replyToken' => $replyToken,
                'messages' => [['type' => 'text', 'text' => $message]],
            ]);
    }
}
