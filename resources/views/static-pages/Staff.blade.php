<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>スタッフのホーム</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--staff" data-static-screen="Staff">
    <div class="app-root">
      <div class="screen">
        <div class="layout-217">
          <header class="layout-030">
            <div class="text-035">ハイクラス業務管理システム</div>
            <div class="text-002">佐藤 花子(スタッフ)</div>
          </header>
          <main class="layout-031">
            <div>
              <h1 class="text-036">ホーム</h1>
              <div class="text-037">2026年10月19日(月)</div>
            </div>
            <section aria-labelledby="s-req" class="surface-131">
              <div class="layout-032">
                <h2 id="s-req" class="text-014">未回答の依頼</h2>
                <span class="surface-132">1件</span></div>
              <div class="text-224">秋季健康フェア</div>
              <div class="text-225">10/21(水) 集合 8:30 ・ 解散 17:30</div>
              <div class="text-038">集合場所:サンプル公民館 1階ロビー</div>
              <div class="text-038">謝金 9,000円 ・ 交通費 480円 ・ <a href="Respond.dc.html">詳しく見る</a></div>
              <div class="layout-218"><button type="button" class="surface-133">承諾</button><button type="button" class="surface-134">未定</button><button type="button" class="surface-134">辞退</button></div>
            </section>
            <section aria-labelledby="s-check" class="surface-010">
              <div class="layout-032">
                <h2 id="s-check" class="text-014">出勤確認</h2>
                <span class="surface-135">7日前確認</span></div>
              <div class="text-224">ハロウィンマルシェ(設営)</div>
              <div class="text-225">10/24(土) 集合 7:30 ・ サンプルモール 正面入口</div>
              <button type="button" class="surface-136">確認しました</button>
              <div class="text-039">LINE・メールの確認用リンクからも確認できます</div>
              <div class="cell-090">
                <div class="text-040">緊急連絡先</div>
                <ul class="layout-033">
                  <li class="layout-034"><span>ハイクラスLINE</span><span class="utility-002">公式LINEのトークから連絡</span></li>
                  <li class="layout-034"><span>社員:山田 太郎</span><a href="tel:" class="text-041">090-XXXX-XXXX</a></li>
                  <li class="layout-034"><span>当日責任者:田中 健</span><a href="tel:" class="text-041">090-XXXX-XXXX</a></li>
                </ul>
              </div>
            </section>
            <section aria-labelledby="s-report" class="surface-010">
              <div class="layout-032">
                <h2 id="s-report" class="text-014">報告書の提出</h2>
                <span class="surface-137">あなたが代表者です</span></div>
              <div class="text-224">秋祭り</div>
              <div class="text-225">10/18(日) 実施済み ・ 設問 3問(選択1・記述2)</div>
              <a href="Report.dc.html" class="layout-219">報告書を書く</a></section>
            <section aria-labelledby="s-plan" class="surface-010">
              <h2 id="s-plan" class="text-014">今後の予定</h2>
              <div class="layout-035">
                <div class="layout-220">
                  <div class="text-226"><span>10/24(土)</span></div>
                  <div class="space-003">
                    <div class="text-032"><span>ハロウィンマルシェ(設営)</span></div>
                    <div class="text-018"><span>集合 7:30</span> ・ <span>サンプルモール 正面入口</span></div>
                    <div class="text-018">持ち物:<span>昼食、ハイクラスTシャツ、軍手</span></div>
                  </div>
                </div>
                <div class="layout-220">
                  <div class="text-226"><span>11/3(火)</span></div>
                  <div class="space-003">
                    <div class="text-032"><span>文化祭(来場者誘導)</span></div>
                    <div class="text-018"><span>集合 9:00</span> ・ <span>サンプル市民会館</span></div>
                    <div class="text-018">持ち物:<span>昼食、室内履き</span></div>
                  </div>
                </div>
              </div>
            </section>
            <section aria-label="今年の勤務回数" class="layout-221">
              <div>
                <div class="text-007">今年の勤務回数</div>
                <div class="text-227">14<span class="text-029">回</span></div>
              </div>
              <a href="Payslip.dc.html" class="text-042">支払明細を見る</a></section>
          </main>
          <nav aria-label="メインメニュー" class="layout-036"><a href="#" aria-current="page" class="layout-037"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"></path><path d="M5 9.5V21h14V9.5"></path></svg>ホーム</a><a href="MyRequests.dc.html" class="layout-038"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>依頼</a><a href="Payslip.dc.html" class="layout-038"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"></path><path d="M9 8h6M9 12h6"></path></svg>支払明細</a><a href="Profile.dc.html" class="layout-038"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"></path></svg>マイプロフィール</a></nav>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
