<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f7f8fa] text-[#20242d] antialiased">
<div class="min-h-screen lg:flex">
    <aside class="hidden w-[72px] shrink-0 flex-col items-center bg-[#20262f] py-5 text-white lg:flex">
        <a href="{{ route('dashboard') }}" class="grid size-10 place-items-center rounded-xl bg-[#e8793e] text-sm font-bold">HC</a>
        <nav class="mt-7 flex flex-1 flex-col gap-3 text-center text-[10px]">
            @foreach ([['事業','◫','dashboard'],['営業','◎','sales-home'],['面談','◇','interviews'],['総務','☎','phone-log'],['会計','¥','payments'],['設定','⚙','accounts']] as [$label,$icon,$target])
                <a href="{{ $target === 'dashboard' ? route('dashboard') : route('screens.show', $target) }}" class="rail-link {{ request()->routeIs('dashboard') && $target === 'dashboard' ? 'active' : '' }}"><span>{{ $icon }}</span>{{ $label }}</a>
            @endforeach
        </nav>
    </aside>
    <aside class="hidden w-[238px] shrink-0 border-r border-[#e5e8ec] bg-white lg:flex lg:flex-col">
        <div class="border-b border-[#edf0f3] px-5 py-6">
            <p class="text-xs text-[#737b88]">ハイクラス業務管理システム</p>
            <h2 class="mt-1 font-semibold">事業（イベント運営）</h2>
        </div>
        <nav class="flex flex-1 flex-col gap-1 p-3 text-sm">
            @foreach ([['ホーム','dashboard'],['イベント','events'],['アサイン','assignments'],['人数不足一覧','shortages'],['報告書','reports'],['スタッフ','staff'],['依頼先','clients']] as [$label,$target])
                <a href="{{ $target === 'dashboard' ? route('dashboard') : route('screens.show', $target) }}" class="menu-link">{{ $label }} @if($target === 'clients')<span>共通</span>@endif</a>
            @endforeach
        </nav>
        <div class="border-t border-[#edf0f3] p-4">
            <a href="{{ route('screens.show', 'profile') }}" class="block rounded-xl bg-[#f6f7f9] p-3 text-sm"><strong>{{ auth()->user()->name }}</strong><br><span class="text-xs text-[#69717e]">自分のページを開く ›</span></a>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">@csrf<button class="w-full text-left text-xs text-[#8a4d30]">ログアウト</button></form>
        </div>
    </aside>
    <div class="min-w-0 flex-1 pb-20 lg:pb-0">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-[#e8ebee] bg-white px-4 lg:hidden">
            <a href="{{ route('dashboard') }}" class="font-semibold">ハイクラス業務管理</a>
            <span class="rounded-full bg-[#f3eee9] px-3 py-1 text-xs">{{ auth()->user()->name }}</span>
        </header>
        <main class="mx-auto w-full max-w-[1180px] p-4 sm:p-6 lg:p-10">@yield('content')</main>
    </div>
    <nav class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-4 border-t border-[#dde1e6] bg-white lg:hidden">
        @foreach ([['事業','dashboard'],['営業','sales-home'],['会計','payments'],['メニュー','accounts']] as [$label,$target])
            <a href="{{ $target === 'dashboard' ? route('dashboard') : route('screens.show', $target) }}" class="py-3 text-center text-xs font-medium text-[#596170]">{{ $label }}</a>
        @endforeach
    </nav>
</div>
</body>
</html>
