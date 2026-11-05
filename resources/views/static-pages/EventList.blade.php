<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>イベント一覧</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--eventlist" data-static-screen="EventList">
    <div class="app-root">
      <div class="screen">
        <div class="layout-061">
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
              <a href="Main.dc.html" class="layout-010"><span>ホーム</span></a><a href="#" aria-current="page" class="layout-009"><span>イベント</span></a><a href="Assign.dc.html" class="layout-010"><span>アサイン</span></a><a href="Shortage.dc.html" class="layout-010"><span>人数不足一覧</span></a><a href="ReportView.dc.html" class="layout-010"><span>報告書</span></a><a href="StaffList.dc.html" class="layout-010"><span>スタッフ</span></a><a href="Clients.dc.html" class="layout-010"><span>依頼先</span><span class="surface-001">共通</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              <div class="layout-013">
                <div>
                  <h1 class="text-044">イベント</h1>
                  <div class="text-060">日付の近い順に並んでいます。イベント名を押すと詳細(社員は編集画面、準社員は閲覧用の詳細)、「アサイン」でアサイン画面が開きます。</div>
                </div>
                <a href="EventEdit.dc.html" class="surface-021">+ イベントを登録</a></div>
              <section aria-label="絞り込み" class="layout-151">
                <div role="group" aria-label="期間" class="layout-152"><span class="text-147">期間</span><button type="button" aria-pressed="false" class="control-008"><span>今日</span></button><button type="button" aria-pressed="false" class="control-008"><span>今週</span></button><button type="button" aria-pressed="true" class="control-009"><span>今月</span></button><button type="button" aria-pressed="false" class="control-008"><span>来月</span></button><button type="button" aria-pressed="false" class="control-008"><span>日付を指定</span></button></div>
                <div class="layout-071">
                  <div class="layout-051"><label for="el-q" class="text-002">キーワード</label><input id="el-q" type="text" placeholder="イベント名・事業名で探す" class="text-071"></div>
                  <div class="layout-051"><label for="el-client" class="text-002">依頼先</label><select id="el-client" class="surface-017"><option>すべて</option><option>サンプル商事</option><option>サンプル町会</option><option>サンプル市</option><option>サンプル商店会</option></select></div>
                  <div class="layout-051"><label for="el-status" class="text-002">ステータス</label><select id="el-status" class="surface-017"><option>中止・実施済み以外</option><option>下書き</option><option>募集中</option><option>確定</option><option>実施済み</option><option>中止</option><option>すべて</option></select></div>
                  <label class="control-010"><input type="checkbox" class="space-006">人数不足のイベントだけ</label></div>
              </section>
              <section aria-labelledby="el-list" class="surface-006">
                <h2 id="el-list" class="text-020"><span>7</span>件</h2>
                <div class="utility-001">
                  <table class="text-148">
                    <thead>
                      <tr class="text-023"><th class="cell-009">ステータス</th><th class="cell-048">確定 / 必要</th><th class="cell-002">実施日・時間</th><th class="cell-002">イベント</th><th class="cell-002">備考</th><th class="cell-002">報告書</th><th class="cell-020">操作</th></tr>
                    </thead>
                    <tbody>
                      <tr><td class="cell-049"><span class="surface-077"><span>募集中</span></span></td><td class="cell-021">
                        <div class="text-067"><span>2 / 5人</span></div>
                        <div class="text-149"><span>あと3人</span></div>
                        </td><td class="cell-022">
                        <div class="text-015"><span>10/21(水)</span></div>
                        <div class="text-018"><span>集合 8:30 〜 解散 17:30</span></div>
                        </td><td class="cell-022"><a href="EventEdit.dc.html" class="text-072"><span>秋季健康フェア</span></a>
                        <div class="text-018"><span>サンプル商事</span> / <span>地域イベント運営</span></div>
                        </td><td class="cell-050"><span>駐車場なし。公共交通機関で来場</span></td><td class="cell-051"><span>必要</span></td><td class="cell-023"><a href="Assign.dc.html" class="text-073">アサイン</a></td></tr>
                      <tr><td class="cell-049"><span class="surface-077"><span>募集中</span></span></td><td class="cell-021">
                        <div class="text-067"><span>4 / 6人</span></div>
                        <div class="text-149"><span>あと2人</span></div>
                        </td><td class="cell-022">
                        <div class="text-015"><span>10/24(土)</span></div>
                        <div class="text-018"><span>集合 7:30 〜 解散 16:00</span></div>
                        </td><td class="cell-022"><a href="EventEdit.dc.html" class="text-072"><span>ハロウィンマルシェ</span></a>
                        <div class="text-018"><span>サンプル開発</span> / <span>商業施設イベント</span></div>
                        </td><td class="cell-050"><span>搬入口は建物裏手。軍手持参</span></td><td class="cell-051"><span>必要</span></td><td class="cell-023"><a href="Assign.dc.html" class="text-073">アサイン</a></td></tr>
                      <tr><td class="cell-049"><span class="surface-078"><span>確定</span></span></td><td class="cell-021">
                        <div class="text-067"><span>6 / 6人</span></div>
                        </td><td class="cell-022">
                        <div class="text-015"><span>10/25(日)</span></div>
                        <div class="text-018"><span>集合 9:00 〜 解散 15:00</span></div>
                        </td><td class="cell-022"><a href="EventEdit.dc.html" class="text-072"><span>体力測定会</span></a>
                        <div class="text-018"><span>サンプル市</span> / <span>健康づくり事業</span></div>
                        </td><td class="cell-050"><span>雨天時は屋内会場に変更</span></td><td class="cell-051"><span>必要</span></td><td class="cell-023"><a href="Assign.dc.html" class="text-073">アサイン</a></td></tr>
                      <tr><td class="cell-049"><span class="surface-077"><span>募集中</span></span></td><td class="cell-021">
                        <div class="text-067"><span>7 / 8人</span></div>
                        <div class="text-149"><span>あと1人</span></div>
                        </td><td class="cell-022">
                        <div class="text-015"><span>10/31(土)</span></div>
                        <div class="text-018"><span>集合 6:30 〜 解散 13:00</span></div>
                        </td><td class="cell-022"><a href="EventEdit.dc.html" class="text-072"><span>市民マラソン</span></a>
                        <div class="text-018"><span>サンプル協会</span> / <span>スポーツ大会</span></div>
                        </td><td class="cell-050"><span>早朝集合のため前日に再確認</span></td><td class="cell-051"><span>不要</span></td><td class="cell-023"><a href="Assign.dc.html" class="text-073">アサイン</a></td></tr>
                      <tr><td class="cell-049"><span class="surface-078"><span>確定</span></span></td><td class="cell-021">
                        <div class="text-067"><span>4 / 4人</span></div>
                        </td><td class="cell-022">
                        <div class="text-015"><span>11/3(火)</span></div>
                        <div class="text-018"><span>集合 9:00 〜 解散 17:00</span></div>
                        </td><td class="cell-022"><a href="EventEdit.dc.html" class="text-072"><span>文化祭</span></a>
                        <div class="text-018"><span>サンプル町会</span> / <span>文化祭</span></div>
                        </td><td class="cell-050"><span>—</span></td><td class="cell-051"><span>不要</span></td><td class="cell-023"><a href="Assign.dc.html" class="text-073">アサイン</a></td></tr>
                      <tr><td class="cell-049"><span class="surface-077"><span>募集中</span></span></td><td class="cell-021">
                        <div class="text-067"><span>0 / 5人</span></div>
                        <div class="text-149"><span>あと5人</span></div>
                        </td><td class="cell-022">
                        <div class="text-015"><span>11/4(水)</span></div>
                        <div class="text-018"><span>集合 8:30 〜 解散 17:30</span></div>
                        </td><td class="cell-022"><a href="EventEdit.dc.html" class="text-072"><span>秋季健康フェア</span></a>
                        <div class="text-018"><span>サンプル商事</span> / <span>地域イベント運営</span></div>
                        </td><td class="cell-050"><span>—</span></td><td class="cell-051"><span>必要</span></td><td class="cell-023"><a href="Assign.dc.html" class="text-073">アサイン</a></td></tr>
                      <tr><td class="cell-049"><span class="surface-079"><span>下書き</span></span></td><td class="cell-021">
                        <div class="text-067"><span>0 / 3人</span></div>
                        </td><td class="cell-022">
                        <div class="text-015"><span>11/8(日)</span></div>
                        <div class="text-018"><span>集合 9:00 〜 解散 15:00</span></div>
                        </td><td class="cell-022"><a href="EventEdit.dc.html" class="text-072"><span>子ども食堂まつり</span></a>
                        <div class="text-018"><span>サンプル町会</span> / <span>地域交流</span></div>
                        </td><td class="cell-050"><span>時間は依頼先と調整中(下書きのため依頼はまだ送れません)</span></td><td class="cell-051"><span>不要</span></td><td class="cell-023"><a href="Assign.dc.html" class="text-073">アサイン</a></td></tr>
                    </tbody>
                  </table>
                </div>
              </section>
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
