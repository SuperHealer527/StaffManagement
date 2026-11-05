<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>依頼への回答</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--respond" data-static-screen="Respond">
    <div class="app-root">
      <div class="screen">
        <div class="layout-196">
          <header class="layout-030">
            <div class="text-035">ハイクラス業務管理システム</div>
            <div class="text-002">佐藤 花子 さん</div>
          </header>
          <main class="layout-057">
            <div><span class="surface-103">お仕事の依頼</span>
              <h1 class="text-206">秋季健康フェア</h1>
              <div class="text-060">サンプル商事 / 地域イベント運営</div>
            </div>
            <section aria-label="イベントの内容" class="layout-058">
              <div class="surface-104">
                <div class="text-207">あなたへの備考</div>
                <div class="text-208">受付の担当です。</div>
              </div>
              <div class="cell-015">
                <div class="text-002">日時</div>
                <div class="text-209">10月21日(水)</div>
                <div class="layout-197">
                  <div>集合 <strong>8:30</strong></div>
                  <div>開始 <strong>9:00</strong></div>
                  <div>終了 <strong>17:00</strong></div>
                  <div>解散 <strong>17:30</strong></div>
                </div>
              </div>
              <div class="layout-198">
                <div>
                  <div class="text-002">謝金</div>
                  <div class="text-003">9,000円</div>
                </div>
                <div>
                  <div class="text-002">交通費</div>
                  <div class="text-003">480円</div>
                </div>
              </div>
              <div class="cell-015">
                <div class="text-002">集合場所</div>
                <div class="text-061">サンプル公民館 1階ロビー</div>
                <div class="text-058">実施場所</div>
                <div class="text-061">サンプル公民館 2階 大ホール</div>
              </div>
              <div class="cell-015">
                <div class="text-002">イベント詳細</div>
                <div class="text-208">市民向けの健康イベント。ハイクラスはリーダー1人と受付4人で、来場者の受付・検温・会場案内を担当します。</div>
              </div>
              <div class="cell-015">
                <div class="text-002">実施内容</div>
                <div class="text-208">体力測定4種目＋骨密度測定</div>
              </div>
              <div class="cell-015">
                <div class="text-002">備考</div>
                <div class="text-208">駐車場はありません。公共交通機関でお越しください。</div>
              </div>
              <div class="cell-015">
                <div class="text-002">持ち物</div>
                <div class="layout-059"><span class="text-045">昼食</span><span class="text-045">室内履き</span><span class="text-045">ハイクラスTシャツ</span><span class="text-045">筆記用具</span></div>
              </div>
            </section>
            <form method="POST" action="{{ isset($actionToken) ? route('public-actions.update', request()->route('token')) : '#' }}" aria-labelledby="ask" class="layout-199">
              @csrf
              <h2 id="ask" class="text-014">このお仕事を引き受けられますか?</h2>
              <button type="submit" name="response" value="accepted" class="control-004">承諾する</button><button type="submit" name="response" value="unknown" class="control-068">まだ分からない(未定)</button><button type="submit" name="response" value="declined" class="control-068">辞退する</button></form>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
