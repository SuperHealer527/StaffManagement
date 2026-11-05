<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>人数不足一覧</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--shortage" data-static-screen="Shortage">
    <div class="app-root">
      <div class="screen">
        <div class="layout-068">
          <div class="layout-002">
            <nav aria-label="エリア切り替え" class="layout-003">
              <div aria-hidden="true" class="layout-004">HC</div>
              <a href="#" aria-current="page" class="layout-005"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="16" y1="3" x2="16" y2="7"></line></svg><span class="text-001">事業</span></a><a href="SalesHome.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 17 9 11 13 15 21 7"></polyline><polyline points="14 7 21 7 21 14"></polyline></svg><span class="text-001">営業</span></a><a href="Coaching.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H9l-4 3v-3a2 2 0 0 1-2-2z"></path><path d="M19 9a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2v3l-4-3h-3"></path></svg><span class="text-001">面談</span></a><a href="PhoneLog.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><rect x="9" y="2" width="6" height="4" rx="1"></rect><line x1="9" y1="11" x2="15" y2="11"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg><span class="text-001">総務</span></a><a href="PayManage.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4l5 7 5-7"></path><line x1="12" y1="11" x2="12" y2="20"></line><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg><span class="text-001">会計</span></a>
              <div class="layout-007"><a href="Accounts.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"></line><circle cx="9" cy="7" r="2.2" fill="#16213A"></circle><line x1="4" y1="17" x2="20" y2="17"></line><circle cx="15" cy="17" r="2.2" fill="#16213A"></circle></svg><span class="text-001">設定</span></a></div>
            </nav>
            <nav aria-label="メインメニュー" class="layout-008">
              <div class="space-001">
                <div class="text-002">ハイクラス業務管理システム</div>
                <div class="text-003">事業(イベント運営)</div>
              </div>
              <a href="Main.dc.html" class="layout-010"><span>ホーム</span></a><a href="EventList.dc.html" class="layout-010"><span>イベント</span></a><a href="Assign.dc.html" class="layout-010"><span>アサイン</span></a><a href="#" aria-current="page" class="layout-009"><span>人数不足一覧</span></a><a href="ReportView.dc.html" class="layout-010"><span>報告書</span></a><a href="StaffList.dc.html" class="layout-010"><span>スタッフ</span></a><a href="Clients.dc.html" class="layout-010"><span>依頼先</span><span class="surface-001">共通</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              <div>
                <h1 class="text-044">人数不足一覧</h1>
                <div class="text-060">「確定」の人数が必要人数に届いていないイベントを、開催が近い順に並べています(下書き・中止は除く)。</div>
              </div>
              <section aria-label="不足の合計" class="layout-216">
                <div class="surface-031">
                  <div class="text-011">人数不足のイベント</div>
                  <div class="text-064">4<span class="text-010">件</span></div>
                </div>
                <div class="surface-030">
                  <div class="text-007">不足している人数(合計)</div>
                  <div class="text-063">11<span class="text-010">人</span></div>
                </div>
                <div class="surface-030">
                  <div class="text-007">承諾済みで確定待ち</div>
                  <div class="text-063">3<span class="text-010">人</span></div>
                </div>
              </section>
              <section aria-labelledby="sh-list" class="surface-006">
                <h2 id="sh-list" class="text-020">不足しているイベント</h2>
                <div class="utility-001">
                  <table class="text-069">
                    <thead>
                      <tr class="text-023"><th class="cell-009">開催まで</th><th class="cell-024">実施日</th><th class="cell-002">イベント</th><th class="cell-002">確定 / 必要</th><th class="cell-002">不足</th><th class="cell-002">回答の状況</th><th class="cell-020">操作</th></tr>
                    </thead>
                    <tbody>
                      <tr><td class="cell-085"><span class="surface-011"><span>あと2日</span></span></td><td class="cell-086">
                        <div class="text-067"><span>10/21(水)</span></div>
                        <div class="text-018">集合 <span>8:30</span></div>
                        </td><td class="cell-018"><a href="EventEdit.dc.html" class="text-072"><span>秋季健康フェア</span></a>
                        <div class="text-018"><span>サンプル商事</span> / <span>地域イベント運営</span></div>
                        </td><td class="cell-087"><span>2 / 5人</span></td><td class="cell-088">
                        <div><span>あと3人</span></div>
                        </td><td class="cell-089"><span>承諾 1 ・ 未定 1 ・ 依頼中 1</span></td><td class="cell-019"><a href="Assign.dc.html" class="surface-130">アサインする</a></td></tr>
                      <tr><td class="cell-085"><span class="surface-013"><span>あと5日</span></span></td><td class="cell-086">
                        <div class="text-067"><span>10/24(土)</span></div>
                        <div class="text-018">集合 <span>7:30</span></div>
                        </td><td class="cell-018"><a href="EventEdit.dc.html" class="text-072"><span>ハロウィンマルシェ</span></a>
                        <div class="text-018"><span>サンプル開発</span> / <span>商業施設イベント</span></div>
                        </td><td class="cell-087"><span>4 / 6人</span></td><td class="cell-088">
                        <div><span>あと2人</span></div>
                        </td><td class="cell-089"><span>承諾 1 ・ 依頼中 3</span></td><td class="cell-019"><a href="Assign.dc.html" class="surface-130">アサインする</a></td></tr>
                      <tr><td class="cell-085"><span class="surface-014"><span>あと12日</span></span></td><td class="cell-086">
                        <div class="text-067"><span>10/31(土)</span></div>
                        <div class="text-018">集合 <span>6:30</span></div>
                        </td><td class="cell-018"><a href="EventEdit.dc.html" class="text-072"><span>市民マラソン</span></a>
                        <div class="text-018"><span>サンプル協会</span> / <span>スポーツ大会</span></div>
                        </td><td class="cell-087"><span>7 / 8人</span></td><td class="cell-088">
                        <div><span>あと1人</span></div>
                        </td><td class="cell-089"><span>承諾 1 ・ 依頼中 1</span></td><td class="cell-019"><a href="Assign.dc.html" class="surface-130">アサインする</a></td></tr>
                      <tr><td class="cell-085"><span class="surface-014"><span>あと16日</span></span></td><td class="cell-086">
                        <div class="text-067"><span>11/4(水)</span></div>
                        <div class="text-018">集合 <span>8:30</span></div>
                        </td><td class="cell-018"><a href="EventEdit.dc.html" class="text-072"><span>秋季健康フェア</span></a>
                        <div class="text-018"><span>サンプル商事</span> / <span>地域イベント運営</span></div>
                        </td><td class="cell-087"><span>0 / 5人</span></td><td class="cell-088">
                        <div><span>あと5人</span></div>
                        </td><td class="cell-089"><span>未定 1 ・ 依頼中 2</span></td><td class="cell-019"><a href="Assign.dc.html" class="surface-130">アサインする</a></td></tr>
                    </tbody>
                  </table>
                </div>
                <div class="text-058">「承諾」の人がいるイベントは、アサイン画面で「確定にする」を押すと不足が減ります。確定が必要人数に達すると、残った依頼中・未定・承諾の人は自動で「見送り」になり、イベントは自動で「確定」になってこの一覧から消えます。</div>
              </section>
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
