<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>報告書の確認</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--reportview" data-static-screen="ReportView">
    <div class="app-root">
      <div class="screen">
        <div class="layout-065">
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
              <a href="Main.dc.html" class="layout-010"><span>ホーム</span></a><a href="EventList.dc.html" class="layout-010"><span>イベント</span></a><a href="Assign.dc.html" class="layout-010"><span>アサイン</span></a><a href="Shortage.dc.html" class="layout-010"><span>人数不足一覧</span></a><a href="#" aria-current="page" class="layout-009"><span>報告書</span></a><a href="StaffList.dc.html" class="layout-010"><span>スタッフ</span></a><a href="Clients.dc.html" class="layout-010"><span>依頼先</span><span class="surface-001">共通</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              <div class="layout-013">
                <div>
                  <h1 class="text-044">報告書</h1>
                  <div class="text-060">報告書が必要なイベントだけを表示しています</div>
                </div>
                <div class="layout-046">
                  <div class="layout-051"><label for="rv-status" class="text-002">提出状況</label><select id="rv-status" class="surface-017"><option>すべて</option><option>未提出</option><option>提出済み</option></select></div>
                  <div class="layout-051"><label for="rv-q1" class="text-002">終了の状況</label><select id="rv-q1" class="surface-017"><option>すべて</option><option>報告事項あり・トラブル等あり</option></select></div>
                </div>
              </div>
              <div class="layout-066">
                <section aria-labelledby="rv-list" class="surface-098">
                  <h2 id="rv-list" class="text-202">イベント一覧</h2>
                  <div class="layout-194"><button type="button" class="control-065">
                    <div class="layout-067"><span class="text-002"><span>10/18(日)</span></span><span class="surface-099"><span>未提出</span></span></div>
                    <div class="text-203"><span>秋祭り</span></div>
                    <div class="text-018">代表者:<span>佐藤 花子</span><span></span></div>
                    </button><button type="button" class="control-066">
                    <div class="layout-067"><span class="text-002"><span>10/17(土)</span></span><span class="surface-100"><span>提出済み</span></span></div>
                    <div class="text-203"><span>防災フェア</span></div>
                    <div class="text-018">代表者:<span>鈴木 一郎</span><span> ・ 報告事項あり</span></div>
                    </button><button type="button" class="control-065">
                    <div class="layout-067"><span class="text-002"><span>10/12(月)</span></span><span class="surface-100"><span>提出済み</span></span></div>
                    <div class="text-203"><span>地域清掃イベント</span></div>
                    <div class="text-018">代表者:<span>田中 健</span><span></span></div>
                    </button><button type="button" class="control-065">
                    <div class="layout-067"><span class="text-002"><span>10/10(土)</span></span><span class="surface-100"><span>提出済み</span></span></div>
                    <div class="text-203"><span>商店街イベント</span></div>
                    <div class="text-018">代表者:<span>中村 翔</span><span> ・ トラブル等あり</span></div>
                    </button></div>
                </section>
                <section aria-labelledby="rv-detail" class="surface-101">
                  <div class="text-002"><span>10/17(土)</span> ・ <span>サンプル市 / 防災啓発事業</span></div>
                  <h2 id="rv-detail" class="text-204"><span>防災フェア</span></h2>
                  <div class="text-046">代表者:<span>鈴木 一郎</span> ・ <span>10/17 19:42 提出</span></div>
                  <div class="layout-195">
                    <div class="space-022">
                      <div class="text-002">設問1 本日の事業は、無事終了しましたか?</div>
                      <div class="utility-020"><span class="surface-102"><span>問題なく終了したが、報告事項あり</span></span></div>
                    </div>
                    <div class="space-022">
                      <div class="layout-067">
                        <div class="text-002">設問2 依頼先への報告用の文章</div>
                        <button type="button" class="control-067">コピー</button></div>
                      <p class="text-205"><span>本日は防災フェアの受付と会場整理を担当しました。 来場者が想定より多く、14時ごろ受付に列ができましたが、15分ほどで解消しています。</span></p></div>
                    <div class="space-022">
                      <div class="layout-067">
                        <div class="text-002">設問3 ハイクラスへの報告用文章</div>
                        <button type="button" class="control-067">コピー</button></div>
                      <p class="text-205"><span>大きなトラブルはありませんでした。 受付の人数が足りない時間帯があったため、次回は受付を1名増やすことをおすすめします。</span></p></div>
                  </div>
                </section>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
