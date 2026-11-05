<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>パスワード再設定</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--login">
  <div class="app-root"><div class="screen"><div class="layout-072">
    <div class="utility-006"><div class="text-075">HIGHCLASS</div><h1 class="text-155">パスワード再設定</h1></div>
    <form method="POST" action="{{ route('password.email') }}" class="layout-158">
      @csrf
      <p class="text-157">登録済みのメールアドレスへ、1時間有効な再設定リンクを送信します。</p>
      <div class="layout-055"><label for="reset-email" class="text-042">メールアドレス</label><input id="reset-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="text-076"></div>
      @if (session('status'))<p role="status" class="text-157">{{ session('status') }}</p>@endif
      @error('email')<p role="alert" class="text-157">{{ $message }}</p>@enderror
      <button type="submit" class="control-004">再設定メールを送信</button>
      <a href="{{ route('login') }}" class="text-156">ログイン画面へ戻る</a>
    </form>
  </div></div></div>
</body>
</html>
