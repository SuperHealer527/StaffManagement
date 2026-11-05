<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@php
    $isMobilePage = in_array(request()->route('screen'), [
        'staff-home', 'my-requests', 'assignment-response', 'attendance-confirmed',
        'report-submit', 'payment-statement', 'profile', 'first-password',
        'mobile-areas', 'interview-select', 'line-connect', 'temporary-bank',
    ], true);
    $section = match (true) {
        request()->is('screens/sales-*'), request()->is('screens/proposal-*'), request()->is('screens/meeting-*') => '営業',
        request()->is('screens/interview*') => '面談',
        request()->is('screens/phone-log') => '総務',
        request()->is('screens/payment*'), request()->is('screens/profit-loss') => '会計',
        request()->is('screens/accounts'), request()->is('screens/notifications') => '設定',
        default => '事業',
    };
@endphp
<div class="app-shell {{ $isMobilePage ? 'mobile-focused' : '' }}">
    <aside class="area-rail" aria-label="エリア切り替え">
        <a href="{{ route('dashboard') }}" class="brand-mark" aria-label="ホーム">HC</a>
        <nav class="area-nav">
            @foreach ([
                ['事業', '▣', 'dashboard'], ['営業', '⌁', 'sales-home'], ['面談', '◇', 'interviews'],
                ['総務', '▤', 'phone-log'], ['会計', '¥', 'payments'], ['設定', '☷', 'accounts'],
            ] as [$label, $icon, $target])
                <a href="{{ $target === 'dashboard' ? route('dashboard') : route('screens.show', $target) }}"
                   class="area-link {{ $section === $label ? 'active' : '' }}">
                    <span aria-hidden="true">{{ $icon }}</span><small>{{ $label }}</small>
                </a>
            @endforeach
        </nav>
    </aside>

    <aside class="side-menu">
        <div class="side-heading">
            <p>ハイクラス業務管理システム</p>
            <strong>{{ $section }}{{ $section === '事業' ? '（イベント運営）' : '' }}</strong>
        </div>
        <nav class="side-links">
            @php
                $menus = match ($section) {
                    '営業' => [['営業ホーム', 'sales-home'], ['提案状況', 'proposal-board'], ['打合せ履歴', 'meeting-history'], ['依頼先', 'clients']],
                    '面談' => [['面談の予定・記録', 'interviews'], ['スタッフ', 'staff']],
                    '総務' => [['電話記録簿', 'phone-log'], ['スタッフ', 'staff'], ['依頼先', 'clients']],
                    '会計' => [['支払管理', 'payments'], ['収支一覧', 'profit-loss']],
                    '設定' => [['アカウント管理', 'accounts'], ['送信履歴', 'notifications']],
                    default => [['ホーム', 'dashboard'], ['イベント', 'events'], ['アサイン', 'assignments'], ['人数不足一覧', 'shortages'], ['報告書', 'reports'], ['スタッフ', 'staff'], ['依頼先', 'clients']],
                };
            @endphp
            @foreach ($menus as [$label, $target])
                <a href="{{ $target === 'dashboard' ? route('dashboard') : route('screens.show', $target) }}"
                   class="side-link {{ ($target === 'dashboard' && request()->routeIs('dashboard')) || request()->route('screen') === $target ? 'active' : '' }}">
                    <span>{{ $label }}</span>@if($target === 'clients')<small>共通</small>@endif
                </a>
            @endforeach
        </nav>
        <div class="account-block">
            <a href="{{ route('screens.show', 'profile') }}">
                <span>{{ auth()->user()->name }}（{{ auth()->user()->role === 'employee' ? '社員' : (auth()->user()->role === 'semi_employee' ? '準社員' : 'スタッフ') }}）</span>
                <strong>自分のページを開く ›</strong>
            </a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button>ログアウト</button></form>
        </div>
    </aside>

    <div class="app-content">
        <header class="mobile-header">
            <a href="{{ route('dashboard') }}"><span class="mobile-logo">HC</span><strong>ハイクラス業務管理</strong></a>
            <a href="{{ route('screens.show', 'profile') }}" class="mobile-user">{{ mb_substr(auth()->user()->name, 0, 1) }}</a>
        </header>
        <main class="page-container">@yield('content')</main>
    </div>

    <nav class="bottom-nav" aria-label="スマートフォンメニュー">
        @foreach ([['事業', '▣', 'dashboard'], ['営業', '⌁', 'sales-home'], ['会計', '¥', 'payments'], ['メニュー', '☷', 'mobile-areas']] as [$label, $icon, $target])
            <a href="{{ $target === 'dashboard' ? route('dashboard') : route('screens.show', $target) }}"><span>{{ $icon }}</span>{{ $label }}</a>
        @endforeach
    </nav>
</div>
</body>
</html>
