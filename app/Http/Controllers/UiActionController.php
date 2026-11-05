<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUiActionRequest;
use App\Models\Assignment;
use App\Models\Availability;
use App\Models\NotificationLog;
use App\Models\PhoneLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UiActionController extends Controller
{
    public function __invoke(StoreUiActionRequest $request, string $action): JsonResponse
    {
        $values = $request->validated();

        DB::transaction(function () use ($request, $action, $values): void {
            match ($action) {
                'account-create' => User::query()->create([
                    ...$values,
                    'password' => Hash::make(Str::password()),
                    'account_status' => $values['role'] === 'temporary' ? 'no_login' : 'invited',
                    'invited_at' => now(),
                ]),
                'phone-log-create' => PhoneLog::query()->create([...$values, 'received_by' => $request->user()->id]),
                'phone-log-complete' => PhoneLog::query()->findOrFail($values['phone_log_id'])->update(['status' => 'completed']),
                'account-status' => $this->updateAccountStatus($request, $values),
                'account-role' => $this->updateAccountRole($request, $values),
                'notification-retry' => NotificationLog::query()->findOrFail($values['notification_log_id'])->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'error_message' => null,
                ]),
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

        User::query()->findOrFail($values['user_id'])->update([
            'account_status' => $values['account_status'],
            'suspended_at' => $values['account_status'] === 'suspended' ? now() : null,
        ]);
    }

    /** @param array<string, mixed> $values */
    private function updateAccountRole(StoreUiActionRequest $request, array $values): void
    {
        abort_if($request->user()->id === $values['user_id'], 422, '自分自身の区分は変更できません。');

        User::query()->findOrFail($values['user_id'])->update(['role' => $values['role']]);
    }
}
