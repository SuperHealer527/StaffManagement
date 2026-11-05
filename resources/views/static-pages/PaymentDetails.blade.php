<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>振込先と住所の入力</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--paymentdetails" data-static-screen="PaymentDetails">
    <div class="app-root">
      <div class="screen">
        <div class="layout-166">
          <header class="cell-025">
            <div class="text-002">ハイクラス</div>
            <h1 class="text-079">振込先と住所の入力</h1>
          </header>
          <main class="layout-074">
            <form method="POST" action="{{ isset($actionToken) ? route('public-actions.update', request()->route('token')) : '#' }}" class="layout-053">
              @csrf
              <div class="text-172">鈴木 次郎 さん、お仕事の確定ありがとうございます。謝金のお振込みと書類の送付のため、次の内容を入力してください。</div>
              <section class="layout-058">
                <h2 class="text-014">住所</h2>
                <label class="layout-044">郵便番号<input name="postal_code" type="text" inputmode="numeric" placeholder="例:1500001" required class="surface-034"></label><label class="layout-044">住所<input name="address" type="text" placeholder="郵便番号から自動で入ります" required class="surface-034"></label><label class="layout-044">ビル名・部屋番号<input name="building" type="text" class="surface-034"></label></section>
              <section class="layout-058">
                <h2 class="text-014">振込先口座</h2>
                <label class="layout-044">銀行名<input name="bank_name" type="text" placeholder="銀行名で探す" required class="surface-034"></label><label class="layout-044">銀行番号<input name="bank_code" type="text" inputmode="numeric" maxlength="4" class="surface-034"></label><label class="layout-044">支店名<input name="branch_name" type="text" placeholder="支店名で探す" required class="surface-034"></label><label class="layout-044">支店番号<input name="branch_code" type="text" inputmode="numeric" maxlength="3" class="surface-034"></label><label class="layout-044">種別<select name="account_type" class="surface-034"><option value="ordinary">普通</option><option value="current">当座</option></select></label><label class="layout-044">口座番号<input name="account_number" type="text" inputmode="numeric" required class="surface-034"></label><label class="layout-044">口座名義(カナ)<input name="account_name" type="text" placeholder="例:スズキ ジロウ" required class="surface-034"></label></section>
              <button type="submit" class="control-013">送信する</button>
              <div class="text-080">送信すると、このページでは見返せなくなります。リンクは本人専用で1回だけ使え、10/30まで有効です。間違えたときは担当にご連絡ください。</div>
            </form>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
