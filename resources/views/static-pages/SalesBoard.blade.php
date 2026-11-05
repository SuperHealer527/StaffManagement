<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>提案状況ボード</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--salesboard" data-static-screen="SalesBoard">
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
              <a href="SalesHome.dc.html" class="layout-010"><span>営業ホーム</span></a><a href="Clients.dc.html" class="layout-010"><span>依頼先(顧客リスト)</span><span class="surface-001">共通</span></a><a href="#" aria-current="page" class="layout-009"><span>提案状況</span></a><a href="Meetings.dc.html" class="layout-010"><span>打合せ履歴</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-200">
              <div class="layout-013">
                <div>
                  <h1 class="text-044">提案状況</h1>
                  <div class="text-060">依頼先を提案状況ごとの列に並べます。状況・次回アクション日は各依頼先の詳細で変えます。社員だけが開けます。</div>
                </div>
                @if(isset($proposalClients))<form method="GET" action="{{ route('sales.proposals') }}" class="layout-070"><input name="keyword" value="{{ request('keyword') }}" type="search" aria-label="依頼先名で探す" placeholder="依頼先名で探す" class="text-047"><select name="type" aria-label="種別" class="surface-017"><option value="">種別:すべて</option>@foreach(['corporation'=>'法人','government'=>'行政','individual'=>'個人','organization'=>'団体','other'=>'その他'] as $value=>$label)<option value="{{ $value }}" @selected(request('type')===$value)>{{ $label }}</option>@endforeach</select><button class="control-024">検索</button></form>@else<div class="layout-070"><input type="text" aria-label="依頼先名で探す" placeholder="依頼先名で探す" class="text-047"><select aria-label="種別" class="surface-017"><option>種別:すべて</option><option>法人</option><option>行政</option><option>個人</option><option>団体</option><option>その他</option></select></div>@endif
              </div>
              <div class="utility-021">
                <div class="layout-201">
                  @if(isset($proposalClients))
                  @php $statusLabels=['uncontacted'=>'未接触','approaching'=>'アプローチ中','proposing'=>'提案中','won'=>'受注','lost'=>'失注','on_hold'=>'保留']; $statusClasses=['uncontacted'=>'surface-105','approaching'=>'surface-107','proposing'=>'surface-108','won'=>'surface-109','lost'=>'surface-110','on_hold'=>'surface-111']; @endphp
                  @foreach($statusLabels as $status=>$label)
                  <section aria-label="{{ $label }}" class="layout-202"><div class="layout-203"><span class="{{ $statusClasses[$status] }}">{{ $label }}</span><span class="text-210">{{ ($proposalClients->get($status) ?? collect())->count() }}社</span></div>@forelse(($proposalClients->get($status) ?? collect()) as $proposalClient)<a href="{{ route('clients.edit',$proposalClient) }}" class="surface-106"><div class="text-017">{{ $proposalClient->name }}</div><div class="text-059">{{ $proposalClient->proposal_notes ?: '提案内容未入力' }}</div><div class="layout-204"><span class="{{ $proposalClient->next_action_date && $proposalClient->next_action_date->isPast() ? 'text-213':'text-211' }}">次回 {{ $proposalClient->next_action_date?->format('n/j') ?? '未定' }}@if($proposalClient->next_action_date?->isPast()) 過ぎ@endif</span><span class="utility-002">{{ $proposalClient->salesOwner?->name ?? '未担当' }}</span></div></a>@empty<div class="surface-106">該当なし</div>@endforelse</section>
                  @endforeach
                  @else
                  <section aria-label="未接触" class="layout-202">
                    <div class="layout-203"><span class="surface-105"><span>未接触</span></span><span class="text-210"><span>15</span>社</span></div>
                    <a href="ClientEdit.dc.html" class="surface-106">
                    <div class="text-017"><span>サンプル病院</span></div>
                    <div class="text-059"><span>健診会場 受付</span></div>
                    <div class="layout-204"><span class="text-211">次回 <span>未定</span></span><span class="utility-002"><span>山田</span></span></div>
                    </a><a href="ClientEdit.dc.html" class="surface-106">
                    <div class="text-017"><span>サンプル体育協会</span></div>
                    <div class="text-059"><span>市民大会 運営補助</span></div>
                    <div class="layout-204"><span class="text-211">次回 <span>未定</span></span><span class="utility-002"><span>山田</span></span></div>
                    </a><a href="Clients.dc.html" class="text-212">ほか <span>13</span>社を一覧で見る</a></section>
                  <section aria-label="アプローチ中" class="layout-202">
                    <div class="layout-203"><span class="surface-107"><span>アプローチ中</span></span><span class="text-210"><span>6</span>社</span></div>
                    <a href="ClientEdit.dc.html" class="surface-106">
                    <div class="text-017"><span>サンプル不動産</span></div>
                    <div class="text-059"><span>モデルルーム 来場受付</span></div>
                    <div class="layout-204"><span class="text-213">次回 <span>10/22 過ぎ</span></span><span class="utility-002"><span>山田</span></span></div>
                    </a><a href="ClientEdit.dc.html" class="surface-106">
                    <div class="text-017"><span>サンプル交通</span></div>
                    <div class="text-059"><span>駅前キャンペーン</span></div>
                    <div class="layout-204"><span class="text-211">次回 <span>10/30</span></span><span class="utility-002"><span>田村</span></span></div>
                    </a><a href="Clients.dc.html" class="text-212">ほか <span>4</span>社を一覧で見る</a></section>
                  <section aria-label="提案中" class="layout-202">
                    <div class="layout-203"><span class="surface-108"><span>提案中</span></span><span class="text-210"><span>4</span>社</span></div>
                    <a href="ClientEdit.dc.html" class="surface-106">
                    <div class="text-017"><span>サンプル商事</span></div>
                    <div class="text-059"><span>体力測定会(年4回)の運営</span></div>
                    <div class="layout-204"><span class="text-211">次回 <span>10/28</span></span><span class="utility-002"><span>山田</span></span></div>
                    </a><a href="ClientEdit.dc.html" class="surface-106">
                    <div class="text-017"><span>サンプル観光協会</span></div>
                    <div class="text-059"><span>冬のイルミネーション 誘導</span></div>
                    <div class="layout-204"><span class="text-211">次回 <span>10/28</span></span><span class="utility-002"><span>山田</span></span></div>
                    </a><a href="ClientEdit.dc.html" class="surface-106">
                    <div class="text-017"><span>サンプル大学</span></div>
                    <div class="text-059"><span>オープンキャンパス 受付</span></div>
                    <div class="layout-204"><span class="text-211">次回 <span>11/2</span></span><span class="utility-002"><span>田村</span></span></div>
                    </a><a href="Clients.dc.html" class="text-212">ほか <span>1</span>社を一覧で見る</a></section>
                  <section aria-label="受注" class="layout-202">
                    <div class="layout-203"><span class="surface-109"><span>受注</span></span><span class="text-210"><span>3</span>社</span></div>
                    <a href="ClientEdit.dc.html" class="surface-106">
                    <div class="text-017"><span>サンプル協会</span></div>
                    <div class="text-059"><span>市民マラソン 給水所</span></div>
                    <div class="layout-204"><span class="text-211">次回 <span>—</span></span><span class="utility-002"><span>田村</span></span></div>
                    </a><a href="ClientEdit.dc.html" class="surface-106">
                    <div class="text-017"><span>サンプル町会</span></div>
                    <div class="text-059"><span>秋祭り・文化祭の運営</span></div>
                    <div class="layout-204"><span class="text-211">次回 <span>—</span></span><span class="utility-002"><span>山田</span></span></div>
                    </a><a href="Clients.dc.html" class="text-212">ほか <span>1</span>社を一覧で見る</a></section>
                  <section aria-label="失注" class="layout-202">
                    <div class="layout-203"><span class="surface-110"><span>失注</span></span><span class="text-210"><span>1</span>社</span></div>
                    <a href="ClientEdit.dc.html" class="surface-106">
                    <div class="text-017"><span>サンプル百貨店</span></div>
                    <div class="text-059"><span>催事場 販売補助</span></div>
                    <div class="layout-204"><span class="text-211">次回 <span>—</span></span><span class="utility-002"><span>山田</span></span></div>
                    </a></section>
                  <section aria-label="保留" class="layout-202">
                    <div class="layout-203"><span class="surface-111"><span>保留</span></span><span class="text-210"><span>2</span>社</span></div>
                    <a href="ClientEdit.dc.html" class="surface-106">
                    <div class="text-017"><span>サンプル商店会</span></div>
                    <div class="text-059"><span>年末大売り出し 呼び込み</span></div>
                    <div class="layout-204"><span class="text-211">次回 <span>11/5</span></span><span class="utility-002"><span>田村</span></span></div>
                    </a><a href="Clients.dc.html" class="text-212">ほか <span>1</span>社を一覧で見る</a></section>
                  @endif
                </div>
              </div>
              <div class="text-002">「未接触」は依頼先一覧に登録しただけの営業先です。営業リストとして使うときは、依頼先一覧を「未接触」で絞り込みます。</div>
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
