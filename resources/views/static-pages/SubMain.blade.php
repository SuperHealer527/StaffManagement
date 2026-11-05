<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>準社員のホーム</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--submain" data-static-screen="SubMain">
    <div class="app-root">
      <div class="screen">
        <div class="layout-001">
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
              <a href="#" aria-current="page" class="layout-009"><span>ホーム</span></a><a href="EventList.dc.html" class="layout-010"><span>イベント</span></a><a href="Assign.dc.html" class="layout-010"><span>アサイン</span></a><a href="Shortage.dc.html" class="layout-010"><span>人数不足一覧</span></a><a href="ReportView.dc.html" class="layout-010"><span>報告書</span></a><a href="StaffList.dc.html" class="layout-010"><span>スタッフ</span></a><a href="Clients.dc.html" class="layout-010"><span>依頼先</span><span class="surface-001">共通</span></a><a href="Staff.dc.html" aria-label="鈴木 一郎(準社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>鈴木 一郎(準社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-002">
            <div class="layout-012">
              <div class="layout-013">
                <div>
                  <h1 class="text-005">ホーム</h1>
                  <div class="text-006">2026年10月19日(月)</div>
                </div>
              </div>
              <section aria-label="主な数字" class="layout-039">
                <div class="surface-002">
                  <div class="text-007">2週間以内のイベント<span class="text-008">今日から14日</span></div>
                  <div class="text-009">6<span class="text-010">件</span></div>
                </div>
                <div class="surface-002">
                  <div class="text-007">4週間以内のイベント<span class="text-008">今日から28日</span></div>
                  <div class="text-009">21<span class="text-010">件</span></div>
                </div>
                <div class="surface-003">
                  <div class="text-011">人数不足のイベント<span class="text-012">2週間以内</span></div>
                  <div class="text-013">3<span class="text-010">件</span></div>
                </div>
              </section>
              <section aria-labelledby="h-news" class="layout-015">
                <div class="layout-016">
                  <h2 id="h-news" class="text-014">お知らせ</h2>
                  <span class="text-002">急ぐものから最大3件。対応すると消えます</span></div>
                <a href="PhoneLog.dc.html" class="layout-017"><span><b>折り返し待ちの電話が2件あります</b>(サンプル観光協会 森 様 ほか)</span><span class="text-015">電話記録簿を開く ›</span></a><a href="#h-pending" class="layout-018"><span><b>3日以上回答が来ていない依頼が2件あります</b></span><span class="text-015">一覧を見る ›</span></a></section>
              <div class="layout-019">
                <div class="layout-020">
                  <section aria-labelledby="h-checkin" class="surface-004">
                    <div class="layout-021">
                      <h2 id="h-checkin" class="text-016">当日確認が済んでいない人</h2>
                      <span class="text-002">集合2時間前の時点で未確認</span></div>
                    <div class="layout-022">
                      <div class="layout-023">
                        <div class="space-003">
                          <div class="text-017"><span>中村 翔</span></div>
                          <div class="text-018"><span>企業説明会(会場案内)</span> ・ <span>集合 9:30</span></div>
                        </div>
                        <div class="layout-024"><a href="#" class="surface-005">LINEする</a><a href="tel:" class="text-019"><span>電話する 090-XXXX-XXXX</span></a></div>
                      </div>
                      <div class="layout-023">
                        <div class="space-003">
                          <div class="text-017"><span>小林 さくら</span></div>
                          <div class="text-018"><span>企業説明会(会場案内)</span> ・ <span>集合 9:30</span></div>
                        </div>
                        <div class="layout-024"><a href="#" class="surface-005">LINEする</a><a href="tel:" class="text-019"><span>電話する 080-XXXX-XXXX</span></a></div>
                      </div>
                    </div>
                  </section>
                  <section aria-labelledby="h-check7" class="surface-006">
                    <div class="layout-021">
                      <h2 id="h-check7" class="text-020">7日前確認が済んでいない人</h2>
                      <span class="text-002">本人には5日前・3日前にもう一度届きます</span></div>
                    <div class="layout-022">
                      <div class="layout-023">
                        <div class="space-003">
                          <div class="text-017"><span>佐藤 花子</span><span class="surface-007">未確認</span></div>
                          <div class="text-018"><span>10/24(土) ハロウィンマルシェ</span> ・ <span>7日前・5日前に送信済み、3日前(10/21)にもう一度送ります</span></div>
                        </div>
                        <div class="layout-024"><a href="#" class="surface-005">LINEする</a><a href="tel:" class="text-019"><span>電話する 090-XXXX-XXXX</span></a></div>
                      </div>
                      <div class="layout-023">
                        <div class="space-003">
                          <div class="text-017"><span>山本 大輔</span><span class="surface-007">未確認</span></div>
                          <div class="text-018"><span>10/25(日) 体力測定会</span> ・ <span>7日前に送信済み、5日前(10/20)にもう一度送ります</span></div>
                        </div>
                        <div class="layout-024"><a href="#" class="surface-005">LINEする</a><a href="tel:" class="text-019"><span>電話する 080-XXXX-XXXX</span></a></div>
                      </div>
                    </div>
                  </section>
                  <section aria-labelledby="h-shortage" class="surface-006">
                    <div class="layout-021">
                      <h2 id="h-shortage" class="text-020">人数不足のイベント(2週間以内)</h2>
                      <a href="Shortage.dc.html" class="text-021">すべて見る</a></div>
                    <div class="utility-001">
                      <table class="text-022">
                        <thead>
                          <tr class="text-023"><th class="cell-001">日付</th><th class="cell-002">イベント / 依頼先</th><th class="cell-002">確定 / 必要</th><th class="cell-002"><span class="space-004">操作</span></th></tr>
                        </thead>
                        <tbody>
                          <tr><td class="cell-003"><span>10/21(水)</span></td><td class="cell-004">
                            <div class="text-024"><span>秋季健康フェア</span></div>
                            <div class="text-025"><span>サンプル商事 / 地域イベント運営</span></div>
                            </td><td class="cell-005"><span>2 / 5人</span><span class="surface-008"><span>あと3人</span></span></td><td class="cell-006"><a href="Assign.dc.html" class="text-026">アサインを見る</a></td></tr>
                          <tr><td class="cell-003"><span>10/24(土)</span></td><td class="cell-004">
                            <div class="text-024"><span>ハロウィンマルシェ</span></div>
                            <div class="text-025"><span>サンプル開発 / 商業施設イベント</span></div>
                            </td><td class="cell-005"><span>4 / 6人</span><span class="surface-008"><span>あと2人</span></span></td><td class="cell-006"><a href="Assign.dc.html" class="text-026">アサインを見る</a></td></tr>
                          <tr><td class="cell-003"><span>10/31(土)</span></td><td class="cell-004">
                            <div class="text-024"><span>市民マラソン</span></div>
                            <div class="text-025"><span>サンプル協会 / スポーツ大会</span></div>
                            </td><td class="cell-005"><span>7 / 8人</span><span class="surface-008"><span>あと1人</span></span></td><td class="cell-006"><a href="Assign.dc.html" class="text-026">アサインを見る</a></td></tr>
                        </tbody>
                      </table>
                    </div>
                  </section>
                  <section aria-labelledby="h-pending" class="surface-006">
                    <div class="layout-021">
                      <h2 id="h-pending" class="text-020">回答が来ていない依頼</h2>
                      <a href="#" class="text-021">すべて見る</a></div>
                    <div class="utility-001">
                      <table class="text-027">
                        <thead>
                          <tr class="text-023"><th class="cell-001">スタッフ</th><th class="cell-002">イベント</th><th class="cell-002">依頼日</th><th class="cell-002">経過</th></tr>
                        </thead>
                        <tbody>
                          <tr><td class="cell-007"><span>佐藤 花子</span></td><td class="cell-004"><span>10/21 秋季健康フェア</span></td><td class="cell-005"><span>10/15</span></td><td class="cell-008"><span>4日</span></td></tr>
                          <tr><td class="cell-007"><span>鈴木 一郎</span></td><td class="cell-004"><span>10/24 ハロウィンマルシェ(設営)</span></td><td class="cell-005"><span>10/16</span></td><td class="cell-008"><span>3日</span></td></tr>
                          <tr><td class="cell-007"><span>高橋 美咲</span></td><td class="cell-004"><span>10/31 市民マラソン(給水所)</span></td><td class="cell-005"><span>10/17</span></td><td class="cell-008"><span>2日</span></td></tr>
                        </tbody>
                      </table>
                    </div>
                  </section>
                </div>
                <div class="layout-025">
                  <section aria-labelledby="h-mine" class="surface-006">
                    <h2 id="h-mine" class="text-020">あなたの予定</h2>
                    <div class="layout-026">
                      <div class="surface-009">
                        <div class="text-002">未回答の依頼</div>
                        <div class="text-028">1<span class="text-029">件</span></div>
                      </div>
                      <div class="surface-009">
                        <div class="text-002">今年の勤務回数</div>
                        <div class="text-030">22<span class="text-029">回</span></div>
                      </div>
                    </div>
                    <div class="layout-027">
                      <div class="layout-028">
                        <div class="text-031"><span>10/21(水)</span></div>
                        <div class="space-003">
                          <div class="text-032"><span>秋季健康フェア</span></div>
                          <div class="text-018"><span>集合 8:30(備考:リーダー、集合は8:00)</span> ・ <span>サンプル公民館 1階ロビー</span></div>
                        </div>
                      </div>
                      <div class="layout-028">
                        <div class="text-031"><span>10/25(日)</span></div>
                        <div class="space-003">
                          <div class="text-032"><span>体力測定会(測定補助)</span></div>
                          <div class="text-018"><span>集合 9:00</span> ・ <span>サンプル市 総合運動場</span></div>
                        </div>
                      </div>
                    </div>
                  </section>
                  <section aria-labelledby="h-qual" class="surface-006">
                    <h2 id="h-qual" class="text-020">期限が近い資格</h2>
                    <div class="text-033">60日以内に期限が切れるもの</div>
                    <div class="layout-022">
                      <div class="layout-029">
                        <div class="space-003">
                          <div class="text-032"><span>田中 健</span></div>
                          <div class="text-018"><span>普通自動車免許</span></div>
                        </div>
                        <div class="text-034"><span>2026/11/08まで</span></div>
                      </div>
                      <div class="layout-029">
                        <div class="space-003">
                          <div class="text-032"><span>伊藤 愛</span></div>
                          <div class="text-018"><span>防災士</span></div>
                        </div>
                        <div class="text-034"><span>2026/11/30まで</span></div>
                      </div>
                    </div>
                  </section>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
