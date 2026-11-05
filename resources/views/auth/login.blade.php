<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ログイン</title>
    @vite(['resources/css/app.css'])
</head>
<body class="m-0 bg-[#f5f6f8] text-[#1a1d23]">
<main class="mx-auto flex min-h-screen w-full max-w-[390px] flex-col justify-center px-6 py-10">
    <div class="text-center">
        <div class="text-[13px] font-bold tracking-[.08em] text-[#5b6270]">HIGHCLASS</div>
        <h1 class="mt-1.5 mb-0 text-2xl font-bold">ハイクラス業務管理システム</h1>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="mt-7 flex flex-col gap-4 rounded-[14px] border border-[#e3e6eb] bg-white px-5 py-6">
        @csrf
        <label class="grid gap-1.5 text-[13px] font-medium">メールアドレス
            <input class="h-12 rounded-[10px] border border-[#c9ced6] px-3 text-base outline-none focus:border-[#1f5bd8] focus:ring-2 focus:ring-[#1f5bd8]/15" type="email" name="email" value="{{ old('email', 'admin@staffmanagement.test') }}" autocomplete="username" placeholder="例: hanako@example.com" required autofocus>
        </label>
        <label class="grid gap-1.5 text-[13px] font-medium">パスワード
            <input class="h-12 rounded-[10px] border border-[#c9ced6] px-3 text-base outline-none focus:border-[#1f5bd8] focus:ring-2 focus:ring-[#1f5bd8]/15" type="password" name="password" value="password123" autocomplete="current-password" required>
        </label>
        <label class="flex min-h-10 cursor-pointer items-center gap-2 text-sm"><input class="size-5 accent-[#1f5bd8]" type="checkbox" name="remember" value="1" checked>ログインしたままにする</label>
        @error('email')<p class="m-0 rounded-lg bg-[#fbe3df] p-3 text-sm text-[#8a1f11]">{{ $message }}</p>@enderror
        <button class="h-[52px] rounded-[10px] border-0 bg-[#1f5bd8] text-base font-bold text-white hover:bg-[#16449f]">ログイン</button>
        <a href="#" class="py-2 text-center text-sm text-[#1f5bd8] no-underline hover:text-[#16449f]">パスワードを忘れた方</a>
    </form>

    <p class="mt-5 mb-0 text-center text-xs leading-7 text-[#5b6270]">アカウントは社員からの招待で作成されます。<br>依頼や出勤確認は、メール・LINEのリンクから<br>ログインせずに回答できます。</p>
</main>
</body>
</html>
