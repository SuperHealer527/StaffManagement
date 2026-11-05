<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>自分への依頼一覧</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--myrequests" data-static-screen="MyRequests">
    <div class="app-root">
      <div class="screen">
        <div class="layout-062">
          <header class="layout-030">
            <div class="text-035">ハイクラス業務管理システム</div>
            <div class="text-002">佐藤 花子(スタッフ)</div>
          </header>
          <main class="layout-161">
            <h1 class="text-036">依頼</h1>
            <div role="tablist" aria-label="依頼の種類" class="layout-162"><button type="button" role="tab" aria-selected="true" class="control-049"><span>回答待ち</span></button><button type="button" role="tab" aria-selected="false" class="control-050"><span>今後の予定</span></button><button type="button" role="tab" aria-selected="false" class="control-050"><span>過去</span></button></div>
            <div class="text-002"><span>「承諾/未定/辞退」をまだ決めていない依頼です。確定前なら回答を変えられます。</span></div>
            <a href="Respond.dc.html" class="surface-083">
            <div class="layout-067"><span class="text-040"><span>10/21(水)</span></span><span class="surface-084"><span>依頼中</span></span></div>
            <div class="text-161"><span>秋季健康フェア</span></div>
            <div class="text-033">集合 <span>8:30</span> ・ <span>サンプル公民館</span></div>
            <div class="layout-163"><span class="utility-014">謝金 <span>9,000円</span> ・ 交通費 <span>480円</span></span><span class="text-162"><span>回答する</span></span></div>
            </a><a href="Respond.dc.html" class="surface-083">
            <div class="layout-067"><span class="text-040"><span>11/4(水)</span></span><span class="surface-085"><span>未定</span></span></div>
            <div class="text-161"><span>秋季健康フェア</span></div>
            <div class="text-033">集合 <span>8:30</span> ・ <span>サンプル公民館</span></div>
            <div class="layout-163"><span class="utility-014">謝金 <span>9,000円</span> ・ 交通費 <span>480円</span></span><span class="text-162"><span>回答を変える</span></span></div>
            </a></main>
          <nav aria-label="メインメニュー" class="layout-036"><a href="Staff.dc.html" class="layout-038"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"></path><path d="M5 9.5V21h14V9.5"></path></svg>ホーム</a><a href="#" aria-current="page" class="layout-037"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>依頼</a><a href="Payslip.dc.html" class="layout-038"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"></path><path d="M9 8h6M9 12h6"></path></svg>支払明細</a><a href="Profile.dc.html" class="layout-038"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"></path></svg>マイプロフィール</a></nav>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
