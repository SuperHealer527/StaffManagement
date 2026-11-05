@extends('layouts.app')
@section('title', '社員のホーム')
@section('content')
<div class="flex flex-wrap items-end justify-between gap-4"><div><p class="eyebrow">2026年11月5日（木）</p><h1 class="page-title">社員のホーム</h1></div><a href="{{ route('screens.show','event-form') }}" class="primary-button">＋ イベントを登録</a></div>
<section class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ([['2週間以内のイベント',$events->count().' 件','calendar'],['4週間以内のイベント','21 件','calendar'],['人数不足のイベント',$shortageCount.' 件','alert'],['支払待ち','合計 ¥29,702','money']] as [$label,$value,$type])
        <article class="metric-card"><span class="metric-icon">{{ $type === 'alert' ? '!' : ($type === 'money' ? '¥' : '□') }}</span><p>{{ $label }}</p><strong>{{ $value }}</strong></article>
    @endforeach
</section>
<section class="panel mt-6"><div class="panel-heading"><div><h2>お知らせ</h2><p>急ぐものから最大3件。対応すると消えます</p></div></div><div class="divide-y divide-[#edf0f3]">
    <a class="notice-row" href="{{ route('screens.show','notifications') }}"><span class="status-dot bg-[#d45742]"></span><div><strong>送れなかった連絡が2件あります</strong><p>送信履歴で確かめる ›</p></div></a>
    <a class="notice-row" href="{{ route('screens.show','phone-log') }}"><span class="status-dot bg-[#e5a445]"></span><div><strong>あなたあての電話の伝言が1件あります</strong><p>電話記録簿を開く ›</p></div></a>
    <a class="notice-row" href="{{ route('screens.show','assignments') }}"><span class="status-dot bg-[#e5a445]"></span><div><strong>3日以上回答が来ていない依頼が{{ $pendingCount }}件あります</strong><p>一覧を見る ›</p></div></a>
</div></section>
<div class="mt-6 grid gap-6 xl:grid-cols-2"><section class="panel"><div class="panel-heading"><div><h2>人数不足のイベント</h2><p>2週間以内</p></div><a href="{{ route('screens.show','shortages') }}">すべて見る</a></div><div class="table-wrap"><table><thead><tr><th>日付</th><th>イベント / 依頼先</th><th>確定 / 必要</th></tr></thead><tbody>@foreach($events->take(4) as $event)<tr><td>{{ $event->event_date->format('m/d') }}</td><td><strong>{{ $event->name }}</strong><small>{{ $event->client->name }}</small></td><td>{{ $event->confirmed_count }} / {{ $event->required_staff }}人</td></tr>@endforeach</tbody></table></div></section>
<section class="panel"><div class="panel-heading"><div><h2>あなたの予定</h2><p>今後の確定イベント</p></div></div><div class="grid gap-3 p-5">@foreach($events->take(3) as $event)<div class="schedule-card"><time>{{ $event->event_date->format('m/d') }}</time><div><strong>{{ $event->name }}</strong><p>集合 {{ substr($event->meeting_time,0,5) }} ・ {{ $event->meeting_place }}</p></div></div>@endforeach</div></section></div>
@endsection
