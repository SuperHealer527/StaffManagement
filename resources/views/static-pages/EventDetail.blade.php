<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>イベントの詳細</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--eventdetail" data-static-screen="EventDetail">
    <div class="app-root">
      <div class="screen">
        <div class="layout-056">
          <div class="layout-002">
            <nav aria-label="エリア切り替え" class="layout-003">
              <div aria-hidden="true" class="layout-004">HC</div>
              <a href="#" aria-current="page" class="layout-005"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="16" y1="3" x2="16" y2="7"></line></svg><span class="text-001">事業</span></a><a href="Clients.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 17 9 11 13 15 21 7"></polyline><polyline points="14 7 21 7 21 14"></polyline></svg><span class="text-001">営業</span></a><a href="PhoneLog.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><rect x="9" y="2" width="6" height="4" rx="1"></rect><line x1="9" y1="11" x2="15" y2="11"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg><span class="text-001">総務</span></a>
              <div class="layout-007"></div>
            </nav>
            <nav aria-label="メインメニュー" class="layout-008">
              <div class="space-001">
                <div class="text-002">ハイクラス業務管理システム</div>
                <div class="text-003">事業(イベント運営)</div>
              </div>
              <a href="SubMain.dc.html" class="layout-010"><span>ホーム</span></a><a href="EventList.dc.html" aria-current="page" class="layout-009"><span>イベント</span></a><a href="Assign.dc.html" class="layout-010"><span>アサイン</span></a><a href="Shortage.dc.html" class="layout-010"><span>人数不足一覧</span></a><a href="ReportView.dc.html" class="layout-010"><span>報告書</span></a><a href="StaffList.dc.html" class="layout-010"><span>スタッフ</span></a><a href="Clients.dc.html" class="layout-010"><span>依頼先</span><span class="surface-001">共通</span></a><a href="Staff.dc.html" aria-label="鈴木 一郎(準社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>鈴木 一郎(準社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              <nav aria-label="パンくずリスト" class="text-007"><a href="EventList.dc.html">イベント</a> / 秋季健康フェア(10/21)</nav>
              <div class="layout-045">
                <div>
                  <div class="layout-042">
                    <h1 class="text-044">秋季健康フェア</h1>
                    <span class="surface-018">募集中</span><span class="surface-020">開催まであと2日</span></div>
                  <div class="text-124">2026/10/21(水) ・ サンプル商事 / 地域イベント運営</div>
                </div>
                <a href="Assign.dc.html" class="surface-021">アサインを開く</a></div>
              <div role="note" class="surface-064">閲覧用の画面です。イベントの編集は社員だけができます。謝金・交通費などの金額は表示しません。</div>
              <div class="layout-120">
                <section aria-labelledby="ev-basic" class="surface-022">
                  <h2 id="ev-basic" class="text-020">イベントの内容</h2>
                  <dl class="layout-121"><dt class="text-125">依頼先</dt><dd class="space-008">サンプル商事</dd><dt class="text-125">事業名</dt><dd class="space-008">地域イベント運営</dd><dt class="text-125">実施日</dt><dd class="space-008">2026/10/21(水)</dd><dt class="text-125">時間</dt><dd class="space-008">集合 8:30 ・ 開始 9:00 ・ 終了 17:00 ・ 解散 17:30<div class="text-002">役割と時間は全員同じ。人によって違うことは「メンバー」の備考に出ます</div>
                  </dd><dt class="text-125">集合場所</dt><dd class="space-008">サンプル公民館 1階ロビー</dd><dt class="text-125">実施場所</dt><dd class="space-008">サンプル公民館 2階 大ホール</dd><dt class="text-125">イベント詳細</dt><dd class="space-008">市民向けの健康イベント。ハイクラスは5人(リーダー1人・受付4人)で、来場者の受付・検温・会場案内を担当します。</dd><dt class="text-125">実施内容</dt><dd class="space-008">体力測定4種目＋骨密度測定</dd><dt class="text-125">持ち物</dt><dd class="layout-122"><span class="text-126">昼食</span><span class="text-126">室内履き</span><span class="text-126">ハイクラスTシャツ</span><span class="text-126">その他:筆記用具</span></dd><dt class="text-125">備考</dt><dd class="space-008">駐車場はありません。公共交通機関でお越しください。</dd></dl></section>
                <div class="layout-123">
                  <section aria-labelledby="ev-member" class="surface-022">
                    <div class="layout-021">
                      <h2 id="ev-member" class="text-020">メンバー</h2>
                      <div class="layout-054"><span class="text-007">確定 <b class="text-127"><span>2</span></b> / <span>5</span>人</span><span class="surface-011"><span>あと3人</span></span></div>
                    </div>
                    <ul class="layout-124">
                      <li class="surface-065">
                        <div class="layout-125"><span class="text-024"><span>鈴木 一郎</span></span><span class="text-002"><span>準社員</span></span><span class="surface-066">当日責任者</span><span class="surface-067">代表者</span></div>
                        <div class="text-059">備考:<span>リーダーをお願いします。集合は8:00(30分早め)です。</span></div>
                      </li>
                      <li class="surface-065">
                        <div class="layout-125"><span class="text-024"><span>田中 健</span></span><span class="text-002"><span>スタッフ</span></span></div>
                      </li>
                    </ul>
                    <div class="text-039"><span>回答待ちなど 3人(承諾 1・未定 1・依頼中 1)</span></div>
                    <div class="text-128">「確定」の人だけを表示しています。依頼中・未定・承諾の人や出勤確認の状況は、アサイン画面で確かめられます。</div>
                  </section>
                  <section aria-labelledby="ev-contact" class="surface-022">
                    <h2 id="ev-contact" class="text-020">緊急連絡先・報告書</h2>
                    <dl class="layout-121"><dt class="text-125">緊急連絡先</dt><dd class="space-008">
                    <div>ハイクラスLINE(公式LINEのトーク)</div>
                    <div>社員:山田 太郎 090-XXXX-XXXX</div>
                    <div>当日責任者:鈴木 一郎 080-XXXX-XXXX</div>
                    </dd><dt class="text-125">報告書</dt><dd class="space-008">必要(設問3問)<div class="text-002">代表者:鈴木 一郎(解散時間に、提出のお願いが届きます)</div>
                    </dd></dl></section>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
