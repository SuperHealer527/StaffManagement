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
              @if ($event)
              @php $statusLabels = ['draft' => '下書き', 'recruiting' => '募集中', 'confirmed' => '確定', 'completed' => '実施済み', 'cancelled' => '中止']; $confirmedAssignments = $event->assignments->where('status', 'confirmed'); $shortage = max(0, $event->required_staff - $confirmedAssignments->count()); $leader = $confirmedAssignments->firstWhere('is_day_leader', true); $representative = $confirmedAssignments->firstWhere('is_report_representative', true); $roleLabels = ['employee' => '社員', 'semi_employee' => '準社員', 'staff' => 'スタッフ', 'temporary' => 'その他(単発)', 'trainer' => 'その他(トレーナー)']; $emergency = $event->emergency_contacts ?? []; $emergencyEmployee = ($employeeId = data_get($emergency, 'employee_id')) ? ($employees ?? collect())->firstWhere('id', (int) $employeeId) : null; @endphp
              <nav aria-label="パンくずリスト" class="text-007"><a href="{{ route('events.index') }}">イベント</a> / {{ $event->name ?: $event->business_name }}({{ $event->event_date->format('n/j') }})</nav>
              <div class="layout-045">
                <div>
                  <div class="layout-042">
                    <h1 class="text-044">{{ $event->name ?: $event->business_name }}</h1>
                    <span class="surface-018">{{ $statusLabels[$event->status] ?? $event->status }}</span>@if ($event->event_date->isFuture())<span class="surface-020">開催まであと{{ today()->diffInDays($event->event_date) }}日</span>@endif</div>
                  <div class="text-124">{{ $event->event_date->isoFormat('YYYY/M/D(ddd)') }} ・ {{ $event->client->name }} / {{ $event->business_name }}</div>
                </div>
                @if(auth()->user()->canManageAssignments())<a href="{{ route('events.assignments.index', $event) }}" class="surface-021">アサインを開く</a>@endif</div>
              <div role="note" class="surface-064">閲覧用の画面です。イベントの編集は社員だけができます。謝金・交通費などの金額は表示しません。</div>
              <div class="layout-120">
                <section aria-labelledby="ev-basic" class="surface-022">
                  <h2 id="ev-basic" class="text-020">イベントの内容</h2>
                  <dl class="layout-121"><dt class="text-125">依頼先</dt><dd class="space-008">{{ $event->client->name }}</dd><dt class="text-125">事業名</dt><dd class="space-008">{{ $event->business_name }}</dd><dt class="text-125">実施日</dt><dd class="space-008">{{ $event->event_date->isoFormat('YYYY/M/D(ddd)') }}</dd><dt class="text-125">時間</dt><dd class="space-008">集合 {{ substr($event->meeting_time, 0, 5) }} ・ 開始 {{ substr($event->start_time, 0, 5) }} ・ 終了 {{ substr($event->end_time, 0, 5) }} ・ 解散 {{ substr($event->dismissal_time, 0, 5) }}<div class="text-002">役割と時間は全員同じ。人によって違うことは「メンバー」の備考に出ます</div>
                  </dd><dt class="text-125">集合場所</dt><dd class="space-008">{{ $event->meeting_place ?: '—' }}</dd><dt class="text-125">実施場所</dt><dd class="space-008">{{ $event->venue ?: '—' }}</dd><dt class="text-125">イベント詳細</dt><dd class="space-008">{{ $event->details ?: '—' }}</dd><dt class="text-125">実施内容</dt><dd class="space-008">{{ $event->work_content ?: '—' }}</dd><dt class="text-125">持ち物</dt><dd class="layout-122">@forelse ($event->items ?? [] as $item)<span class="text-126">{{ ['lunch' => '昼食', 'dinner' => '夕食', 'indoor_shoes' => '室内履き', 'highclass_shirt' => 'ハイクラスTシャツ'][$item] ?? $item }}</span>@empty<span>—</span>@endforelse</dd><dt class="text-125">備考</dt><dd class="space-008">{{ $event->notes ?: '—' }}</dd></dl></section>
                <div class="layout-123">
                  <section aria-labelledby="ev-member" class="surface-022">
                    <div class="layout-021">
                      <h2 id="ev-member" class="text-020">メンバー</h2>
                      <div class="layout-054"><span class="text-007">確定 <b class="text-127"><span>{{ $confirmedAssignments->count() }}</span></b> / <span>{{ $event->required_staff }}</span>人</span>@if ($shortage > 0)<span class="surface-011"><span>あと{{ $shortage }}人</span></span>@endif</div>
                    </div>
                    <ul class="layout-124">@forelse ($confirmedAssignments as $confirmedAssignment)<li class="surface-065"><div class="layout-125"><span class="text-024"><span>{{ $confirmedAssignment->user->name }}</span></span><span class="text-002"><span>{{ $roleLabels[$confirmedAssignment->user->role] ?? $confirmedAssignment->user->role }}</span></span>@if ($confirmedAssignment->is_day_leader)<span class="surface-066">当日責任者</span>@endif @if ($confirmedAssignment->is_report_representative)<span class="surface-067">代表者</span>@endif</div>@if ($confirmedAssignment->special_notes)<div class="text-059">備考:<span>{{ $confirmedAssignment->special_notes }}</span></div>@endif</li>@empty<li class="surface-065">確定メンバーはまだいません。</li>@endforelse</ul>
                    <div class="text-039"><span>回答待ちなど {{ $event->assignments->whereIn('status', ['requested', 'unknown', 'accepted'])->count() }}人(承諾 {{ $event->assignments->where('status', 'accepted')->count() }}・未定 {{ $event->assignments->where('status', 'unknown')->count() }}・依頼中 {{ $event->assignments->where('status', 'requested')->count() }})</span></div>
                    <div class="text-128">「確定」の人だけを表示しています。依頼中・未定・承諾の人や出勤確認の状況は、アサイン画面で確かめられます。</div>
                  </section>
                  <section aria-labelledby="ev-contact" class="surface-022">
                    <h2 id="ev-contact" class="text-020">緊急連絡先・報告書</h2>
                    <dl class="layout-121"><dt class="text-125">緊急連絡先</dt><dd class="space-008">
                    @if (data_get($emergency, 'line') || in_array('ハイクラスLINE', $emergency, true))<div>ハイクラスLINE(公式LINEのトーク)</div>@endif
                    @if ($emergencyEmployee)<div>社員:{{ $emergencyEmployee->name }} {{ $emergencyEmployee->phone }}</div>@endif
                    @if ($leader)<div>当日責任者:{{ $leader->user->name }} {{ $leader->user->phone }}</div>@endif
                    @if (data_get($emergency, 'other'))<div>{{ data_get($emergency, 'other') }}</div>@endif
                    </dd><dt class="text-125">報告書</dt><dd class="space-008">{{ $event->report_required ? '必要(設問'.$event->reportQuestions->count().'問)' : '不要' }}@if ($representative)<div class="text-002">代表者:{{ $representative->user->name }}(解散時間に、提出のお願いが届きます)</div>@endif
                    </dd></dl></section>
                </div>
              </div>
              @else
              <nav aria-label="パンくずリスト" class="text-007"><a href="{{ route('events.index') }}">イベント</a> / 詳細</nav>
              <section class="surface-022"><h1 class="text-044">イベントがありません</h1><p class="text-033">イベントを登録すると、ここに詳細が表示されます。</p></section>
              @endif
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
