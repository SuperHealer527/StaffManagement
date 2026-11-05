<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ログイン</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--login" data-static-screen="Login">
    <div class="app-root">
      <div class="screen">
        <div class="layout-072">
          <div class="utility-006">
            <div class="text-075">HIGHCLASS</div>
            <h1 class="text-155">ハイクラス業務管理システム</h1>
          </div>
          <form method="POST" action="{{ route('login.store') }}" class="layout-158">
            @csrf
            <div class="layout-055"><label for="lg-mail" class="text-042">メールアドレス</label><input id="lg-mail" name="email" type="email" value="{{ old('email') }}" autocomplete="username" placeholder="例:hanako@example.com" required="" class="text-076"></div>
            <div class="layout-055"><label for="lg-pass" class="text-042">パスワード</label><input id="lg-pass" name="password" type="password" autocomplete="current-password" required="" class="text-076"></div>
            @if (session('status'))<p role="status" class="text-157">{{ session('status') }}</p>@endif
            @error('email')<p role="alert" class="text-157">{{ $message }}</p>@enderror
            <label class="control-048"><input name="remember" value="1" type="checkbox" checked="" class="space-009">ログインしたままにする</label><button type="submit" class="control-004">ログイン</button><a href="{{ route('password.request') }}" class="text-156">パスワードを忘れた方</a></form>
          <p class="text-157">アカウントは社員からの招待で作成されます。<br>依頼や出勤確認は、メール・LINEのリンクから<br>ログインせずに回答できます。</p></div>
      </div>
    </div>
    
    
  </body>
</html>
