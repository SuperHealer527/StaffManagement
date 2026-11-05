<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>イベント登録・編集</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--eventedit" data-static-screen="EventEdit">
    <div class="app-root">
      <div class="screen">
        <div class="layout-126">
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
            <form method="POST" enctype="multipart/form-data" action="{{ $event ? route('events.update', $event) : route('events.store') }}" class="layout-127">
              @csrf
              @if($event) @method('PATCH') @endif
              <div class="text-007"><a href="{{ route('events.index') }}">イベント</a> / {{ $event ? 'イベントの編集' : 'イベントの登録' }}</div>
              <div class="layout-041">
                <h1 class="text-044">{{ $event ? 'イベントの編集' : 'イベントの登録' }}</h1>
                <div class="layout-048">@if ($event)<a href="{{ route('events.copy', $event) }}" class="surface-068">このイベントを複製</a>@if (! in_array($event->status, ['cancelled', 'completed'], true))<button type="button" data-cancel-event-url="{{ route('events.cancel', $event) }}" class="control-038">中止にする</button>@endif @endif<label for="ev-status" class="text-129">ステータス</label><select id="ev-status" name="status" class="surface-069" @disabled($event && in_array($event->status, ['confirmed', 'completed', 'cancelled'], true))><option value="draft" @selected(old('status', $event?->status ?? 'draft') === 'draft')>下書き</option><option value="recruiting" @selected(old('status', $event?->status) === 'recruiting')>募集中</option>@if ($event && ! in_array($event->status, ['draft', 'recruiting'], true))<option value="{{ $event->status }}" selected>{{ ['confirmed' => '確定', 'completed' => '実施済み', 'cancelled' => '中止'][$event->status] }}</option><input type="hidden" name="status" value="{{ $event->status }}">@endif</select></div>
              </div>
              <div class="text-130">選べるのは「下書き」と「募集中」だけです。下書きのあいだは依頼を送れず、人数不足一覧にも出ません。「確定」「実施済み」は自動で変わります。</div>
              <section aria-labelledby="sec-basic" class="layout-049">
                <h2 id="sec-basic" class="text-020">基本情報</h2>
                <div class="layout-050">
                  <div class="layout-051"><label for="ev-client" class="text-042">依頼先<span class="text-050">必須</span></label><select id="ev-client" name="client_id" required class="surface-070">@foreach($clients as $clientOption)<option value="{{ $clientOption->id }}" @selected((string) old('client_id', $event?->client_id) === (string) $clientOption->id)>{{ $clientOption->name }}</option>@endforeach</select><span class="text-002">依頼先一覧に登録した中から選びます。<a href="{{ route('clients.create') }}">新しい依頼先を登録</a></span></div>
                  <div class="layout-051"><label for="ev-biz" class="text-042">事業名<span class="text-050">必須</span></label><input id="ev-biz" name="business_name" type="text" value="{{ old('business_name', $event?->business_name) }}" required maxlength="255" class="text-051"></div>
                </div>
                <div class="layout-051"><label for="ev-name" class="text-042">イベント名</label><input id="ev-name" name="name" type="text" value="{{ old('name', $event?->name) }}" maxlength="255" class="text-051"></div>
                <div class="layout-051"><label for="ev-desc" class="text-042">イベント詳細</label><textarea id="ev-desc" name="details" rows="3" maxlength="5000" class="text-052">{{ old('details', $event?->details) }}</textarea></div>
                <div class="layout-051"><label for="ev-work" class="text-042">実施内容</label><textarea id="ev-work" name="work_content" rows="3" maxlength="5000" class="text-052">{{ old('work_content', $event?->work_content) }}</textarea></div>
              </section>
              <section aria-labelledby="sec-time" class="layout-049">
                <h2 id="sec-time" class="text-020">実施日・時間・必要人数</h2>
                <div class="layout-128"><label class="layout-052"><span>実施日<span class="text-050">必須</span></span><input id="ev-date" name="event_date" type="date" value="{{ old('event_date', $event?->event_date?->format('Y-m-d')) }}" required class="text-131"></label>
                  <div class="text-132">イベントは1日に1件です。2日以上にわたる仕事は、「このイベントを複製」で日付を追加して1日ずつ作ります。</div>
                </div>
                <div class="layout-129"><label class="layout-052"><span>集合時間<span class="text-050">必須</span></span><input name="meeting_time" type="time" value="{{ old('meeting_time', $event ? substr($event->meeting_time, 0, 5) : '08:30') }}" required class="text-051"></label><label class="layout-052"><span>開始時間<span class="text-050">必須</span></span><input name="start_time" type="time" value="{{ old('start_time', $event ? substr($event->start_time, 0, 5) : '09:00') }}" required class="text-051"></label><label class="layout-052"><span>終了時間<span class="text-050">必須</span></span><input name="end_time" type="time" value="{{ old('end_time', $event ? substr($event->end_time, 0, 5) : '17:00') }}" required class="text-051"></label><label class="layout-052"><span>解散時間<span class="text-050">必須</span></span><input name="dismissal_time" type="time" value="{{ old('dismissal_time', $event ? substr($event->dismissal_time, 0, 5) : '17:30') }}" required class="text-051"></label><label class="layout-052"><span>必要人数<span class="text-050">必須</span></span><span class="layout-130"><input name="required_staff" type="number" min="1" max="100" value="{{ old('required_staff', $event?->required_staff ?? 1) }}" required class="text-133">人</span></label></div>
                <div class="surface-071">謝金はイベントには入れません。依頼するときにアサイン画面で1人ずつ決めます(スタッフ詳細の「基本の謝金」が入り、その場で直せます)。役割と時間は全員このイベントと同じです。リーダーを頼む・集合を早めるなど、人によって違うことは、アサイン画面でその人の備考に書きます(依頼・確定の連絡に載ります)。</div>
              </section>
              <section aria-labelledby="sec-place" class="layout-049">
                <h2 id="sec-place" class="text-020">場所・持ち物・緊急連絡先・備考</h2>
                <div class="layout-050">
                  <div class="layout-051"><label for="ev-meet" class="text-042">集合場所</label><input id="ev-meet" name="meeting_place" type="text" value="{{ old('meeting_place', $event?->meeting_place) }}" maxlength="255" class="text-051"></div>
                  <div class="layout-051"><label for="ev-venue" class="text-042">実施場所</label><input id="ev-venue" name="venue" type="text" value="{{ old('venue', $event?->venue) }}" maxlength="255" class="text-051"></div>
                </div>
                @php
                  $savedItems = collect(old('items', $event?->items ?? []));
                  $itemAliases = ['lunch' => '昼食', 'dinner' => '夕食', 'indoor_shoes' => '室内履き', 'highclass_shirt' => 'ハイクラスTシャツ'];
                  $hasItem = fn ($key) => $savedItems->contains($key) || $savedItems->contains($itemAliases[$key] ?? $key);
                  $savedOtherItem = old('other_item', $savedItems->first(fn ($item) => str_starts_with($item, 'other:')) ? str($savedItems->first(fn ($item) => str_starts_with($item, 'other:')))->after('other:')->toString() : $savedItems->first(fn ($item) => ! in_array($item, array_merge(array_keys($itemAliases), array_values($itemAliases)), true)));
                @endphp
                <fieldset class="space-007"><legend class="text-134">持ち物(複数選択)</legend>
                  <div class="layout-131">@foreach (['lunch' => '昼食', 'dinner' => '夕食', 'indoor_shoes' => '室内履き', 'highclass_shirt' => 'ハイクラスTシャツ'] as $value => $label)<label class="control-039"><input name="items[]" value="{{ $value }}" type="checkbox" @checked($hasItem($value)) class="space-006">{{ $label }}</label>@endforeach<label class="control-039"><input name="items[]" value="other" type="checkbox" @checked(filled($savedOtherItem) || $savedItems->contains('other')) class="space-006">その他</label></div>
                  <div class="space-016"><label for="ev-other" class="space-004">その他の持ち物</label><input id="ev-other" name="other_item" type="text" placeholder="その他の持ち物を入力" value="{{ $savedOtherItem }}" maxlength="255" class="text-135"></div>
                </fieldset>
                @php $emergency = old('emergency_contacts', $event?->emergency_contacts ?? []); $lineEmergency = data_get($emergency, 'line', in_array('ハイクラスLINE', $emergency, true)); $leaderEmergency = data_get($emergency, 'leader', in_array('当日責任者', $emergency, true)); @endphp
                <fieldset class="space-007"><legend class="text-134">緊急連絡先(複数選択)</legend>
                  <div class="layout-132"><input type="hidden" name="emergency_contacts[line]" value="0"><label class="control-040"><input name="emergency_contacts[line]" value="1" type="checkbox" @checked($lineEmergency) class="space-006">ハイクラスLINE</label><span class="text-007">公式LINEのトークで連絡してもらう</span><label class="control-040">社員</label><select name="emergency_contacts[employee_id]" aria-label="緊急連絡先の社員" class="surface-072"><option value="">選択しない</option>@foreach ($employees ?? [] as $employee)<option value="{{ $employee->id }}" @selected((string) data_get($emergency, 'employee_id') === (string) $employee->id)>{{ $employee->name }}({{ $employee->phone ?: '電話未登録' }})</option>@endforeach</select><input type="hidden" name="emergency_contacts[leader]" value="0"><label class="control-040"><input name="emergency_contacts[leader]" value="1" type="checkbox" @checked($leaderEmergency) class="space-006">当日責任者</label><span class="text-053">アサイン画面で決めた人。<a href="{{ route('screens.show', ['screen' => 'assignments', 'event_id' => $event?->id]) }}">アサイン画面で変える</a></span><label class="control-040">その他</label><input name="emergency_contacts[other]" value="{{ data_get($emergency, 'other') }}" type="text" aria-label="その他の緊急連絡先" placeholder="連絡先の名前と電話番号" maxlength="255" class="text-136"></div>
                  <div class="text-054">選んだ連絡先は、スタッフの出勤確認の画面とホームに表示されます。</div>
                </fieldset>
                <div class="layout-051"><label for="ev-note" class="text-042">備考</label><textarea id="ev-note" name="notes" rows="2" placeholder="スタッフに伝えたいことなど" maxlength="5000" class="text-052">{{ old('notes', $event?->notes) }}</textarea></div>
              </section>
              <section aria-labelledby="sec-report" class="layout-049">
                <h2 id="sec-report" class="text-020">報告書</h2>
                <fieldset class="space-007"><legend class="text-134">報告書の提出</legend>
                  <div class="layout-133"><label class="control-039"><input type="radio" name="report_required" value="1" @checked((bool) old('report_required', $event?->report_required ?? true)) class="space-006">必要</label><label class="control-039"><input type="radio" name="report_required" value="0" @checked(! (bool) old('report_required', $event?->report_required ?? true)) class="space-006">不要</label></div>
                </fieldset>
                <div class="layout-053">
                  <div class="surface-073">報告書は代表者1人が提出します。代表者は、アサイン画面で確定の人の中から選びます(最初は当日責任者と同じ人が入ります)。</div>
                  @php
                    $questions = $event?->reportQuestions ?? collect([
                      (object) ['id' => null, 'type' => 'select', 'question' => '本日の事業は、無事終了しましたか?', 'options' => ['問題なく終了した', '問題なく終了したが、報告事項あり', 'トラブル等あり']],
                      (object) ['id' => null, 'type' => 'text', 'question' => '依頼先への報告用の文章は下記に記載してください。', 'options' => []],
                      (object) ['id' => null, 'type' => 'text', 'question' => 'ハイクラスへの報告用文章を作成してください。', 'options' => []],
                    ]);
                  @endphp
                  <div class="layout-134">
                    <h3 class="text-137">設問(<span data-question-count>{{ $questions->count() }}</span>問)</h3>
                    <span class="text-002">新しいイベントには標準の3問が最初から入ります。イベントごとに変更できます。</span></div>
                  <div data-question-rows>
                  @foreach ($questions as $question)
                    <div class="layout-135" data-question-row>
                      @if ($question->id)<input type="hidden" name="report_questions[{{ $loop->index }}][id]" value="{{ $question->id }}">@endif
                      <input type="hidden" name="report_questions[{{ $loop->index }}][type]" value="{{ $question->type }}">
                      <div class="layout-136"><span class="text-040">設問{{ $loop->iteration }}</span><div class="layout-054"><span class="{{ $question->type === 'select' ? 'surface-018' : 'surface-019' }}">{{ $question->type === 'select' ? '選択形式' : '記述形式' }}</span><button type="button" data-remove-question class="control-001" aria-label="設問を削除">削除</button></div></div>
                      <input name="report_questions[{{ $loop->index }}][question]" type="text" value="{{ $question->question }}" required maxlength="1000" class="text-051">
                      @if ($question->type === 'select')<div class="layout-137" data-option-rows><span class="text-002">選択肢</span>@foreach ($question->options ?? [] as $option)<input name="report_questions[{{ $loop->parent->index }}][options][]" type="text" value="{{ $option }}" required maxlength="255" class="text-138">@endforeach<button type="button" data-add-option class="control-041">+ 選択肢を追加</button></div>@else<div class="layout-138">回答は自由に書いてもらう欄になります</div>@endif
                    </div>
                  @endforeach
                  </div>
                  <template id="question-template-select"><div class="layout-135" data-question-row><input type="hidden" name="report_questions[__INDEX__][type]" value="select"><div class="layout-136"><span class="text-040">設問</span><div class="layout-054"><span class="surface-018">選択形式</span><button type="button" data-remove-question class="control-001">削除</button></div></div><input name="report_questions[__INDEX__][question]" type="text" required maxlength="1000" class="text-051"><div class="layout-137" data-option-rows><span class="text-002">選択肢</span><input name="report_questions[__INDEX__][options][]" type="text" required maxlength="255" class="text-138"><button type="button" data-add-option class="control-041">+ 選択肢を追加</button></div></div></template>
                  <template id="question-template-text"><div class="layout-135" data-question-row><input type="hidden" name="report_questions[__INDEX__][type]" value="text"><div class="layout-136"><span class="text-040">設問</span><div class="layout-054"><span class="surface-019">記述形式</span><button type="button" data-remove-question class="control-001">削除</button></div></div><input name="report_questions[__INDEX__][question]" type="text" required maxlength="1000" class="text-051"><div class="layout-138">回答は自由に書いてもらう欄になります</div></div></template>
                  @if (false)
                  <div class="layout-135">
                    <div class="layout-136"><span class="text-040">設問1</span>
                      <div class="layout-054"><span class="surface-018">選択形式</span><button type="button" aria-label="設問1を削除" class="control-001"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"></path></svg></button></div>
                    </div>
                    <input type="text" aria-label="設問1の文章" value="本日の事業は、無事終了しましたか?" class="text-051">
                    <div class="layout-137"><span class="text-002">選択肢</span><input type="text" aria-label="選択肢1" value="問題なく終了した" class="text-138"><input type="text" aria-label="選択肢2" value="問題なく終了したが、報告事項あり" class="text-138"><input type="text" aria-label="選択肢3" value="トラブル等あり" class="text-138"><button type="button" class="control-041">+ 選択肢を追加</button></div>
                  </div>
                  <div class="layout-135">
                    <div class="layout-136"><span class="text-040">設問2</span>
                      <div class="layout-054"><span class="surface-019">記述形式</span><button type="button" aria-label="設問2を削除" class="control-001"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"></path></svg></button></div>
                    </div>
                    <input type="text" aria-label="設問2の文章" value="依頼先への報告用の文章は下記に記載してください。" class="text-051">
                    <div class="layout-138">回答は自由に書いてもらう欄になります</div>
                  </div>
                  <div class="layout-135">
                    <div class="layout-136"><span class="text-040">設問3</span>
                      <div class="layout-054"><span class="surface-019">記述形式</span><button type="button" aria-label="設問3を削除" class="control-001"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"></path></svg></button></div>
                    </div>
                    <input type="text" aria-label="設問3の文章" value="ハイクラスへの報告用文章を作成してください。" class="text-051">
                    <div class="layout-138">回答は自由に書いてもらう欄になります</div>
                  </div>
                  @endif
                  <div class="layout-024"><button type="button" data-add-question="select" class="control-002">+ 選択形式の設問</button><button type="button" data-add-question="text" class="control-002">+ 記述形式の設問</button></div>
                </div>
              </section>
              <section aria-labelledby="sec-money" class="layout-139">
                <div class="layout-136">
                  <h2 id="sec-money" class="text-020">請求と収支</h2>
                  <span class="surface-074">社員だけに表示</span></div>
                <div class="text-130">依頼先に請求する額と、アサイン以外にかかった経費を入れると、利益を自動で計算します。金額はすべて税抜です。請求書はこれまでどおり会計ソフトで作ります。</div>
                <div class="layout-140">
                  @php $invoiceRows = $event?->invoiceItems ?? collect(); $invoiceTotal = $invoiceRows->sum('amount'); $expenseRows = $event?->expenses ?? collect(); $expenseTotal = $expenseRows->sum('amount'); @endphp
                  <div class="layout-141">
                    <h3 class="text-137">請求の内訳</h3>
                    <span class="text-053">合計<strong class="text-139"><span data-invoice-total>¥{{ number_format($invoiceTotal) }}</span></strong></span></div>
                  <div aria-hidden="true" class="layout-142"><span>項目</span><span>メモ</span><span class="utility-012">金額(円)</span><span></span></div>
                  <div data-invoice-rows>
                  @foreach ($invoiceRows as $invoiceItem)
                    <div class="layout-143" data-money-row>@if ($invoiceItem->id)<input type="hidden" name="invoice_items[{{ $loop->index }}][id]" value="{{ $invoiceItem->id }}">@endif<select name="invoice_items[{{ $loop->index }}][type]" class="surface-017">@foreach (['work' => '作業費', 'transportation' => '交通費', 'fuel' => '燃料費', 'other' => 'その他'] as $value => $label)<option value="{{ $value }}" @selected($invoiceItem->type === $value || ($invoiceItem->type === 'work_fee' && $value === 'work'))>{{ $label }}</option>@endforeach</select><input name="invoice_items[{{ $loop->index }}][memo]" type="text" maxlength="255" value="{{ $invoiceItem->memo }}" class="text-140"><input name="invoice_items[{{ $loop->index }}][amount]" type="number" min="0" max="100000000" value="{{ $invoiceItem->amount }}" required data-money-amount class="text-141"><button type="button" data-remove-money-row class="control-042">削除</button></div>
                  @endforeach
                  </div>
                  <template id="invoice-row-template"><div class="layout-143" data-money-row><select name="invoice_items[__INDEX__][type]" class="surface-017"><option value="work">作業費</option><option value="transportation">交通費</option><option value="fuel">燃料費</option><option value="other">その他</option></select><input name="invoice_items[__INDEX__][memo]" type="text" maxlength="255" class="text-140"><input name="invoice_items[__INDEX__][amount]" type="number" min="0" max="100000000" value="0" required data-money-amount class="text-141"><button type="button" data-remove-money-row class="control-042">削除</button></div></template>
                  @if (false)
                  <div class="layout-143"><select aria-label="請求1の項目" class="surface-017"><option>作業費</option><option>交通費</option><option>燃料費</option><option>その他</option></select><input type="text" aria-label="請求1のメモ" placeholder="例:リーダー1人・受付4人" value="リーダー1人・受付4人" class="text-140"><input type="number" min="0" aria-label="請求1の金額" value="72000" class="text-141"><button type="button" aria-label="請求1を削除" class="control-042"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"></path></svg></button></div>
                  <div class="layout-143"><select aria-label="請求2の項目" class="surface-017"><option>作業費</option><option>交通費</option><option>燃料費</option><option>その他</option></select><input type="text" aria-label="請求2のメモ" placeholder="例:リーダー1人・受付4人" value="スタッフの交通費" class="text-140"><input type="number" min="0" aria-label="請求2の金額" value="3600" class="text-141"><button type="button" aria-label="請求2を削除" class="control-042"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"></path></svg></button></div>
                  <div class="layout-143"><select aria-label="請求3の項目" class="surface-017"><option>作業費</option><option>交通費</option><option>燃料費</option><option>その他</option></select><input type="text" aria-label="請求3のメモ" placeholder="例:リーダー1人・受付4人" value="備品の運搬(社用車)" class="text-140"><input type="number" min="0" aria-label="請求3の金額" value="2400" class="text-141"><button type="button" aria-label="請求3を削除" class="control-042"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"></path></svg></button></div>
                  @endif
                  <button type="button" data-add-invoice class="control-043">+ 行を追加</button></div>
                <div class="layout-144">
                  <div class="layout-141">
                    <h3 class="text-137">経費<span class="text-055">アサイン(謝金・交通費・立替)以外にかかった費用</span></h3>
                    <span class="text-053">合計<strong class="text-139"><span data-expense-total>¥{{ number_format($expenseTotal) }}</span></strong></span></div>
                  <div aria-hidden="true" class="layout-145"><span>種類</span><span>メモ</span><span>領収書</span><span class="utility-012">金額(円)</span><span></span></div>
                  <div data-expense-rows>
                  @foreach ($expenseRows as $expense)
                    <div class="layout-146" data-money-row>@if ($expense->id)<input type="hidden" name="expenses[{{ $loop->index }}][id]" value="{{ $expense->id }}">@endif<select name="expenses[{{ $loop->index }}][type]" class="surface-017">@foreach (['fuel' => '燃料費', 'transportation' => '運送費', 'supplies' => '消耗品', 'other' => 'その他'] as $value => $label)<option value="{{ $value }}" @selected($expense->type === $value)>{{ $label }}</option>@endforeach</select><input name="expenses[{{ $loop->index }}][memo]" type="text" maxlength="255" value="{{ $expense->memo }}" class="text-140"><div class="layout-147">@if ($expense->receipt_path)<a href="{{ route('events.expenses.receipt', [$event, $expense]) }}" class="text-142">写真を見る</a>@endif<label class="control-044">{{ $expense->receipt_path ? '変更' : '+ 写真を付ける' }}<input name="expenses[{{ $loop->index }}][receipt]" type="file" accept="image/jpeg,image/png,application/pdf" hidden></label></div><input name="expenses[{{ $loop->index }}][amount]" type="number" min="0" max="100000000" value="{{ $expense->amount }}" required data-money-amount class="text-141"><button type="button" data-remove-money-row class="control-042">削除</button></div>
                  @endforeach
                  </div>
                  <template id="expense-row-template"><div class="layout-146" data-money-row><select name="expenses[__INDEX__][type]" class="surface-017"><option value="fuel">燃料費</option><option value="transportation">運送費</option><option value="supplies">消耗品</option><option value="other">その他</option></select><input name="expenses[__INDEX__][memo]" type="text" maxlength="255" class="text-140"><div class="layout-147"><label class="control-044">+ 写真を付ける<input name="expenses[__INDEX__][receipt]" type="file" accept="image/jpeg,image/png,application/pdf" hidden></label></div><input name="expenses[__INDEX__][amount]" type="number" min="0" max="100000000" value="0" required data-money-amount class="text-141"><button type="button" data-remove-money-row class="control-042">削除</button></div></template>
                  @if (false)
                  <div class="layout-146"><select aria-label="経費1の種類" class="surface-017"><option>燃料費</option><option>運送費</option><option>消耗品</option><option>その他</option></select><input type="text" aria-label="経費1のメモ" placeholder="例:社用車で備品を運搬" value="社用車で備品を運搬(往復)" class="text-140">
                    <div class="layout-147"><button type="button" class="control-044">+ 写真を付ける</button></div>
                    <input type="number" min="0" aria-label="経費1の金額" value="2400" class="text-141"><button type="button" aria-label="経費1を削除" class="control-042"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"></path></svg></button></div>
                  <div class="layout-146"><select aria-label="経費2の種類" class="surface-017"><option>燃料費</option><option>運送費</option><option>消耗品</option><option>その他</option></select><input type="text" aria-label="経費2のメモ" placeholder="例:社用車で備品を運搬" value="受付用品(名札・筆記用具)" class="text-140">
                    <div class="layout-147"><a href="#" class="text-142"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.5l-8.5 8.5a5 5 0 0 1-7-7l9-9a3.3 3.3 0 0 1 4.7 4.7l-9 9a1.7 1.7 0 0 1-2.4-2.4l8.3-8.3"></path></svg>写真を見る</a></div>
                    <input type="number" min="0" aria-label="経費2の金額" value="1800" class="text-141"><button type="button" aria-label="経費2を削除" class="control-042"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"></path></svg></button></div>
                  @endif
                  <button type="button" data-add-expense class="control-043">+ 行を追加</button></div>
                <div class="layout-148">
                  @php $eligibleAssignments = $event?->assignments?->whereIn('status', ['confirmed', 'completed']) ?? collect(); $feeCost = $eligibleAssignments->sum('fee'); $transportCost = $eligibleAssignments->sum('transportation'); $reimbursementCost = $eligibleAssignments->sum('reimbursement'); $totalCost = $feeCost + $transportCost + $reimbursementCost + $expenseTotal; $profit = $invoiceTotal - $totalCost; $margin = $invoiceTotal > 0 ? ($profit / $invoiceTotal) * 100 : 0; @endphp
                  <div class="layout-016">
                    <h3 class="text-137">収支</h3>
                    <a href="Profit.dc.html" class="text-143">収支一覧で月ごとに見る ›</a></div>
                  <div class="layout-149">
                    <div class="surface-075">
                      <div class="text-002">請求の合計</div>
                      <div class="text-030"><span data-profit-invoice>¥{{ number_format($invoiceTotal) }}</span></div>
                    </div>
                    <div class="surface-075">
                      <div class="text-002">原価</div>
                      <div class="text-030"><span data-profit-cost data-base-cost="{{ $feeCost + $transportCost + $reimbursementCost }}">¥{{ number_format($totalCost) }}</span></div>
                      <div class="text-144">謝金 <span>¥{{ number_format($feeCost) }}</span> ・ 交通費 <span>¥{{ number_format($transportCost) }}</span><br>立替 <span>¥{{ number_format($reimbursementCost) }}</span> ・ 経費 <span data-profit-expense>¥{{ number_format($expenseTotal) }}</span></div>
                    </div>
                    <div class="surface-076">
                      <div class="text-002">利益</div>
                      <div class="text-145"><span data-profit-value>¥{{ number_format($profit) }}</span></div>
                      <div class="text-146">利益率 <strong class="utility-013"><span data-profit-margin>{{ number_format($margin, 1) }}%</span></strong></div>
                    </div>
                  </div>
                  <div class="text-056">謝金・交通費・立替は、このイベントの「確定」「実施済み」のアサインの合計です(支払管理と同じ金額)。源泉税は謝金から預かって納めるものなので、原価からは引きません。いまは必要人数5人のうち確定が2人なので、残りの人が確定すると原価が増え、利益は下がります。</div>
                </div>
              </section>
              @if ($errors->any())<div role="alert" class="text-050">{{ $errors->first() }}</div>@endif
              @if (session('status'))<div role="status" class="text-060">{{ session('status') }}</div>@endif
              <div class="layout-150"><a href="{{ route('events.index') }}" class="text-057">キャンセル</a><button type="submit" class="control-003">保存する</button></div>
            </form>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
