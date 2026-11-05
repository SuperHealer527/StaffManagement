<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>面談の日程を選ぶ</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--interviewschedule" data-static-screen="InterviewSchedule">
    <div class="app-root">
      <div class="screen">
        <div class="layout-073">
          <header class="cell-025">
            <div class="text-002">ハイクラス業務管理システム</div>
            <h1 class="text-079">面談の日程を選んでください</h1>
          </header>
          <main class="layout-074">
            <form method="POST" action="{{ isset($actionToken) ? route('public-actions.update', request()->route('token')) : '#' }}" class="layout-053" data-interview-form>
              @csrf
              <section class="surface-010">
                <div class="text-007">担当</div>
                <div class="text-035">山田 太郎</div>
                <div class="text-150">種別</div>
                <div class="text-151">個別指導(30分〜1時間)</div>
                <div class="text-150">場所</div>
                <div class="text-151">ハイクラス 事務所(対面)</div>
              </section>
              @php($candidateTimes = isset($interview) && $interview ? $interview->candidate_times : ['2026-10-28 18:00:00', '2026-10-29 18:00:00', '2026-10-31 10:00:00'])
              <input type="hidden" name="selected_at" value="{{ $candidateTimes[0] ?? '' }}">
              <fieldset class="layout-153"><legend class="text-152">都合のよい日時</legend><button type="button" data-interview-value="{{ $candidateTimes[0] ?? '' }}" aria-pressed="true" class="control-045"><span>10/28(水) 18:00〜19:00</span></button><button type="button" data-interview-value="{{ $candidateTimes[1] ?? ($candidateTimes[0] ?? '') }}" aria-pressed="false" class="control-046"><span>10/29(木) 18:00〜19:00</span></button><button type="button" data-interview-value="{{ $candidateTimes[2] ?? ($candidateTimes[0] ?? '') }}" aria-pressed="false" class="control-046"><span>10/31(土) 10:00〜11:00</span></button><button type="button" data-interview-value="{{ $candidateTimes[0] ?? '' }}" aria-pressed="false" class="control-046"><span>どれも都合が悪い</span></button></fieldset>
              <button type="submit" class="control-013">この内容で送る</button>
              <div class="text-002">このページはLINEのメッセージから開いています。ログインは不要です。</div>
            </form>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
