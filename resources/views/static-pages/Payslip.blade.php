<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>支払明細</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--payslip" data-static-screen="Payslip">
    <div class="app-root">
      <div class="screen">
        <div class="layout-062">
          <header class="layout-030">
            <div class="text-035">ハイクラス業務管理システム</div>
            <div class="text-002">{{ isset($year) ? auth()->user()->name : '佐藤 花子' }}(スタッフ)</div>
          </header>
          <main class="layout-031">
            @if(isset($year))
            @php $total=$paymentAssignments->sum('total_payment'); $paid=$paymentAssignments->where('payment_status','paid')->sum('total_payment'); $waiting=$paymentAssignments->where('payment_status','waiting')->sum('total_payment'); $statusLabels=['paid'=>'支払済み','waiting'=>'支払待ち','unconfirmed'=>'未確定']; $statusClasses=['paid'=>'surface-089','waiting'=>'surface-088','unconfirmed'=>'surface-087']; @endphp
            <div><h1 class="text-036">支払明細</h1><form method="GET" action="{{ route('my.payments') }}"><label class="text-037"><input name="year" type="number" min="2000" max="2100" value="{{ $year }}">年(1〜12月)の勤務分</label><button type="submit" class="control-024">表示</button></form></div>
            <section aria-label="今年の合計" class="surface-010"><div class="layout-043"><span class="text-007">総支払額({{ $paymentAssignments->count() }}回)</span><span class="text-173">¥{{ number_format($total) }}</span></div><div class="layout-167"><div><div class="text-174">支払済み</div><div class="text-068">¥{{ number_format($paid) }}</div></div><div><div class="text-175">支払待ち</div><div class="text-176">¥{{ number_format($waiting) }}</div></div><div><div class="text-174">未確定</div><div class="text-068">{{ $paymentAssignments->where('payment_status','unconfirmed')->count() }}件</div></div></div></section>
            <section aria-labelledby="ps-list" class="surface-010"><h2 id="ps-list" class="text-014">勤務ごとの明細</h2><div class="layout-168">@forelse($paymentAssignments as $assignment)<div class="cell-017"><div class="layout-063"><div class="space-003"><div class="text-002">{{ $assignment->event->event_date->isoFormat('M/D(ddd)') }}</div><div class="text-177">{{ $assignment->event->name ?: $assignment->event->business_name }}</div></div><div class="utility-017"><div class="text-178">¥{{ number_format($assignment->total_payment) }}</div><span class="{{ $statusClasses[$assignment->payment_status] ?? 'surface-087' }}">{{ $statusLabels[$assignment->payment_status] ?? $assignment->payment_status }}</span></div></div><div class="text-054">謝金 ¥{{ number_format($assignment->fee) }} + 交通費 ¥{{ number_format($assignment->transportation) }}@if($assignment->reimbursement) + 立替 ¥{{ number_format($assignment->reimbursement) }}@endif @if($assignment->withholding_tax) - 源泉徴収 ¥{{ number_format($assignment->withholding_tax) }}@endif @if($assignment->paid_at) ・ {{ $assignment->paid_at->format('n/j') }}に支払い@endif</div></div>@empty<div class="cell-017">この年の支払対象勤務はありません。</div>@endforelse</div></section>
            @else
            <div>
              <h1 class="text-036">支払明細</h1>
              <div class="text-037">2026年(1〜12月)の勤務分</div>
            </div>
            <section aria-label="今年の合計" class="surface-010">
              <div class="layout-043"><span class="text-007">総支払額(実施済み14回)</span><span class="text-173">¥136,280</span></div>
              <div class="layout-167">
                <div>
                  <div class="text-174">支払済み</div>
                  <div class="text-068">¥126,800</div>
                </div>
                <div>
                  <div class="text-175">支払待ち</div>
                  <div class="text-176">¥9,480</div>
                </div>
                <div>
                  <div class="text-174">未確定</div>
                  <div class="text-068">2件</div>
                </div>
              </div>
            </section>
            <section aria-labelledby="ps-list" class="surface-010">
              <h2 id="ps-list" class="text-014">勤務ごとの明細</h2>
              <div class="layout-168">
                <div class="cell-017">
                  <div class="layout-063">
                    <div class="space-003">
                      <div class="text-002"><span>11/3(火)</span></div>
                      <div class="text-177"><span>文化祭(来場者誘導)</span></div>
                    </div>
                    <div class="utility-017">
                      <div class="text-178"><span>¥9,480</span></div>
                      <span class="surface-087"><span>未確定</span></span></div>
                  </div>
                  <div class="text-054"><span>謝金 ¥9,000 + 交通費 ¥480(実施後に支払待ちになります)</span></div>
                </div>
                <div class="cell-017">
                  <div class="layout-063">
                    <div class="space-003">
                      <div class="text-002"><span>10/24(土)</span></div>
                      <div class="text-177"><span>ハロウィンマルシェ(設営)</span></div>
                    </div>
                    <div class="utility-017">
                      <div class="text-178"><span>¥9,480</span></div>
                      <span class="surface-087"><span>未確定</span></span></div>
                  </div>
                  <div class="text-054"><span>謝金 ¥9,000 + 交通費 ¥480(実施後に支払待ちになります)</span></div>
                </div>
                <div class="cell-017">
                  <div class="layout-063">
                    <div class="space-003">
                      <div class="text-002"><span>10/18(日)</span></div>
                      <div class="text-177"><span>秋祭り(運営補助)</span></div>
                    </div>
                    <div class="utility-017">
                      <div class="text-178"><span>¥9,480</span></div>
                      <span class="surface-088"><span>支払待ち</span></span></div>
                  </div>
                  <div class="text-054"><span>謝金 ¥9,000 + 交通費 ¥480</span></div>
                </div>
                <div class="cell-017">
                  <div class="layout-063">
                    <div class="space-003">
                      <div class="text-002"><span>10/10(土)</span></div>
                      <div class="text-177"><span>商店街イベント(設営)</span></div>
                    </div>
                    <div class="utility-017">
                      <div class="text-178"><span>¥9,480</span></div>
                      <span class="surface-089"><span>支払済み</span></span></div>
                  </div>
                  <div class="text-054"><span>謝金 ¥9,000 + 交通費 ¥480 ・ 10/16に支払い</span></div>
                </div>
                <div class="cell-017">
                  <div class="layout-063">
                    <div class="space-003">
                      <div class="text-002"><span>9/27(日)</span></div>
                      <div class="text-177"><span>地域スポーツ大会(受付)</span></div>
                    </div>
                    <div class="utility-017">
                      <div class="text-178"><span>¥10,780</span></div>
                      <span class="surface-089"><span>支払済み</span></span></div>
                  </div>
                  <div class="text-054"><span>謝金 ¥9,000 + 交通費 ¥480 + 立替 ¥1,300 ・ 10/2に支払い</span></div>
                </div>
              </div>
              <a href="#" class="cell-063">さらに表示(残り11件)</a></section>
            @endif
          </main>
          <nav aria-label="メインメニュー" class="layout-036"><a href="Staff.dc.html" class="layout-038"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"></path><path d="M5 9.5V21h14V9.5"></path></svg>ホーム</a><a href="MyRequests.dc.html" class="layout-038"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>依頼</a><a href="#" aria-current="page" class="layout-037"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"></path><path d="M9 8h6M9 12h6"></path></svg>支払明細</a><a href="Profile.dc.html" class="layout-038"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"></path></svg>マイプロフィール</a></nav>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
