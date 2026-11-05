@extends('layouts.app')
@section('title', $user->role === 'staff' ? 'スタッフのホーム' : ($user->role === 'semi_employee' ? '準社員のホーム' : '社員のホーム'))
@section('content')
@if(in_array($user->role, ['staff', 'trainer'], true))
    <div class="mobile-card">
        <header class="mobile-page-head">
            <p>{{ $user->name }}さん</p>
            <h1>ホーム</h1>
        </header>
        <div class="mobile-card-body">
            @php($unanswered = $myAssignments->where('status', 'requested'))
            <section>
                <div class="flex items-center justify-between"><h2 class="m-0 text-[16px]">未回答の依頼</h2><span class="badge badge-orange">{{ $unanswered->count() }}件</span></div>
                <div class="mt-3 grid gap-3">
                    @forelse($unanswered as $assignment)
                        <article class="mobile-event">
                            <div><span class="badge badge-orange">回答してください</span><h2 class="mt-2">{{ $assignment->event->name }}</h2></div>
                            <p>{{ $assignment->event->event_date->isoFormat('M月D日（ddd）') }}　集合 {{ substr($assignment->event->meeting_time, 0, 5) }}<br>{{ $assignment->event->meeting_place }}</p>
                            <a class="primary-button" href="{{ route('screens.show', 'assignment-response') }}">依頼を確認して回答する</a>
                        </article>
                    @empty
                        <div class="mobile-event"><p>現在、回答が必要な依頼はありません。</p></div>
                    @endforelse
                </div>
            </section>
            <section class="panel">
                <div class="panel-heading"><h2>出勤確認</h2><span class="badge badge-blue">当日</span></div>
                <div class="panel-body text-sm leading-7">当日の集合2時間前に確認ボタンが表示されます。安全にお越しください。</div>
            </section>
            <section class="panel">
                <div class="panel-heading"><h2>報告書の提出</h2><a href="{{ route('screens.show', 'report-submit') }}">開く ›</a></div>
                <div class="panel-body text-sm text-[#5b6270]">担当したイベントの終了後、代表者は報告書を提出してください。</div>
            </section>
            <section>
                <h2 class="m-0 text-[16px]">今後の予定</h2>
                <div class="mt-3 grid gap-3">
                    @foreach($myAssignments->where('status', 'confirmed')->take(4) as $assignment)
                        <article class="mobile-event">
                            <div class="flex items-start justify-between gap-2"><h2>{{ $assignment->event->name }}</h2><span class="badge badge-green">確定</span></div>
                            <p>{{ $assignment->event->event_date->isoFormat('M月D日（ddd）') }}　{{ substr($assignment->event->start_time, 0, 5) }}〜{{ substr($assignment->event->end_time, 0, 5) }}<br>{{ $assignment->event->venue }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@else
    <div class="page-header">
        <div><h1>ホーム</h1><p>{{ now()->isoFormat('YYYY年M月D日（ddd）') }}</p></div>
        @if($user->isEmployee())<a href="{{ route('screens.show', 'event-form') }}" class="primary-button">＋ イベントを登録</a>@endif
    </div>

    <section class="metrics" aria-label="主な数字">
        <article class="metric-card"><p>2週間以内のイベント <small>今日から14日</small></p><strong>{{ $twoWeekCount }}<small>件</small></strong></article>
        <article class="metric-card"><p>4週間以内のイベント <small>今日から28日</small></p><strong>{{ $fourWeekCount }}<small>件</small></strong></article>
        <article class="metric-card warning"><p>人数不足のイベント <small>2週間以内</small></p><strong>{{ $shortages->count() }}<small>件</small></strong></article>
        @if($user->isEmployee())
            <article class="metric-card"><p>支払待ち <span class="badge">社員のみ</span></p><strong>{{ $pendingPaymentCount }}<small>件</small></strong><small>合計 ¥{{ number_format($pendingPaymentTotal) }}</small></article>
        @else
            <article class="metric-card"><p>回答が来ていない依頼</p><strong>{{ $pendingCount }}<small>件</small></strong><small>準社員は金額を閲覧できません</small></article>
        @endif
    </section>

    <section class="panel mt-6">
        <div class="panel-heading"><div><h2>お知らせ</h2><p>急ぐものから最大3件。対応すると消えます</p></div></div>
        <div class="notice-list">
            @if($user->isEmployee())<a class="notice-row danger" href="{{ route('screens.show', 'notifications') }}"><span><b>送れなかった連絡が2件あります</b>（お仕事のご依頼・LINE連携のご案内）</span><strong>送信履歴で確かめる ›</strong></a>@endif
            <a class="notice-row warning" href="{{ route('screens.show', 'phone-log') }}"><span><b>あなたあての電話の伝言が1件あります</b>（サンプル商事 森 様・折り返し待ち）</span><strong>電話記録簿を開く ›</strong></a>
            <a class="notice-row" href="#pending"><span><b>3日以上回答が来ていない依頼が{{ $staleAssignments->count() }}件あります</b></span><strong>一覧を見る ›</strong></a>
        </div>
    </section>

    <div class="content-columns">
        <div class="stack">
            <section class="panel alert-panel">
                <div class="panel-heading"><h2>当日確認が済んでいない人</h2><p>集合2時間前の時点で未確認</p></div>
                <div class="panel-body">
                    @forelse($uncheckedAssignments as $assignment)
                        <div class="person-row"><div><strong>{{ $assignment->user?->name }}</strong><p>{{ $assignment->event?->name }}・集合 {{ substr($assignment->event?->meeting_time, 0, 5) }}</p></div><div class="page-actions"><a class="success-button" href="#">LINEする</a><a class="secondary-button" href="tel:{{ $assignment->user?->phone }}">電話する</a></div></div>
                    @empty
                        <p class="m-0 text-sm text-[#5b6270]">当日確認待ちのスタッフはいません。</p>
                    @endforelse
                </div>
            </section>

            <section class="panel">
                <div class="panel-heading"><h2>7日前確認が済んでいない人</h2><p>本人には5日前・3日前にもう一度届きます</p></div>
                <div class="panel-body">
                    @forelse($sevenDayUncheckedAssignments as $assignment)
                        <div class="person-row"><div><strong>{{ $assignment->user?->name }} <span class="badge badge-orange">未確認</span></strong><p>{{ $assignment->event?->name }}・7日前に送信済み</p></div><div class="page-actions"><a class="success-button" href="#">LINEする</a><a class="secondary-button" href="tel:{{ $assignment->user?->phone }}">電話する</a></div></div>
                    @empty
                        <p class="m-0 text-sm text-[#5b6270]">7日前確認待ちのスタッフはいません。</p>
                    @endforelse
                </div>
            </section>

            <section class="panel">
                <div class="panel-heading"><h2>人数不足のイベント（2週間以内）</h2><a href="{{ route('screens.show', 'shortages') }}">すべて見る</a></div>
                <div class="table-wrap"><table><thead><tr><th>日付</th><th>イベント / 依頼先</th><th>確定 / 必要</th><th>操作</th></tr></thead><tbody>
                    @foreach($shortages->take(4) as $event)<tr><td>{{ $event->event_date->format('m/d') }}</td><td><strong>{{ $event->name }}</strong><small>{{ $event->client->name }} / {{ $event->business_name }}</small></td><td>{{ $event->confirmed_count }} / {{ $event->required_staff }}人 <span class="badge badge-orange">あと{{ $event->required_staff - $event->confirmed_count }}人</span></td><td><a class="secondary-button" href="{{ route('screens.show', 'assignments') }}">アサインする</a></td></tr>@endforeach
                </tbody></table></div>
            </section>

            <section class="panel" id="pending">
                <div class="panel-heading"><h2>回答が来ていない依頼</h2><a href="{{ route('screens.show', 'assignments') }}">すべて見る</a></div>
                <div class="table-wrap"><table><thead><tr><th>スタッフ</th><th>イベント</th><th>依頼日</th><th>経過</th></tr></thead><tbody>
                    @foreach($staleAssignments as $assignment)<tr><td><strong>{{ $assignment->user->name }}</strong></td><td>{{ $assignment->event->name }}</td><td>{{ $assignment->requested_at?->format('m/d') }}</td><td>{{ (int) $assignment->requested_at?->diffInDays() }}日</td></tr>@endforeach
                </tbody></table></div>
            </section>
        </div>

        <aside class="stack">
            <section class="panel">
                <div class="panel-heading"><h2>あなたの予定</h2></div>
                <div class="panel-body">
                    <div class="grid grid-cols-2 gap-3"><div class="detail-box"><span>未回答の依頼</span><strong>{{ $myAssignments->where('status', 'requested')->count() }}件</strong></div><div class="detail-box"><span>今年の勤務回数</span><strong>{{ $user->assignments()->where('status', 'confirmed')->count() }}回</strong></div></div>
                    <div class="mt-4">@foreach($myAssignments->where('status', 'confirmed')->take(3) as $assignment)<div class="person-row"><strong>{{ $assignment->event->event_date->format('m/d') }}</strong><div class="flex-1"><strong>{{ $assignment->event->name }}</strong><p>{{ substr($assignment->event->meeting_time, 0, 5) }}・{{ $assignment->event->meeting_place }}</p></div></div>@endforeach</div>
                </div>
            </section>
            <section class="panel">
                <div class="panel-heading"><h2>期限が近い資格</h2><p>60日以内に期限が切れるもの</p></div>
                <div class="panel-body">@forelse($expiringQualifications as $qualification)<div class="person-row"><div><strong>{{ $qualification->user->name }}</strong><p>{{ $qualification->name }}</p></div><strong class="text-[#8a3108]">{{ $qualification->expires_at->format('m/d') }}</strong></div>@empty<p class="text-sm text-[#5b6270]">期限が近い資格はありません。</p>@endforelse</div>
            </section>
        </aside>
    </div>
@endif
@endsection
