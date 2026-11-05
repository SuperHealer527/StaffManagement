<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkUpdatePaymentsRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Assignment;
use App\Services\AuditService;
use App\Services\NotificationDeliveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(
        private AuditService $auditService,
        private NotificationDeliveryService $notificationDeliveryService,
    ) {}

    public function update(UpdatePaymentRequest $request, Assignment $assignment): RedirectResponse
    {
        abort_unless(in_array($assignment->status, ['confirmed', 'completed'], true), 422);
        $oldValues = $assignment->only(['fee', 'transportation', 'reimbursement', 'withholding_tax', 'payment_status', 'payment_source', 'paid_at']);
        $assignment->update($request->validated());
        $this->auditService->record($assignment, 'update_payment', $oldValues, $assignment->only(array_keys($oldValues)));
        if ($oldValues['payment_status'] !== 'paid' && $assignment->payment_status === 'paid') {
            $this->sendPaymentNotice($assignment);
        }

        return back()->with('status', '支払状況を更新しました。');
    }

    public function bulkUpdate(BulkUpdatePaymentsRequest $request): RedirectResponse
    {
        $values = $request->validated();
        $assignments = Assignment::query()->whereKey($values['assignment_ids'])->whereIn('status', ['confirmed', 'completed'])->get();
        abort_unless($assignments->count() === count($values['assignment_ids']), 422);

        DB::transaction(function () use ($assignments, $values): void {
            foreach ($assignments as $assignment) {
                $oldValues = $assignment->only(['payment_status', 'payment_source', 'paid_at']);
                $assignment->update([
                    'payment_status' => $values['payment_status'],
                    'payment_source' => $values['payment_status'] === 'paid' ? $values['payment_source'] : null,
                    'paid_at' => $values['payment_status'] === 'paid' ? $values['paid_at'] : null,
                ]);
                $this->auditService->record($assignment, 'bulk_update_payment', $oldValues, $assignment->only(array_keys($oldValues)));
                if ($oldValues['payment_status'] !== 'paid' && $assignment->payment_status === 'paid') {
                    $this->sendPaymentNotice($assignment);
                }
            }
        });

        return back()->with('status', $assignments->count().'件の支払状況を更新しました。');
    }

    private function sendPaymentNotice(Assignment $assignment): void
    {
        $assignment->loadMissing(['event', 'user']);
        if ($assignment->user->role !== 'temporary') {
            return;
        }

        $this->notificationDeliveryService->deliver(
            $assignment->user,
            $assignment,
            'payment_notice',
            'お支払いのお知らせ：'.($assignment->event->name ?: $assignment->event->business_name),
            sprintf(
                "%s（%s）のお支払いが完了しました。\n謝金 %s円＋交通費 %s円＋立替 %s円－源泉税 %s円\nお振込額 %s円\n支払日 %s（%s）",
                $assignment->event->name ?: $assignment->event->business_name,
                $assignment->event->event_date->format('Y年n月j日'),
                number_format($assignment->fee),
                number_format($assignment->transportation),
                number_format($assignment->reimbursement),
                number_format($assignment->withholding_tax),
                number_format($assignment->total_payment),
                $assignment->paid_at?->format('Y年n月j日') ?? now()->format('Y年n月j日'),
                $assignment->payment_source ?: '支払元未設定',
            ),
        );
    }
}
