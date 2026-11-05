<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>報告書の提出</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--report" data-static-screen="Report">
    <div class="app-root">
      <div class="screen">
        <div class="layout-191">
          <header class="layout-030">
            <div class="text-035">ハイクラス業務管理システム</div>
            <div class="text-002">{{ ($user ?? auth()->user())?->name ?? '鈴木 一郎' }} さん</div>
          </header>
          <main class="layout-057">
            @php $assignment = $assignment ?? null; $event = $event ?? null; $user = $user ?? auth()->user(); $reportSubmission = $reportSubmission ?? null; @endphp
            <div><span class="surface-096">あなたが代表者です</span>
              <h1 class="text-197">報告書の提出</h1>
              <div class="text-062">{{ $event?->name ?: ($event?->business_name ?? '秋季健康フェア') }}</div>
              <div class="text-037">{{ $event?->event_date?->isoFormat('M月D日(ddd)') ?? '10月21日(水)' }} ・ {{ $event?->client?->name ?? 'サンプル商事' }} / {{ $event?->business_name ?? '地域イベント運営' }}</div>
            </div>
            <form method="POST" action="{{ isset($authenticatedReport) ? route('my.reports.store', $assignment) : (isset($actionToken) ? route('public-actions.update', request()->route('token')) : '#') }}" class="layout-053">
              @csrf
              <fieldset class="surface-097"><legend class="text-198"><span class="text-199">設問1</span>本日の事業は、無事終了しましたか?</legend>
                <div class="layout-192"><label class="control-064"><input type="radio" name="completion" value="completed" @checked(data_get($reportSubmission?->answers, 'completion', 'completed') === 'completed') class="space-009">問題なく終了した</label><label class="control-064"><input type="radio" name="completion" value="issue" @checked(data_get($reportSubmission?->answers, 'completion') === 'issue') class="space-009">問題なく終了したが、報告事項あり</label><label class="control-064"><input type="radio" name="completion" value="incident" @checked(data_get($reportSubmission?->answers, 'completion') === 'incident') class="space-009">トラブル等あり</label></div>
              </fieldset>
              @forelse($event?->reportQuestions ?? [] as $question)
              <div class="layout-193"><label for="q{{ $question->id }}" class="text-200"><span class="text-199">設問{{ $loop->iteration + 1 }}</span>{{ $question->question }}</label>@if($question->type === 'select')<div class="layout-192">@foreach($question->options ?? [] as $option)<label class="control-064"><input id="q{{ $question->id }}" type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" @checked(data_get($reportSubmission?->answers, (string) $question->id) === $option) class="space-009" required>{{ $option }}</label>@endforeach</div>@else<textarea id="q{{ $question->id }}" name="answers[{{ $question->id }}]" rows="5" class="text-052">{{ data_get($reportSubmission?->answers, (string) $question->id) }}</textarea>@endif</div>
              @empty
              <div class="layout-193"><label for="q2" class="text-200"><span class="text-199">設問2</span>依頼先への報告用の文章は下記に記載してください。</label><textarea id="q2" name="answers[client_report]" rows="5" class="text-052">{{ data_get($reportSubmission?->answers, 'client_report') }}</textarea></div><div class="layout-193"><label for="q3" class="text-200"><span class="text-199">設問3</span>ハイクラスへの報告用文章を作成してください。</label><textarea id="q3" name="answers[internal_report]" rows="5" class="text-052">{{ data_get($reportSubmission?->answers, 'internal_report') }}</textarea></div>
              @endforelse
              <button type="submit" class="control-004">報告書を提出する</button>
              <div class="text-201">書きかけの内容は自動で保存されます</div>
            </form>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
