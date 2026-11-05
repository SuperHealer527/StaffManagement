<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>収支一覧</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--profit" data-static-screen="Profit">
    <div class="app-root">
      <div class="screen">
        <div class="layout-188">
          <div class="layout-002">
            <nav aria-label="エリア切り替え" class="layout-003">
              <div aria-hidden="true" class="layout-004">HC</div>
              <a href="Main.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="16" y1="3" x2="16" y2="7"></line></svg><span class="text-001">事業</span></a><a href="SalesHome.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 17 9 11 13 15 21 7"></polyline><polyline points="14 7 21 7 21 14"></polyline></svg><span class="text-001">営業</span></a><a href="Coaching.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H9l-4 3v-3a2 2 0 0 1-2-2z"></path><path d="M19 9a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2v3l-4-3h-3"></path></svg><span class="text-001">面談</span></a><a href="PhoneLog.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><rect x="9" y="2" width="6" height="4" rx="1"></rect><line x1="9" y1="11" x2="15" y2="11"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg><span class="text-001">総務</span></a><a href="#" aria-current="page" class="layout-005"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4l5 7 5-7"></path><line x1="12" y1="11" x2="12" y2="20"></line><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg><span class="text-001">会計</span></a>
              <div class="layout-007"><a href="Accounts.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"></line><circle cx="9" cy="7" r="2.2" fill="#16213A"></circle><line x1="4" y1="17" x2="20" y2="17"></line><circle cx="15" cy="17" r="2.2" fill="#16213A"></circle></svg><span class="text-001">設定</span></a></div>
            </nav>
            <nav aria-label="メインメニュー" class="layout-008">
              <div class="space-001">
                <div class="text-002">ハイクラス業務管理システム</div>
                <div class="text-003">会計</div>
              </div>
              <a href="PayManage.dc.html" class="layout-010"><span>支払管理</span></a><a href="#" aria-current="page" class="layout-009"><span>収支一覧</span></a><a href="#" class="layout-010"><span>請求</span><span class="surface-023">今後</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              @if(isset($profitRows))
              @php $invoiceTotal=$profitRows->whereNotNull('invoice')->sum('invoice'); $costTotal=$profitRows->sum('cost'); $profitTotal=$profitRows->whereNotNull('invoice')->sum('profit'); $marginTotal=$invoiceTotal>0?round($profitTotal/$invoiceTotal*100,1):null; $missingCount=$profitRows->whereNull('invoice')->count(); @endphp
              <div><h1 class="text-044">収支一覧</h1><div class="text-191">イベントごとの請求と、謝金・交通費・立替・経費から利益を計算します。金額は税抜です。</div></div>
              <div class="layout-013"><form method="GET" action="{{ route('finance.profit') }}" class="layout-189"><div class="layout-051"><label for="pf-month" class="text-002">月(実施日)</label><input id="pf-month" name="month" type="month" value="{{ $month->format('Y-m') }}" class="surface-017"></div><div role="group" class="layout-051"><span class="text-002">表示</span><div class="layout-190"><button name="group" value="event" class="{{ $group==='event'?'control-061':'control-062' }}">イベントごと</button><button name="group" value="client" class="{{ $group==='client'?'control-061':'control-062' }}">依頼先ごと</button></div></div></form><a href="{{ route('finance.profit.csv',['month'=>$month->format('Y-m')]) }}" class="control-063">CSVで書き出す</a></div>
              <section aria-label="この月の合計" class="layout-014"><div class="surface-024"><div class="text-007">請求の合計</div><div class="text-063">¥{{ number_format($invoiceTotal) }}</div><div class="text-018">{{ $profitRows->count() }}件のイベント</div></div><div class="surface-024"><div class="text-007">原価の合計</div><div class="text-063">¥{{ number_format($costTotal) }}</div><div class="text-018">謝金・交通費・立替・経費</div></div><div class="surface-095"><div class="text-007">利益</div><div class="text-192">¥{{ number_format($profitTotal) }}</div><div class="text-193">利益率 <strong>{{ $marginTotal===null?'—':$marginTotal.'%' }}</strong></div></div><div class="surface-025"><div class="text-011">請求が未入力</div><div class="text-064">{{ $missingCount }}<span class="text-010">件</span></div><div class="text-194">合計には入れていません</div></div></section>
              <section aria-labelledby="h-events" class="surface-006"><h2 id="h-events" class="text-020">{{ $group==='client'?'依頼先ごと':'イベントごと' }}の収支</h2><div class="utility-003"><table class="text-195"><thead><tr class="text-065">@if($group==='event')<th class="cell-064">実施日</th>@endif<th class="cell-065">{{ $group==='client'?'依頼先':'イベント / 依頼先' }}</th><th class="cell-016">請求</th><th class="cell-016">謝金</th><th class="cell-016">交通費</th><th class="cell-016">立替</th><th class="cell-016">経費</th><th class="cell-016">原価</th><th class="cell-016">利益</th><th class="cell-066">利益率</th></tr></thead><tbody>
              @if($group==='event')
                @forelse($profitRows as $row)<tr class="utility-004"><td class="cell-067">{{ $row['event']->event_date->isoFormat('M/D(ddd)') }}</td><td class="cell-068"><a href="{{ route('events.edit',$row['event']) }}" class="text-196">{{ $row['event']->name ?: $row['event']->business_name }}</a><div class="text-018">{{ $row['event']->client->name }}</div></td><td class="cell-069">@if($row['invoice']===null)<span class="surface-027">未入力</span>@else¥{{ number_format($row['invoice']) }}@endif</td><td class="cell-070">¥{{ number_format($row['fee']) }}</td><td class="cell-070">¥{{ number_format($row['transportation']) }}</td><td class="cell-070">¥{{ number_format($row['reimbursement']) }}</td><td class="cell-070">¥{{ number_format($row['expense']) }}</td><td class="cell-071">¥{{ number_format($row['cost']) }}</td><td class="{{ ($row['profit']??0)<0?'cell-074':'cell-072' }}">{{ $row['profit']===null?'—':'¥'.number_format($row['profit']) }}</td><td class="cell-073">{{ $row['margin']===null?'—':$row['margin'].'%' }}</td></tr>@empty<tr><td colspan="10">この月のイベントはありません。</td></tr>@endforelse
              @else
                @forelse($profitRows->groupBy(fn($row)=>$row['event']->client_id) as $clientRows)@php $clientInvoice=$clientRows->whereNotNull('invoice')->sum('invoice'); $clientCost=$clientRows->sum('cost'); $clientProfit=$clientRows->whereNotNull('invoice')->sum('profit'); @endphp<tr class="utility-004"><td class="cell-068">{{ $clientRows->first()['event']->client->name }}<div class="text-018">{{ $clientRows->count() }}イベント</div></td><td class="cell-069">¥{{ number_format($clientInvoice) }}</td><td class="cell-070">¥{{ number_format($clientRows->sum('fee')) }}</td><td class="cell-070">¥{{ number_format($clientRows->sum('transportation')) }}</td><td class="cell-070">¥{{ number_format($clientRows->sum('reimbursement')) }}</td><td class="cell-070">¥{{ number_format($clientRows->sum('expense')) }}</td><td class="cell-071">¥{{ number_format($clientCost) }}</td><td class="cell-072">¥{{ number_format($clientProfit) }}</td><td class="cell-073">{{ $clientInvoice>0?round($clientProfit/$clientInvoice*100,1).'%':'—' }}</td></tr>@empty<tr><td colspan="9">この月のイベントはありません。</td></tr>@endforelse
              @endif
              </tbody></table></div></section><div class="text-056">原価 = 謝金 + 交通費 + 立替 + 経費。源泉税は原価から引きません。利益率 = 利益 ÷ 請求。</div>
              @else
              <div>
                <h1 class="text-044">収支一覧</h1>
                <div class="text-191">イベントごとに、依頼先への請求と、かかった原価(謝金・交通費・立替・経費)から利益を出します。金額はすべて税抜です。請求と経費は、各イベントの編集画面の「請求と収支」で入れます。</div>
              </div>
              <div class="layout-013">
                <div class="layout-189">
                  <div class="layout-051"><label for="pf-month" class="text-002">月(実施日)</label><select id="pf-month" class="surface-017"><option>2026年10月</option><option>2026年9月</option><option>2026年8月</option></select></div>
                  <div role="group" aria-label="表示の単位" class="layout-051"><span class="text-002">表示</span>
                    <div class="layout-190"><button type="button" aria-pressed="true" class="control-061">イベントごと</button><button type="button" aria-pressed="false" class="control-062">依頼先ごと</button></div>
                  </div>
                </div>
                <button type="button" class="control-063">CSVで書き出す</button></div>
              <section aria-label="この月の合計" class="layout-014">
                <div class="surface-024">
                  <div class="text-007">請求の合計</div>
                  <div class="text-063"><span>¥244,000</span></div>
                  <div class="text-018"><span>5</span>件のイベント</div>
                </div>
                <div class="surface-024">
                  <div class="text-007">原価の合計</div>
                  <div class="text-063"><span>¥172,210</span></div>
                  <div class="text-018">謝金・交通費・立替・経費</div>
                </div>
                <div class="surface-095">
                  <div class="text-007">利益</div>
                  <div class="text-192"><span>¥71,790</span></div>
                  <div class="text-193">利益率 <strong><span>29.4%</span></strong></div>
                </div>
                <div class="surface-025">
                  <div class="text-011">請求が未入力</div>
                  <div class="text-064"><span>1</span><span class="text-010">件</span></div>
                  <div class="text-194">合計には入れていません</div>
                </div>
              </section>
              <section aria-labelledby="h-events" class="surface-006">
                <h2 id="h-events" class="text-020">イベントごとの収支</h2>
                <div class="utility-003">
                  <table class="text-195">
                    <thead>
                      <tr class="text-065"><th class="cell-064">実施日</th><th class="cell-065">イベント / 依頼先</th><th class="cell-016">請求</th><th class="cell-016">謝金</th><th class="cell-016">交通費</th><th class="cell-016">立替</th><th class="cell-016">経費</th><th class="cell-016">原価</th><th class="cell-016">利益</th><th class="cell-066">利益率</th></tr>
                    </thead>
                    <tbody>
                      <tr class="utility-004"><td class="cell-067"><span>10/10(土)</span></td><td class="cell-068"><a href="EventEdit.dc.html" class="text-196"><span>商店街イベント(設営)</span></a>
                        <div class="text-018"><span>サンプル商店会</span></div>
                        </td><td class="cell-069"><span class="text-024"><span>¥40,000</span></span></td><td class="cell-070"><span>¥18,500</span></td><td class="cell-070"><span>¥1,100</span></td><td class="cell-070"><span>¥0</span></td><td class="cell-070"><span>¥1,500</span></td><td class="cell-071"><span>¥21,100</span></td><td class="cell-072"><span>¥18,900</span></td><td class="cell-073"><span>47.3%</span></td></tr>
                      <tr class="utility-004"><td class="cell-067"><span>10/12(月)</span></td><td class="cell-068"><a href="EventEdit.dc.html" class="text-196"><span>地域清掃イベント(誘導)</span></a>
                        <div class="text-018"><span>サンプル市</span></div>
                        </td><td class="cell-069"><span class="text-024"><span>¥36,000</span></span></td><td class="cell-070"><span>¥18,000</span></td><td class="cell-070"><span>¥1,400</span></td><td class="cell-070"><span>¥650</span></td><td class="cell-070"><span>¥0</span></td><td class="cell-071"><span>¥20,050</span></td><td class="cell-072"><span>¥15,950</span></td><td class="cell-073"><span>44.3%</span></td></tr>
                      <tr class="utility-004"><td class="cell-067"><span>10/17(土)</span></td><td class="cell-068"><a href="EventEdit.dc.html" class="text-196"><span>防災フェア(受付)</span></a>
                        <div class="text-018"><span>サンプル市</span></div>
                        </td><td class="cell-069"><span class="text-024"><span>¥30,000</span></span></td><td class="cell-070"><span>¥27,000</span></td><td class="cell-070"><span>¥2,200</span></td><td class="cell-070"><span>¥1,200</span></td><td class="cell-070"><span>¥900</span></td><td class="cell-071"><span>¥31,300</span></td><td class="cell-074"><span>−¥1,300</span></td><td class="cell-075"><span>−4.3%</span></td></tr>
                      <tr class="utility-004"><td class="cell-067"><span>10/18(日)</span></td><td class="cell-068"><a href="EventEdit.dc.html" class="text-196"><span>秋祭り(運営補助)</span></a>
                        <div class="text-018"><span>サンプル町会</span></div>
                        </td><td class="cell-069"><span class="text-024"><span>¥60,000</span></span></td><td class="cell-070"><span>¥37,000</span></td><td class="cell-070"><span>¥3,000</span></td><td class="cell-070"><span>¥0</span></td><td class="cell-070"><span>¥2,400</span></td><td class="cell-071"><span>¥42,400</span></td><td class="cell-072"><span>¥17,600</span></td><td class="cell-073"><span>29.3%</span></td></tr>
                      <tr class="utility-004"><td class="cell-067"><span>10/21(水)</span></td><td class="cell-068"><a href="EventEdit.dc.html" class="text-196"><span>秋季健康フェア</span></a>
                        <div class="text-018"><span>サンプル商事</span></div>
                        </td><td class="cell-069"><span class="text-024"><span>¥78,000</span></span></td><td class="cell-070"><span>¥49,500</span></td><td class="cell-070"><span>¥3,660</span></td><td class="cell-070"><span>¥0</span></td><td class="cell-070"><span>¥4,200</span></td><td class="cell-071"><span>¥57,360</span></td><td class="cell-072"><span>¥20,640</span></td><td class="cell-073"><span>26.5%</span></td></tr>
                      <tr class="utility-018"><td class="cell-067"><span>10/24(土)</span></td><td class="cell-068"><a href="EventEdit.dc.html" class="text-196"><span>ハロウィンマルシェ(設営)</span></a>
                        <div class="text-018"><span>サンプル商店会</span></div>
                        </td><td class="cell-069"><span class="utility-019"><span class="surface-027">未入力</span><a href="EventEdit.dc.html" class="text-066">入力する</a></span></td><td class="cell-070"><span>¥18,000</span></td><td class="cell-070"><span>¥960</span></td><td class="cell-070"><span>¥0</span></td><td class="cell-070"><span>¥0</span></td><td class="cell-071"><span>¥18,960</span></td><td class="cell-076"><span>—</span></td><td class="cell-077"><span>—</span></td></tr>
                      <tr class="text-067"><td colspan="2" class="space-018">合計<span class="text-055">請求が未入力の<span>1</span>件を除く</span></td><td class="space-019"><span>¥244,000</span></td><td class="space-019"><span>¥150,000</span></td><td class="space-019"><span>¥11,360</span></td><td class="space-019"><span>¥1,850</span></td><td class="space-019"><span>¥9,000</span></td><td class="space-019"><span>¥172,210</span></td><td class="space-020"><span>¥71,790</span></td><td class="space-021"><span>29.4%</span></td></tr>
                    </tbody>
                  </table>
                </div>
              </section>
              <div class="text-056">原価 = 謝金 + 交通費 + 立替 + 経費。謝金・交通費・立替は「確定」「実施済み」のアサインの合計で、支払管理と同じ金額です(源泉税は謝金から預かって納めるものなので引きません)。利益率 = 利益 ÷ 請求。請求が未入力のイベントは合計に入れず、上の件数に出します。請求書は今の会計ソフトで作ります。</div>
              @endif
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
