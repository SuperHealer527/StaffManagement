<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>依頼先一覧</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--clients" data-static-screen="Clients">
    <div class="app-root">
      <div class="screen">
        <div class="layout-068">
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
              <a href="SalesHome.dc.html" class="layout-010"><span>営業ホーム</span></a><a href="#" aria-current="page" class="layout-009"><span>依頼先(顧客リスト)</span><span class="surface-001">共通</span></a><a href="SalesBoard.dc.html" class="layout-010"><span>提案状況</span></a><a href="Meetings.dc.html" class="layout-010"><span>打合せ履歴</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              <div class="layout-013">
                <div>
                  <h1 class="text-044">依頼先</h1>
                  <div class="text-060">派遣の依頼先・営業先の一覧です。詳細(提案状況・打合せ履歴など)は社員だけが開けます。</div>
                </div>
                <a href="ClientEdit.dc.html" class="surface-021">+ 依頼先を登録</a></div>
              <section aria-labelledby="cl-list" class="surface-006">
                <div role="group" aria-label="種別で絞り込み" class="layout-069"><button type="button" aria-pressed="true" class="control-006"><span>すべて</span></button><button type="button" aria-pressed="false" class="control-007"><span>法人</span></button><button type="button" aria-pressed="false" class="control-007"><span>行政</span></button><button type="button" aria-pressed="false" class="control-007"><span>個人</span></button><button type="button" aria-pressed="false" class="control-007"><span>団体</span></button><button type="button" aria-pressed="false" class="control-007"><span>その他</span></button></div>
                <div class="layout-041">
                  <h2 id="cl-list" class="text-020">依頼先一覧(<span>5</span>件)</h2>
                  <div class="layout-070"><input type="text" aria-label="依頼先名・担当者名で探す" placeholder="依頼先名・担当者名で探す" class="text-119"><label class="layout-107">提案状況<span class="surface-056">社員のみ</span><select aria-label="提案状況で絞り込み" class="surface-017"><option>すべて</option><option>未接触</option><option>アプローチ中</option><option>提案中</option><option>受注</option><option>失注</option><option>保留</option></select></label><select aria-label="並び順" class="surface-017"><option>最終更新日が新しい順</option><option>依頼先名順</option></select></div>
                </div>
                <div class="utility-001">
                  <table class="text-069">
                    <thead>
                      <tr class="text-023"><th class="cell-042">種別</th><th class="cell-002">依頼先名</th><th class="cell-002">電話番号</th><th class="cell-002">担当者</th><th class="cell-002">携帯電話</th><th class="cell-002">最終更新日</th><th class="cell-043"><span class="space-004">操作</span></th></tr>
                    </thead>
                    <tbody>
                      <tr><td class="cell-044"><span class="surface-028"><span>法人</span></span></td><td class="cell-018"><span class="text-067"><span>サンプル商事 株式会社</span></span>
                        <div class="text-018"><span>地域連携部</span></div>
                        </td><td class="cell-045"><span>03-XXXX-XXXX</span></td><td class="cell-018"><span>青木 様</span></td><td class="cell-045"><span>090-XXXX-XXXX</span></td><td class="cell-046"><span>2026/10/15</span></td><td class="cell-019"><a href="ClientEdit.dc.html" class="text-070">詳細を見る</a></td></tr>
                      <tr><td class="cell-044"><span class="surface-028"><span>団体</span></span></td><td class="cell-018"><span class="text-067"><span>サンプル町会</span></span>
                        <div class="text-018"><span></span></div>
                        </td><td class="cell-045"><span>03-XXXX-XXXX</span></td><td class="cell-018"><span>大野 様</span></td><td class="cell-045"><span>080-XXXX-XXXX</span></td><td class="cell-046"><span>2026/10/12</span></td><td class="cell-019"><a href="ClientEdit.dc.html" class="text-070">詳細を見る</a></td></tr>
                      <tr><td class="cell-044"><span class="surface-028"><span>行政</span></span></td><td class="cell-018"><span class="text-067"><span>サンプル市</span></span>
                        <div class="text-018"><span>環境課</span></div>
                        </td><td class="cell-045"><span>0XX-XXX-XXXX</span></td><td class="cell-018"><span>川村 様</span></td><td class="cell-045"><span>—</span></td><td class="cell-046"><span>2026/09/30</span></td><td class="cell-019"><a href="ClientEdit.dc.html" class="text-070">詳細を見る</a></td></tr>
                      <tr><td class="cell-044"><span class="surface-028"><span>団体</span></span></td><td class="cell-018"><span class="text-067"><span>サンプル商店会</span></span>
                        <div class="text-018"><span></span></div>
                        </td><td class="cell-045"><span>03-XXXX-XXXX</span></td><td class="cell-018"><span>佐野 様</span></td><td class="cell-045"><span>090-XXXX-XXXX</span></td><td class="cell-046"><span>2026/09/02</span></td><td class="cell-019"><a href="ClientEdit.dc.html" class="text-070">詳細を見る</a></td></tr>
                      <tr><td class="cell-044"><span class="surface-028"><span>個人</span></span></td><td class="cell-018"><span class="text-067"><span>田中 一郎</span></span>
                        <div class="text-018"><span></span></div>
                        </td><td class="cell-045"><span>—</span></td><td class="cell-018"><span>田中 様</span></td><td class="cell-045"><span>090-XXXX-XXXX</span></td><td class="cell-046"><span>2026/08/18</span></td><td class="cell-019"><a href="ClientEdit.dc.html" class="text-070">詳細を見る</a></td></tr>
                    </tbody>
                  </table>
                </div>
                <div class="text-058">「詳細を見る」と提案状況の絞り込みは社員の画面にだけ表示されます。準社員は一覧の閲覧のみ、スタッフはこの画面を開けません。営業リストは、提案状況を「未接触」で絞り込んで使います。</div>
              </section>
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
