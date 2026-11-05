<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Client;
use App\Models\Event;
use App\Models\Interview;
use App\Models\Meeting;
use App\Models\NotificationLog;
use App\Models\PhoneLog;
use App\Models\User;
use Illuminate\View\View;

class ScreenController extends Controller
{
    /** @var array<string, string> */
    private const TEMPLATES = [
        'staff-home' => 'Staff',
        'semi-home' => 'SubMain',
        'assignments' => 'Assign',
        'event-form' => 'EventEdit',
        'event-copy' => 'Duplicate',
        'event-detail' => 'EventDetail',
        'assignment-response' => 'Respond',
        'attendance-confirmed' => 'AttendanceComplete',
        'report-submit' => 'Report',
        'payments' => 'PayManage',
        'profit-loss' => 'Profit',
        'payment-statement' => 'Payslip',
        'profile' => 'Profile',
        'reports' => 'ReportView',
        'clients' => 'Clients',
        'client-detail' => 'ClientEdit',
        'events' => 'EventList',
        'staff' => 'StaffList',
        'staff-detail' => 'StaffDetail',
        'shortages' => 'Shortage',
        'accounts' => 'Accounts',
        'notifications' => 'SendLog',
        'my-requests' => 'MyRequests',
        'first-password' => 'SetPassword',
        'mobile-areas' => 'AreaSwitch',
        'sales-home' => 'SalesHome',
        'proposal-board' => 'SalesBoard',
        'meeting-history' => 'Meetings',
        'interviews' => 'Coaching',
        'interview-select' => 'InterviewSchedule',
        'phone-log' => 'PhoneLog',
        'line-connect' => 'LineConnect',
        'temporary-bank' => 'PaymentDetails',
    ];

    /** @var array<string, string> */
    private const TITLES = [
        'staff-home' => 'スタッフのホーム',
        'semi-home' => '準社員のホーム',
        'assignments' => 'アサイン',
        'event-form' => 'イベント登録・編集',
        'event-copy' => 'イベントの複製',
        'event-detail' => 'イベントの詳細',
        'assignment-response' => '依頼への回答',
        'attendance-confirmed' => '出勤確認の完了',
        'report-submit' => '報告書の提出',
        'payments' => '支払管理',
        'profit-loss' => '収支一覧',
        'payment-statement' => '支払明細',
        'profile' => 'マイプロフィール',
        'reports' => '報告書の確認',
        'clients' => '依頼先一覧',
        'client-detail' => '依頼先の詳細・編集',
        'events' => 'イベント一覧',
        'staff' => 'スタッフ一覧',
        'staff-detail' => 'スタッフ詳細',
        'shortages' => '人数不足一覧',
        'accounts' => 'アカウント管理',
        'notifications' => '送信履歴',
        'my-requests' => '自分への依頼一覧',
        'first-password' => '初回パスワード設定',
        'mobile-areas' => 'エリア切り替え',
        'sales-home' => '営業ホーム',
        'proposal-board' => '提案状況ボード',
        'meeting-history' => '打合せ履歴',
        'interviews' => '面談の予定・記録',
        'interview-select' => '面談の日程を選ぶ',
        'phone-log' => '電話記録簿',
        'line-connect' => '単発の人のLINE連携',
        'temporary-bank' => '振込先と住所の入力',
    ];

    /** @var list<string> */
    private const EMPLOYEE_ONLY = [
        'event-form', 'event-copy', 'payments', 'profit-loss', 'client-detail', 'accounts',
        'notifications', 'sales-home', 'proposal-board', 'meeting-history', 'interviews',
    ];

    /** @var list<string> */
    private const MANAGEMENT = [
        'semi-home', 'assignments', 'event-detail', 'reports', 'clients', 'events', 'staff',
        'staff-detail', 'shortages', 'phone-log',
    ];

    public function show(string $screen): View
    {
        abort_unless(isset(self::TITLES[$screen]), 404);

        /** @var User $user */
        $user = request()->user();
        $user->loadMissing('qualifications');
        if (in_array($screen, self::EMPLOYEE_ONLY, true)) {
            abort_unless($user->isEmployee(), 403);
        }
        if (in_array($screen, self::MANAGEMENT, true)) {
            abort_unless($user->canManageAssignments(), 403);
        }

        $events = Event::query()
            ->with(['client.contacts', 'assignments.user', 'invoiceItems', 'expenses', 'reportQuestions', 'reportSubmissions'])
            ->withCount(['assignments as confirmed_count' => fn ($query) => $query->where('status', 'confirmed')])
            ->orderBy('event_date')
            ->get();
        $clients = Client::query()
            ->with(['contacts', 'events.invoiceItems', 'events.assignments', 'events.expenses', 'salesOwner', 'meetings.recorder'])
            ->orderBy('name')
            ->get();
        $staffMembers = User::query()
            ->with(['qualifications', 'assignments.event'])
            ->whereIn('role', ['employee', 'semi_employee', 'staff', 'temporary', 'trainer'])
            ->orderBy('name')
            ->get();
        $assignments = Assignment::query()->with(['event.client', 'user'])->latest()->get();

        return view('static-pages.'.self::TEMPLATES[$screen], [
            'screen' => $screen,
            'title' => self::TITLES[$screen],
            'events' => $events,
            'event' => $events->first(),
            'clients' => $clients,
            'client' => $clients->first(),
            'staffMembers' => $staffMembers,
            'staffMember' => $staffMembers->firstWhere('role', 'staff') ?? $staffMembers->first(),
            'assignments' => $assignments,
            'myAssignments' => $assignments->where('user_id', $user->id),
            'notificationLogs' => NotificationLog::query()->with('user')->latest()->get(),
            'meetings' => Meeting::query()->with(['client', 'recorder'])->latest('meeting_date')->get(),
            'interviews' => Interview::query()->with(['subject', 'manager'])->latest('scheduled_at')->get(),
            'phoneLogs' => PhoneLog::query()->with(['recipient', 'receiver'])->latest('called_at')->get(),
        ]);
    }
}
