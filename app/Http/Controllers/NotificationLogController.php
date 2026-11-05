<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Services\NotificationDeliveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isEmployee(), 403);

        $logs = NotificationLog::query()
            ->with('user')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->when($request->filled('channel'), fn ($query) => $query->where('channel', $request->string('channel')))
            ->when($request->filled('keyword'), fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$request->string('keyword').'%')))
            ->when($request->input('period', 'seven_days') === 'today', fn ($query) => $query->whereDate('created_at', today()))
            ->when($request->input('period', 'seven_days') === 'seven_days', fn ($query) => $query->where('created_at', '>=', now()->subDays(7)))
            ->when($request->input('period', 'seven_days') === 'month', fn ($query) => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('static-pages.SendLog', [
            'notificationLogsPage' => $logs,
            'failedCount' => NotificationLog::query()->where('status', 'failed')->count(),
            'notificationTypes' => NotificationLog::query()->whereNotNull('type')->distinct()->orderBy('type')->pluck('type'),
        ]);
    }

    public function retry(
        Request $request,
        NotificationLog $notificationLog,
        NotificationDeliveryService $delivery,
    ): RedirectResponse {
        abort_unless($request->user()?->isEmployee(), 403);
        abort_unless($notificationLog->status === 'failed', 422);

        $result = $delivery->retry($notificationLog);

        return back()->with(
            $result->status === 'sent' ? 'status' : 'error',
            $result->status === 'sent' ? '再送しました。' : '再送できませんでした。送信先と設定を確認してください。',
        );
    }
}
