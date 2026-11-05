<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>イベントの複製</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--duplicate" data-static-screen="Duplicate">
    <div class="app-root">
      <div class="screen">
        <div class="layout-113">
          <form method="POST" action="{{ isset($event) ? route('events.duplicate', $event) : '#' }}" role="dialog" aria-labelledby="dup-title" class="layout-114" data-duplicate-form>
            @csrf
            <div>
              <h1 id="dup-title" class="text-036">イベントを複製</h1>
              <div class="text-121">複製元:{{ $event?->client?->name ?? 'サンプル商事' }} / {{ $event?->business_name ?? '地域イベント運営' }} / {{ $event?->name ?: '秋季健康フェア' }}({{ $event?->event_date?->format('n/j') ?? '10/21' }})</div>
            </div>
            <div>
              <div class="text-017">作成する日付</div>
              <div class="text-033">日付を追加した数だけイベントが作られます(1日に1件。複数日の仕事も1日ずつ作ります)</div>
              <div class="layout-115" data-duplicate-dates></div>
              <div class="layout-116"><label for="dup-date" class="space-004">追加する日付</label><input id="dup-date" type="date" value="{{ $event?->event_date?->copy()->addWeek()->format('Y-m-d') ?? today()->addWeek()->format('Y-m-d') }}" min="{{ today()->format('Y-m-d') }}" class="text-122"><button type="button" data-add-duplicate-date class="control-033">日付を追加</button><button type="button" data-add-weekly-dates class="control-034">毎週同じ曜日で追加…</button></div>
            </div>
            <fieldset class="space-014"><legend class="text-123">引き継ぐ内容</legend>
              <div class="layout-117"><label class="layout-118"><input type="checkbox" disabled="" checked="" class="space-015">依頼先・事業名(必ず引き継ぐ)</label><label class="control-035"><input name="copy_name" value="1" type="checkbox" checked class="space-006">イベント名</label><label class="control-035"><input name="copy_schedule" value="1" type="checkbox" checked class="space-006">時間(集合〜解散)・必要人数</label><label class="control-035"><input name="copy_location" value="1" type="checkbox" checked class="space-006">場所・実施内容・持ち物・緊急連絡先</label><label class="control-035"><input name="copy_details" value="1" type="checkbox" checked class="space-006">イベント詳細・備考</label><label class="control-035"><input name="copy_report" value="1" type="checkbox" checked class="space-006">報告書の要否・設問</label></div>
              <div class="text-058">アサイン(依頼・確定・当日責任者・代表者)と、一人ひとりの謝金・交通費、出勤確認、報告書の回答は引き継ぎません。</div>
            </fieldset>
            <div class="layout-055"><label for="dup-status" class="text-017">作成後のステータス</label><select id="dup-status" name="status" class="surface-063"><option value="draft">下書き</option><option value="recruiting">募集中</option></select>
              <div class="text-002">下書きのイベントには依頼を送れません。依頼を始めるときに募集中にします。</div>
            </div>
            @if ($errors->any())<div role="alert" class="text-050">{{ $errors->first() }}</div>@endif
            <div class="layout-119"><a href="{{ isset($event) ? route('events.edit', $event) : route('events.index') }}" class="control-036">キャンセル</a><button type="submit" class="control-037"><span data-duplicate-count>0</span>件のイベントを作成</button></div>
          </form>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
