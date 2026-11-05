<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUiActionRequest;
use App\Models\Assignment;
use App\Models\Availability;
use App\Models\NotificationLog;
use App\Models\PhoneLog;
use App\Models\User;
use App\Notifications\AccountInvitationNotification;
use App\Services\NotificationDeliveryService;
use App\Services\SecureActionLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class UiActionController extends Controller
{
    public function __construct(
        private SecureActionLinkService $secureActionLinkService,
        private NotificationDeliveryService $notificationDeliveryService,
    ) {}

    public function __invoke(StoreUiActionRequest $request, string $action): JsonResponse
    {
        $values = $request->validated();

        DB::transaction(function () use ($request, $action, $values): void {
            match ($action) {
                'account-create' => $this->createAccount($values),
                'phone-log-create' => PhoneLog::query()->create([...$values, 'received_by' => $request->user()->id]),
                'phone-log-complete' => PhoneLog::query()->findOrFail($values['phone_log_id'])->update(['status' => 'completed']),
                'account-status' => $this->updateAccountStatus($request, $values),
                'account-role' => $this->updateAccountRole($request, $values),
                'notification-retry' => $this->retryNotification($values),
                'availability' => Availability::query()->updateOrCreate(
                    ['user_id' => $request->user()->id, 'weekday' => $values['weekday'], 'time_slot' => $values['time_slot']],
                    ['availability' => $values['availability']],
                ),
                'assignment-status' => Assignment::query()->findOrFail($values['assignment_id'])->update([
                    'status' => $values['status'],
                    'responded_at' => now(),
                ]),
                'payment-paid' => Assignment::query()->whereIn('id', $values['assignment_ids'])->update([
                    'payment_status' => 'paid',
                    'paid_at' => today(),
                ]),
                default => abort(404),
            };
        });

        return response()->json(['message' => '保存しました。']);
    }

    /** @param array<string, mixed> $values */
    private function updateAccountStatus(StoreUiActionRequest $request, array $values): void
    {
        abort_if($request->user()->id === $values['user_id'], 422, '自分自身のログイン状態は変更できません。');

        $user = User::query()->findOrFail($values['user_id']);
        if ($user->role === 'temporary') {
            abort_unless(in_array($values['account_status'], ['no_login', 'suspended'], true), 422, '単発の方は利用停止またはログインなしに設定してください。');
        }

        $user->update([
            'account_status' => $values['account_status'],
            'suspended_at' => $values['account_status'] === 'suspended' ? now() : null,
        ]);

        if ($values['account_status'] === 'invited') {
            $user->update(['invited_at' => now()]);
            $this->queueInvitation($user);
        }
    }

    /** @param array<string, mixed> $values */
    private function updateAccountRole(StoreUiActionRequest $request, array $values): void
    {
        abort_if($request->user()->id === $values['user_id'], 422, '自分自身の区分は変更できません。');

        $user = User::query()->findOrFail($values['user_id']);
        $wasTemporary = $user->role === 'temporary';
        $isTemporary = $values['role'] === 'temporary';

        $user->update([
            'role' => $values['role'],
            'account_status' => $isTemporary ? 'no_login' : ($wasTemporary ? 'invited' : $user->account_status),
            'invited_at' => $wasTemporary && ! $isTemporary ? now() : $user->invited_at,
            'suspended_at' => $isTemporary ? null : $user->suspended_at,
        ]);

        if ($wasTemporary && ! $isTemporary) {
            $this->queueInvitation($user);
        }
    }

    /** @param array<string, mixed> $values */
    private function createAccount(array $values): void
    {
        $user = User::query()->create([
            ...$values,
            'password' => Hash::make(Str::password()),
            'account_status' => $values['role'] === 'temporary' ? 'no_login' : 'invited',
            'invited_at' => $values['role'] === 'temporary' ? null : now(),
        ]);

        if ($user->role !== 'temporary') {
            $this->queueInvitation($user);
        } else {
            $this->queueLineConnection($user);
        }
    }

    private function queueInvitation(User $user): void
    {
        $plainToken = $this->secureActionLinkService->create($user, 'first_password', $user);
        $actionUrl = route('public-actions.show', $plainToken);
        $notificationLog = NotificationLog::query()->create([
            'user_id' => $user->id,
            'notifiable_context_type' => User::class,
            'notifiable_context_id' => $user->id,
            'type' => 'account_invitation',
            'channel' => 'email',
            'destination' => $user->email,
            'status' => 'pending',
            'subject' => 'ハイクラス業務管理システムへのご招待',
            'body' => "初回パスワード設定リンクを送信しました。\n[ACTION_URL]".$actionUrl,
        ]);

        DB::afterCommit(function () use ($actionUrl, $notificationLog, $user): void {
            try {
                $user->notify(new AccountInvitationNotification($actionUrl));
                $notificationLog->update(['status' => 'sent', 'sent_at' => now()]);
            } catch (Throwable $exception) {
                $notificationLog->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
            }
        });
    }

    private function queueLineConnection(User $user): void
    {
        $plainToken = $this->secureActionLinkService->create($user, 'line_connect', $user, 24);
        $this->notificationDeliveryService->deliver(
            $user,
            $user,
            'line_connect',
            '公式LINEの登録のお願い',
            'お仕事の依頼や出勤確認を受け取るため、公式LINEと連携してください。本人専用のリンクです。',
            route('public-actions.show', $plainToken),
            '公式LINEと連携する',
        );
    }

    /** @param array<string, mixed> $values */
    private function retryNotification(array $values): void
    {
        $notificationLog = NotificationLog::query()->findOrFail($values['notification_log_id']);
        abort_unless($notificationLog->status === 'failed', 422, '送信失敗の履歴だけ再送できます。');

        $this->notificationDeliveryService->retry($notificationLog);
    }
}
