<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>パスワードの設定</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--setpassword" data-static-screen="SetPassword">
    <div class="app-root">
      <div class="screen">
        <div class="layout-072">
          <div class="utility-006">
            <div class="text-075">HIGHCLASS</div>
            <h1 class="text-221">パスワードの設定</h1>
            <p class="text-222">佐藤 花子 さん、ようこそ。<br>ログインに使うパスワードを決めてください。</p></div>
          <form method="POST" action="{{ isset($actionToken) ? route('public-actions.update', request()->route('token')) : '#' }}" class="layout-212">
            @csrf
            <div class="layout-055"><span class="text-042">メールアドレス(ログインID)</span>
              <div class="layout-213">hanako@example.com</div>
            </div>
            <div class="layout-055"><label for="sp-pass" class="text-042">新しいパスワード</label><input id="sp-pass" name="password" type="password" autocomplete="new-password" required="" minlength="8" value="" class="text-076"></div>
            <ul class="layout-214">
              <li class="layout-215"><span aria-hidden="true" class="text-067"><span>・</span></span>8文字以上</li>
              <li class="layout-215"><span aria-hidden="true" class="text-067"><span>・</span></span>英字と数字を両方ふくむ</li>
            </ul>
            <div class="layout-055"><label for="sp-pass2" class="text-042">もう一度入力</label><input id="sp-pass2" name="password_confirmation" type="password" autocomplete="new-password" required="" minlength="8" class="text-076"></div>
            <button type="submit" class="control-004">設定してはじめる</button></form>
          <p class="text-223">設定が終わると、マイプロフィールが開きます。<br>ご自身の情報と対応可能時間の登録をお願いします。</p></div>
      </div>
    </div>
    
    
  </body>
</html>
