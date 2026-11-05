<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>アカウント管理</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--accounts" data-static-screen="Accounts">
    <div class="app-root">
      <div class="screen">
        <div class="layout-061">
          <div class="layout-002">
            <nav aria-label="エリア切り替え" class="layout-003">
              <div aria-hidden="true" class="layout-004">HC</div>
              <a href="Main.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="16" y1="3" x2="16" y2="7"></line></svg><span class="text-001">事業</span></a><a href="SalesHome.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 17 9 11 13 15 21 7"></polyline><polyline points="14 7 21 7 21 14"></polyline></svg><span class="text-001">営業</span></a><a href="Coaching.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H9l-4 3v-3a2 2 0 0 1-2-2z"></path><path d="M19 9a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2v3l-4-3h-3"></path></svg><span class="text-001">面談</span></a><a href="PhoneLog.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><rect x="9" y="2" width="6" height="4" rx="1"></rect><line x1="9" y1="11" x2="15" y2="11"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg><span class="text-001">総務</span></a><a href="PayManage.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4l5 7 5-7"></path><line x1="12" y1="11" x2="12" y2="20"></line><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg><span class="text-001">会計</span></a>
              <div class="layout-007"><a href="#" aria-current="page" class="layout-005"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"></line><circle cx="9" cy="7" r="2.2" fill="#16213A"></circle><line x1="4" y1="17" x2="20" y2="17"></line><circle cx="15" cy="17" r="2.2" fill="#16213A"></circle></svg><span class="text-001">設定</span></a></div>
            </nav>
            <nav aria-label="メインメニュー" class="layout-008">
              <div class="space-001">
                <div class="text-002">ハイクラス業務管理システム</div>
                <div class="text-003">設定</div>
              </div>
              <a href="#" aria-current="page" class="layout-009"><span>アカウント管理</span></a><a href="SendLog.dc.html" class="layout-010"><span>送信履歴</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              <div class="layout-013">
                <div>
                  <h1 class="text-044">アカウント管理</h1>
                  <div class="text-060">アプリを使う人のアカウントの作成・招待、区分の変更、ログインの停止・再開を行います。単発の方は区分「その他(単発)」で「ログインなし」として登録し、連絡は公式LINEで届きます。トレーナーは「その他(トレーナー)」で、スタッフと同じように招待してログインしてもらいます。社員だけが使える画面です。</div>
                </div>
                <button type="button" data-create-account class="control-014">+ アカウントを作成して招待</button></div>
              <section aria-labelledby="ac-list" class="surface-006">
                <div class="layout-041">
                  <h2 id="ac-list" class="text-020">アカウント一覧(<span>10</span>件)</h2>
                  <div role="group" aria-label="状態で絞り込み" class="layout-024"><button type="button" aria-pressed="true" class="control-009"><span>すべて</span></button><button type="button" aria-pressed="false" class="control-008"><span>利用中</span></button><button type="button" aria-pressed="false" class="control-008"><span>招待中</span></button><button type="button" aria-pressed="false" class="control-008"><span>停止中</span></button><button type="button" aria-pressed="false" class="control-008"><span>ログインなし</span></button></div>
                </div>
                <div class="utility-001">
                  <table class="text-081">
                    <thead>
                      <tr class="text-023"><th class="cell-001">名前</th><th class="cell-002">メールアドレス・連絡先</th><th class="cell-010">区分</th><th class="cell-024">状態</th><th class="cell-024">LINE連携</th><th class="cell-024">最終ログイン</th><th class="cell-026">操作</th></tr>
                    </thead>
                    <tbody>
                      <tr class="utility-004"><td class="cell-027"><span>山田 太郎</span></td><td class="cell-028"><span>yamada@highclass-inc.com</span></td><td class="cell-004"><select aria-label="山田 太郎の区分" disabled="" class="surface-035"><option>社員</option><option>準社員</option><option>スタッフ</option><option>その他(単発)</option><option>その他(トレーナー)</option></select></td><td class="cell-028"><span class="surface-012"><span>利用中</span></span></td><td class="cell-028"><span class="surface-012"><span>連携済み</span></span></td><td class="cell-029"><span>今日 23:40</span></td><td class="cell-013"></td></tr>
                      <tr class="utility-004"><td class="cell-027"><span>佐々木 恵</span></td><td class="cell-028"><span>sasaki@highclass-inc.com</span></td><td class="cell-004"><select aria-label="佐々木 恵の区分" class="surface-035"><option>社員</option><option>準社員</option><option>スタッフ</option><option>その他(単発)</option><option>その他(トレーナー)</option></select></td><td class="cell-028"><span class="surface-012"><span>利用中</span></span></td><td class="cell-028"><span class="surface-012"><span>連携済み</span></span></td><td class="cell-029"><span>今日 18:12</span></td><td class="cell-013"><button type="button" class="control-015">ログイン停止</button></td></tr>
                      <tr class="utility-004"><td class="cell-027"><span>田村 優子</span></td><td class="cell-028"><span>tamura@highclass-inc.com</span></td><td class="cell-004"><select aria-label="田村 優子の区分" class="surface-035"><option>社員</option><option>準社員</option><option>スタッフ</option><option>その他(単発)</option><option>その他(トレーナー)</option></select></td><td class="cell-028"><span class="surface-012"><span>利用中</span></span></td><td class="cell-028"><span class="surface-012"><span>連携済み</span></span></td><td class="cell-029"><span>今日 09:02</span></td><td class="cell-013"><button type="button" class="control-015">ログイン停止</button></td></tr>
                      <tr class="utility-004"><td class="cell-027"><span>鈴木 一郎</span></td><td class="cell-028"><span>suzuki@example.com</span></td><td class="cell-004"><select aria-label="鈴木 一郎の区分" class="surface-035"><option>社員</option><option>準社員</option><option>スタッフ</option><option>その他(単発)</option><option>その他(トレーナー)</option></select></td><td class="cell-028"><span class="surface-012"><span>利用中</span></span></td><td class="cell-028"><span class="surface-012"><span>連携済み</span></span></td><td class="cell-029"><span>昨日 21:05</span></td><td class="cell-013"><button type="button" class="control-015">ログイン停止</button></td></tr>
                      <tr class="utility-004"><td class="cell-027"><span>佐藤 花子</span></td><td class="cell-028"><span>hanako@example.com</span></td><td class="cell-004"><select aria-label="佐藤 花子の区分" class="surface-035"><option>社員</option><option>準社員</option><option>スタッフ</option><option>その他(単発)</option><option>その他(トレーナー)</option></select></td><td class="cell-028"><span class="surface-012"><span>利用中</span></span></td><td class="cell-028"><span class="surface-012"><span>連携済み</span></span></td><td class="cell-029"><span>今日 07:31</span></td><td class="cell-013"><button type="button" class="control-015">ログイン停止</button></td></tr>
                      <tr class="utility-004"><td class="cell-027"><span>西田 涼</span></td><td class="cell-028"><span>nishida@example.com</span></td><td class="cell-004"><select aria-label="西田 涼の区分" class="surface-035"><option>社員</option><option>準社員</option><option>スタッフ</option><option>その他(単発)</option><option>その他(トレーナー)</option></select></td><td class="cell-028"><span class="surface-013"><span>招待中</span></span></td><td class="cell-028"><span class="surface-011"><span>未連携</span></span></td><td class="cell-029"><span>—</span></td><td class="cell-013"><button type="button" class="control-016">招待を再送</button><button type="button" class="control-015">ログイン停止</button></td></tr>
                      <tr class="utility-008"><td class="cell-027"><span>森 健二</span></td><td class="cell-028"><span>mori@example.com</span></td><td class="cell-004"><select aria-label="森 健二の区分" class="surface-035"><option>社員</option><option>準社員</option><option>スタッフ</option><option>その他(単発)</option><option>その他(トレーナー)</option></select></td><td class="cell-028"><span class="surface-016"><span>停止中</span></span></td><td class="cell-028"><span class="surface-011"><span>未連携</span></span></td><td class="cell-029"><span>2026/06/30</span></td><td class="cell-013"><button type="button" class="control-017">再開</button></td></tr>
                      <tr class="utility-004"><td class="cell-027"><span>前田 由紀</span></td><td class="cell-028"><span>maeda@example.com</span></td><td class="cell-004"><select aria-label="前田 由紀の区分" class="surface-035"><option>社員</option><option>準社員</option><option>スタッフ</option><option>その他(単発)</option><option>その他(トレーナー)</option></select></td><td class="cell-028"><span class="surface-012"><span>利用中</span></span></td><td class="cell-028"><span class="surface-012"><span>連携済み</span></span></td><td class="cell-029"><span>昨日 19:22</span></td><td class="cell-013"><button type="button" class="control-015">ログイン停止</button></td></tr>
                      <tr class="utility-004"><td class="cell-027"><span>小川 拓也</span></td><td class="cell-028"><span>ogawa@example.com</span></td><td class="cell-004"><select aria-label="小川 拓也の区分" class="surface-035"><option>社員</option><option>準社員</option><option>スタッフ</option><option>その他(単発)</option><option>その他(トレーナー)</option></select></td><td class="cell-028"><span class="surface-015"><span>ログインなし</span></span></td><td class="cell-028"><span class="surface-012"><span>連携済み</span></span></td><td class="cell-029"><span>—</span></td><td class="cell-013"></td></tr>
                      <tr class="utility-004"><td class="cell-027"><span>岡本 直人</span></td><td class="cell-028"><span>okamoto@example.com</span></td><td class="cell-004"><select aria-label="岡本 直人の区分" class="surface-035"><option>社員</option><option>準社員</option><option>スタッフ</option><option>その他(単発)</option><option>その他(トレーナー)</option></select></td><td class="cell-028"><span class="surface-015"><span>ログインなし</span></span></td><td class="cell-028"><span class="surface-011"><span>未連携</span></span></td><td class="cell-029"><span>—</span></td><td class="cell-013"></td></tr>
                    </tbody>
                  </table>
                </div>
                <div class="text-082">ログインを停止しても、これまでの勤務・支払・報告書の記録は残ります。退職した人は「ログイン停止」にしてください。自分自身の区分と停止は変更できません。「その他(単発)」の人をスタッフ・その他(トレーナー)など、ログインする区分に変えると、その時点で招待メールが届きます(メールアドレスが必要です)。LINE連携が「未連携」の人には、マイプロフィールの「LINEと連携する」から連携するよう声をかけてください。勤務・支払・報告書の記録はそのまま引き継がれます。</div>
              </section>
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
    <script type="application/json" id="account-directory">@json($staffMembers->mapWithKeys(fn ($member) => [$member->name => $member->id]))</script>
  </body>
</html>
