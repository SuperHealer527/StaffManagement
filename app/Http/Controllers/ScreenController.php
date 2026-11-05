<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Client;
use App\Models\Event;
use App\Models\User;
use Illuminate\View\View;

class ScreenController extends Controller
{
    /** @var array<string, string> */
    private const TITLES = [
        'staff-home' => 'スタッフのホーム', 'semi-home' => '準社員のホーム', 'assignments' => 'アサイン', 'event-form' => 'イベント登録・編集',
        'event-copy' => 'イベントの複製', 'event-detail' => 'イベントの詳細', 'assignment-response' => '依頼への回答', 'attendance-confirmed' => '出勤確認の完了',
        'report-submit' => '報告書の提出', 'payments' => '支払管理', 'profit-loss' => '収支一覧', 'payment-statement' => '支払明細',
        'profile' => 'マイプロフィール', 'reports' => '報告書の確認', 'clients' => '依頼先一覧', 'client-detail' => '依頼先の詳細・編集',
        'events' => 'イベント一覧', 'staff' => 'スタッフ一覧', 'staff-detail' => 'スタッフ詳細', 'shortages' => '人数不足一覧',
        'accounts' => 'アカウント管理', 'notifications' => '送信履歴', 'my-requests' => '自分への依頼一覧', 'first-password' => '初回パスワード設定',
        'mobile-areas' => 'エリア切り替え', 'sales-home' => '営業ホーム', 'proposal-board' => '提案状況ボード', 'meeting-history' => '打合せ履歴',
        'interviews' => '面談の予定・記録', 'interview-select' => '面談の日程を選ぶ', 'phone-log' => '電話記録簿', 'line-connect' => '単発の人のLINE連携',
        'temporary-bank' => '振込先と住所の入力',
    ];

    public function show(string $screen): View
    {
        abort_unless(isset(self::TITLES[$screen]), 404);

        return view('screens.show', [
            'screen' => $screen, 'title' => self::TITLES[$screen],
            'events' => Event::query()->with('client')->withCount('assignments')->orderBy('event_date')->get(),
            'clients' => Client::query()->with('contacts')->orderBy('name')->get(),
            'staffMembers' => User::query()->with('qualifications')->whereIn('role', ['employee', 'semi_employee', 'staff', 'temporary', 'trainer'])->orderBy('name')->get(),
            'assignments' => Assignment::query()->with(['event.client', 'user'])->latest()->get(),
        ]);
    }
}
