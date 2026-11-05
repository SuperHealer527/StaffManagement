<!DOCTYPE html>
<html lang="ja">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>ログイン</title>@vite(['resources/css/app.css'])</head>
<body class="grid min-h-screen place-items-center bg-[#f4f2ed] px-5 text-[#25282e]">
<main class="w-full max-w-[390px] rounded-3xl bg-white px-7 py-10 shadow-[0_14px_50px_rgba(34,38,45,.10)]">
    <div class="mx-auto grid size-14 place-items-center rounded-2xl bg-[#242b34] font-bold text-white">HC</div>
    <p class="mt-5 text-center text-xs font-semibold tracking-[.16em] text-[#a35b34]">HIGHCLASS</p>
    <h1 class="mt-2 text-center text-xl font-semibold">ハイクラス業務管理システム</h1>
    <form method="POST" action="{{ route('login.store') }}" class="mt-8 grid gap-5">@csrf
        <label class="form-label">メールアドレス<input class="form-input" type="email" name="email" value="{{ old('email', 'admin@staffmanagement.test') }}" required autofocus></label>
        <label class="form-label">パスワード<input class="form-input" type="password" name="password" value="password123" required></label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1">ログインしたままにする</label>
        @error('email')<p class="rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $message }}</p>@enderror
        <button class="primary-button w-full">ログイン</button>
    </form>
    <p class="mt-6 text-center text-sm text-[#707784]">パスワードを忘れた方</p>
    <div class="mt-7 rounded-xl bg-[#f8f6f3] p-4 text-xs leading-6 text-[#69717d]">デモ: admin@staffmanagement.test<br>パスワード: password123</div>
</main>
</body></html>
