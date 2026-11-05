<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>営業ホーム</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--saleshome" data-static-screen="SalesHome">
    <div class="app-root">
      <div class="screen">
        <div class="layout-056">
          <div class="layout-002">
            <nav aria-label="エリア切り替え" class="layout-003">
              <div aria-hidden="true" class="layout-004">HC</div>
              <a href="Main.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="16" y1="3" x2="16" y2="7"></line></svg><span class="text-001">事業</span></a><a href="#" aria-current="page" class="layout-005"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 17 9 11 13 15 21 7"></polyline><polyline points="14 7 21 7 21 14"></polyline></svg><span class="text-001">営業</span></a><a href="Coaching.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H9l-4 3v-3a2 2 0 0 1-2-2z"></path><path d="M19 9a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2v3l-4-3h-3"></path></svg><span class="text-001">面談</span></a><a href="PhoneLog.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><rect x="9" y="2" width="6" height="4" rx="1"></rect><line x1="9" y1="11" x2="15" y2="11"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg><span class="text-001">総務</span></a><a href="PayManage.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4l5 7 5-7"></path><line x1="12" y1="11" x2="12" y2="20"></line><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg><span class="text-001">会計</span></a>
              <div class="layout-007"><a href="Accounts.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"></line><circle cx="9" cy="7" r="2.2" fill="#16213A"></circle><line x1="4" y1="17" x2="20" y2="17"></line><circle cx="15" cy="17" r="2.2" fill="#16213A"></circle></svg><span class="text-001">設定</span></a></div>
            </nav>
            <nav aria-label="メインメニュー" class="layout-008">
              <div class="space-001">
                <div class="text-002">ハイクラス業務管理システム</div>
                <div class="text-003">営業</div>
              </div>
              <a href="#" aria-current="page" class="layout-009"><span>営業ホーム</span></a><a href="Clients.dc.html" class="layout-010"><span>依頼先(顧客リスト)</span><span class="surface-001">共通</span></a><a href="SalesBoard.dc.html" class="layout-010"><span>提案状況</span></a><a href="Meetings.dc.html" class="layout-010"><span>打合せ履歴</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              <div class="layout-013">
                <div>
                  <h1 class="text-044">営業ホーム</h1>
                  <div class="text-060">これから仕事を取りに行く依頼先の動きをまとめて見ます。社員だけが開けます。</div>
                </div>
                <a href="ClientEdit.dc.html" class="surface-021">+ 依頼先を登録</a></div>
              <section aria-label="営業の数字" class="layout-039">
                <div class="surface-002">
                  <div class="text-007"><span>提案中の依頼先</span></div>
                  <div class="text-009"><span>4</span><span class="text-010"><span>社</span></span></div>
                  <div class="text-018"><span>アプローチ中は 6社</span></div>
                </div>
                <div class="surface-002">
                  <div class="text-007"><span>今週の次回アクション</span></div>
                  <div class="text-009"><span>3</span><span class="text-010"><span>件</span></span></div>
                  <div class="text-018"><span>期限切れ 1件</span></div>
                </div>
                <div class="surface-002">
                  <div class="text-007"><span>今年の受注</span></div>
                  <div class="text-009"><span>2</span><span class="text-010"><span>社</span></span></div>
                  <div class="text-018"><span>失注 1社</span></div>
                </div>
              </section>
              <section aria-labelledby="sh-stage" class="surface-006">
                <div class="layout-021">
                  <h2 id="sh-stage" class="text-020">提案状況ごとの件数</h2>
                  <a href="SalesBoard.dc.html" class="text-021">提案状況ボードを開く</a></div>
                <div class="layout-205"><a href="SalesBoard.dc.html" class="surface-112"><span>未接触</span><span class="text-214"><span>15</span></span></a><a href="SalesBoard.dc.html" class="surface-113"><span>アプローチ中</span><span class="text-214"><span>6</span></span></a><a href="SalesBoard.dc.html" class="surface-114"><span>提案中</span><span class="text-214"><span>4</span></span></a><a href="SalesBoard.dc.html" class="surface-115"><span>受注</span><span class="text-214"><span>3</span></span></a><a href="SalesBoard.dc.html" class="surface-116"><span>失注</span><span class="text-214"><span>1</span></span></a><a href="SalesBoard.dc.html" class="surface-117"><span>保留</span><span class="text-214"><span>2</span></span></a></div>
              </section>
              <div class="layout-066">
                <section aria-labelledby="sh-next" class="surface-118">
                  <div class="layout-021">
                    <h2 id="sh-next" class="text-020">次回アクションが近い依頼先</h2>
                    <span class="text-002">期限切れ・14日以内</span></div>
                  <div class="utility-001">
                    <table class="text-077">
                      <thead>
                        <tr class="text-023"><th class="cell-001">次回アクション日</th><th class="cell-002">依頼先 / 提案内容</th><th class="cell-002">提案状況</th><th class="cell-002"><span class="space-004">操作</span></th></tr>
                      </thead>
                      <tbody>
                        <tr><td class="cell-078"><span>10/22(木)</span>
                          <div class="text-066"><span>1日過ぎ</span></div>
                          </td><td class="cell-004">
                          <div class="text-067"><span>サンプル不動産</span></div>
                          <div class="text-018"><span>モデルルーム 来場受付</span></div>
                          </td><td class="cell-005"><span class="surface-018"><span>アプローチ中</span></span></td><td class="cell-006"><a href="ClientEdit.dc.html" class="text-070">詳細を見る</a></td></tr>
                        <tr><td class="cell-079"><span>10/28(水)</span>
                          <div class="text-066"><span>あと5日</span></div>
                          </td><td class="cell-004">
                          <div class="text-067"><span>サンプル商事</span></div>
                          <div class="text-018"><span>体力測定会(年4回)の運営</span></div>
                          </td><td class="cell-005"><span class="surface-033"><span>提案中</span></span></td><td class="cell-006"><a href="ClientEdit.dc.html" class="text-070">詳細を見る</a></td></tr>
                        <tr><td class="cell-079"><span>10/28(水)</span>
                          <div class="text-066"><span>あと5日</span></div>
                          </td><td class="cell-004">
                          <div class="text-067"><span>サンプル観光協会</span></div>
                          <div class="text-018"><span>冬のイルミネーション 誘導スタッフ</span></div>
                          </td><td class="cell-005"><span class="surface-033"><span>提案中</span></span></td><td class="cell-006"><a href="ClientEdit.dc.html" class="text-070">詳細を見る</a></td></tr>
                        <tr><td class="cell-079"><span>11/2(月)</span>
                          <div class="text-066"><span>あと10日</span></div>
                          </td><td class="cell-004">
                          <div class="text-067"><span>サンプル大学</span></div>
                          <div class="text-018"><span>オープンキャンパス 受付</span></div>
                          </td><td class="cell-005"><span class="surface-033"><span>提案中</span></span></td><td class="cell-006"><a href="ClientEdit.dc.html" class="text-070">詳細を見る</a></td></tr>
                        <tr><td class="cell-079"><span>11/5(木)</span>
                          <div class="text-066"><span>あと13日</span></div>
                          </td><td class="cell-004">
                          <div class="text-067"><span>サンプル商店会</span></div>
                          <div class="text-018"><span>年末大売り出し 呼び込み</span></div>
                          </td><td class="cell-005"><span class="surface-119"><span>保留</span></span></td><td class="cell-006"><a href="ClientEdit.dc.html" class="text-070">詳細を見る</a></td></tr>
                      </tbody>
                    </table>
                  </div>
                </section>
                <section aria-labelledby="sh-meet" class="surface-120">
                  <div class="layout-021">
                    <h2 id="sh-meet" class="text-020">最近の打合せ</h2>
                    <a href="Meetings.dc.html" class="text-021">すべて見る</a></div>
                  <div class="layout-035">
                    <div class="cell-017">
                      <div class="text-002"><span>10/23(金)</span> ・ <span>営業</span> ・ <span>訪問</span></div>
                      <div class="text-068"><span>サンプル観光協会</span></div>
                      <div class="text-038"><span>見積の説明。人数と時間帯の確認</span></div>
                    </div>
                    <div class="cell-017">
                      <div class="text-002"><span>10/20(火)</span> ・ <span>相談</span> ・ <span>オンライン</span></div>
                      <div class="text-068"><span>サンプル大学</span></div>
                      <div class="text-038"><span>当日の動線と集合場所の相談</span></div>
                    </div>
                    <div class="cell-017">
                      <div class="text-002"><span>10/16(金)</span> ・ <span>営業</span> ・ <span>メール</span></div>
                      <div class="text-068"><span>サンプル不動産</span></div>
                      <div class="text-038"><span>会社案内と料金表を送付</span></div>
                    </div>
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
