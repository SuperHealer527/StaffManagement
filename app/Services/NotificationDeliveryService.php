<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\User;
use App\Notifications\ActionLinkNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class NotificationDeliveryService
{
    public function deliver(
        User $user,
        Model $context,
        string $type,
        string $subject,
        string $body,
        ?string $actionUrl = null,
        string $actionLabel = '確認する',
    ): NotificationLog {
        $usesLine = $user->notification_method === 'line'
            && filled($user->line_user_id)
            && filled(config('services.line.channel_access_token'));
        $channel = $usesLine ? 'line' : 'email';

        $log = NotificationLog::query()->create([
            'user_id' => $user->id,
            'notifiable_context_type' => $context->getMorphClass(),
            'notifiable_context_id' => $context->getKey(),
            'type' => $type,
            'channel' => $channel,
            'destination' => $usesLine ? $user->line_user_id : $user->email,
            'status' => 'pending',
            'subject' => $subject,
            'body' => $body,
        ]);

        return $this->send($log, $user, $body, $actionUrl, $actionLabel);
    }

    public function retry(NotificationLog $log): NotificationLog
    {
        $user = $log->user;

        if ($user === null) {
            $log->update(['status' => 'failed', 'error_message' => '送信先のアカウントが見つかりません。']);

            return $log->refresh();
        }

        $actionUrl = null;
        $body = (string) $log->body;
        if (preg_match('/\n\[ACTION_URL\](.+)$/u', $body, $matches) === 1) {
            $actionUrl = trim($matches[1]);
            $body = preg_replace('/\n\[ACTION_URL\].+$/u', '', $body) ?? $body;
        }

        $usesLine = $user->notification_method === 'line'
            && filled($user->line_user_id)
            && filled(config('services.line.channel_access_token'));
        $log->update([
            'channel' => $usesLine ? 'line' : 'email',
            'destination' => $usesLine ? $user->line_user_id : $user->email,
            'status' => 'pending',
            'error_message' => null,
        ]);

        return $this->send($log, $user, $body, $actionUrl);
    }

    private function send(
        NotificationLog $log,
        User $user,
        string $body,
        ?string $actionUrl,
        string $actionLabel = '確認する',
    ): NotificationLog {
        try {
            if ($log->channel === 'line') {
                Http::withToken((string) config('services.line.channel_access_token'))
                    ->post('https://api.line.me/v2/bot/message/push', [
                        'to' => $user->line_user_id,
                        'messages' => $this->lineMessages($log, $body, $actionUrl, $actionLabel),
                    ])->throw();
            } else {
                if (blank($user->email)) {
                    throw new \RuntimeException('メールアドレスが登録されていません。');
                }

                $user->notify(new ActionLinkNotification(
                    (string) $log->subject,
                    $body,
                    $actionUrl,
                    $actionLabel,
                ));
            }

            $log->update([
                'status' => 'sent',
                'body' => $body.($actionUrl === null ? '' : "\n[ACTION_URL]".$actionUrl),
                'error_message' => null,
                'sent_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
            $log->update([
                'status' => 'failed',
                'body' => $body.($actionUrl === null ? '' : "\n[ACTION_URL]".$actionUrl),
                'error_message' => mb_substr($exception->getMessage(), 0, 2000),
                'sent_at' => null,
            ]);
        }

        return $log->refresh();
    }

    /** @return list<array<string, mixed>> */
    private function lineMessages(
        NotificationLog $log,
        string $body,
        ?string $actionUrl,
        string $actionLabel,
    ): array {
        if ($actionUrl === null) {
            return [['type' => 'text', 'text' => $body]];
        }

        $token = basename((string) parse_url($actionUrl, PHP_URL_PATH));
        $actions = match ($log->type) {
            'assignment_request' => [
                ['type' => 'postback', 'label' => '承諾する', 'data' => http_build_query(['token' => $token, 'response' => 'accepted']), 'displayText' => '承諾します'],
                ['type' => 'postback', 'label' => 'まだ分からない', 'data' => http_build_query(['token' => $token, 'response' => 'unknown']), 'displayText' => 'まだ分かりません'],
                ['type' => 'postback', 'label' => '辞退する', 'data' => http_build_query(['token' => $token, 'response' => 'declined']), 'displayText' => '辞退します'],
                ['type' => 'uri', 'label' => '詳しく見る', 'uri' => $actionUrl],
            ],
            'attendance_7_day', 'attendance_5_day', 'attendance_3_day', 'attendance_same_day' => [
                ['type' => 'postback', 'label' => '確認しました', 'data' => http_build_query(['token' => $token, 'attendance' => 'confirmed']), 'displayText' => '出勤を確認しました'],
                ['type' => 'uri', 'label' => '詳しく見る', 'uri' => $actionUrl],
            ],
            default => [['type' => 'uri', 'label' => Str::limit($actionLabel, 20, ''), 'uri' => $actionUrl]],
        };

        return [[
            'type' => 'template',
            'altText' => Str::limit($body, 400, ''),
            'template' => [
                'type' => 'buttons',
                'text' => Str::limit($body, 160, ''),
                'actions' => $actions,
            ],
        ]];
    }
}
