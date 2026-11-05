@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="flex flex-wrap items-end justify-between gap-4"><div><p class="eyebrow">ハイクラス業務管理システム</p><h1 class="page-title">{{ $title }}</h1></div><div class="flex gap-2"><button class="secondary-button">絞り込み</button>@if(in_array($screen,['events','clients','staff','accounts','interviews','phone-log']))<button class="primary-button">＋ 新しく登録</button>@endif</div></div>

@if(in_array($screen, ['staff-home','my-requests','assignment-response','attendance-confirmed','report-submit','payment-statement','profile','interview-select','line-connect','temporary-bank','first-password','mobile-areas']))
<div class="mx-auto mt-7 max-w-[430px] overflow-hidden rounded-[28px] border border-[#e1e5e9] bg-white shadow-[0_16px_50px_rgba(31,36,45,.08)]">
    <div class="border-b border-[#edf0f3] px-6 py-5"><p class="text-xs text-[#7b828d]">{{ auth()->user()->name }} さん</p><h2 class="mt-1 text-xl font-semibold">{{ $title }}</h2></div>
    <div class="grid gap-5 p-6">
        @if(in_array($screen,['line-connect','temporary-bank','first-password']))
            <div class="rounded-2xl bg-[#fbf5ef] p-5 text-sm leading-7"><strong>{{ $screen === 'line-connect' ? 'お仕事の連絡をLINEで受け取る' : ($screen === 'temporary-bank' ? 'お仕事の確定ありがとうございます。' : 'ログインに使うパスワードを決めてください。') }}</strong><p class="mt-2 text-[#666e79]">このページは本人専用です。安全なリンクからログインせずに操作できます。</p></div>
        @else
            @php($event = $events->first())
            <div><span class="badge badge-orange">{{ $event?->status ?? '募集中' }}</span><h3 class="mt-3 text-lg font-semibold">{{ $event?->name ?? '秋季健康フェア' }}</h3><p class="mt-1 text-sm text-[#68707c]">{{ $event?->event_date?->format('m月d日') }}　集合 {{ substr($event?->meeting_time ?? '08:30',0,5) }}</p></div>
            <dl class="detail-list"><div><dt>集合場所</dt><dd>{{ $event?->meeting_place }}</dd></div><div><dt>謝金</dt><dd>9,000円</dd></div><div><dt>交通費</dt><dd>480円</dd></div><div><dt>持ち物</dt><dd>昼食・室内履き・ハイクラスTシャツ</dd></div></dl>
        @endif
        @if($screen === 'assignment-response')<div class="grid grid-cols-3 gap-2"><button class="primary-button">承諾する</button><button class="secondary-button">未定</button><button class="danger-button">辞退する</button></div>
        @elseif($screen === 'attendance-confirmed')<div class="rounded-2xl bg-[#edf8f1] p-6 text-center"><div class="text-4xl text-[#3d8a60]">✓</div><strong class="mt-2 block">確認しました</strong><p class="mt-1 text-sm">ありがとうございます。本日もよろしくお願いします。</p></div>
        @elseif($screen === 'report-submit')<label class="form-label">本日の事業は、無事終了しましたか？<select class="form-input"><option>問題なく終了した</option><option>報告事項あり</option></select></label><label class="form-label">依頼先への報告<textarea class="form-input" rows="5"></textarea></label><button class="primary-button">報告書を提出する</button>
        @elseif(in_array($screen,['temporary-bank','first-password']))<label class="form-label">{{ $screen === 'temporary-bank' ? '郵便番号・住所' : '新しいパスワード' }}<input class="form-input"></label><label class="form-label">{{ $screen === 'temporary-bank' ? '銀行名・支店名・口座番号' : 'もう一度入力' }}<input class="form-input"></label><button class="primary-button">送信する</button>
        @elseif($screen === 'line-connect')<button class="primary-button">LINEで友だち追加して連携する</button>
        @else<a href="{{ route('screens.show','my-requests') }}" class="primary-button text-center">詳しく見る</a>@endif
    </div>
</div>

@elseif(in_array($screen,['events','shortages','clients','staff','accounts','notifications','reports','meeting-history','phone-log','payments','profit-loss']))
<section class="panel mt-7"><div class="panel-heading"><div><h2>{{ $title }}</h2><p>{{ $screen === 'shortages' ? '「確定」の人数が必要人数に届いていないイベント' : '登録済みデータを新しい順に表示しています' }}</p></div><span class="badge">{{ $screen === 'staff' ? $staffMembers->count() : ($screen === 'clients' ? $clients->count() : $events->count()) }}件</span></div>
<div class="filter-bar"><input class="form-input max-w-xs" placeholder="キーワードで検索"><select class="form-input max-w-48"><option>すべて</option><option>募集中</option><option>確定</option></select></div>
<div class="table-wrap"><table><thead><tr>
@if($screen === 'staff')<th>名前</th><th>区分</th><th>最寄り駅</th><th>車</th><th>LINE</th>
@elseif($screen === 'clients')<th>依頼先名</th><th>種別</th><th>電話番号</th><th>担当者</th><th>提案状況</th>
@elseif($screen === 'payments')<th>実施日</th><th>イベント</th><th>スタッフ</th><th>謝金</th><th>総支払額</th><th>状態</th>
@else<th>日付</th><th>イベント / 内容</th><th>依頼先 / 相手</th><th>状態</th><th>操作</th>@endif
</tr></thead><tbody>
@if($screen === 'staff')@foreach($staffMembers as $member)<tr><td><strong>{{ $member->name }}</strong><small>{{ $member->email }}</small></td><td><span class="badge">{{ $member->role }}</span></td><td>{{ $member->nearest_station }}</td><td>{{ $member->has_car ? 'あり' : 'なし' }}</td><td>{{ $member->line_linked_at ? '連携済み' : '未連携' }}</td></tr>@endforeach
@elseif($screen === 'clients')@foreach($clients as $client)<tr><td><strong>{{ $client->name }}</strong><small>{{ $client->department }}</small></td><td>{{ $client->type }}</td><td>{{ $client->phone }}</td><td>{{ $client->contacts->first()?->name ?? '—' }}</td><td><span class="badge badge-orange">{{ $client->proposal_status }}</span></td></tr>@endforeach
@elseif($screen === 'payments')@foreach($assignments as $assignment)<tr><td>{{ $assignment->event->event_date->format('m/d') }}</td><td>{{ $assignment->event->name }}</td><td>{{ $assignment->user->name }}</td><td>¥{{ number_format($assignment->fee) }}</td><td><strong>¥{{ number_format($assignment->total_payment) }}</strong></td><td><span class="badge">{{ $assignment->payment_status }}</span></td></tr>@endforeach
@else @foreach($events as $event)<tr><td>{{ $event->event_date->format('m/d') }}</td><td><strong>{{ $event->name }}</strong><small>集合 {{ substr($event->meeting_time,0,5) }}</small></td><td>{{ $event->client->name }}</td><td><span class="badge badge-orange">{{ $event->status }}</span></td><td><a href="{{ route('screens.show','event-detail') }}" class="table-link">詳細を見る</a></td></tr>@endforeach @endif
</tbody></table></div></section>

@elseif($screen === 'proposal-board')
<div class="mt-7 grid gap-4 xl:grid-cols-5">@foreach(['未接触','アプローチ中','提案中','受注','保留'] as $index => $status)<section class="kanban-column"><div class="flex items-center justify-between"><h2>{{ $status }}</h2><span>{{ $clients->where('proposal_status',['uncontacted','approaching','proposing','won','on_hold'][$index])->count() }}</span></div>@foreach($clients->slice($index % 3,2) as $client)<article><strong>{{ $client->name }}</strong><p>{{ $client->proposal_notes ?: '次回の連絡内容を確認' }}</p><time>次回 {{ $client->next_action_date?->format('m/d') ?? '未定' }}</time></article>@endforeach</section>@endforeach</div>

@else
<div class="mt-7 grid gap-6 xl:grid-cols-3"><section class="panel xl:col-span-2"><div class="panel-heading"><div><h2>基本情報</h2><p>画面案の項目を要件に沿って表示</p></div><button class="secondary-button">編集する</button></div><div class="grid gap-5 p-6 sm:grid-cols-2">
@php($event=$events->first())
@foreach([['依頼先',$event?->client?->name],['事業名',$event?->business_name],['イベント名',$event?->name],['実施日',$event?->event_date?->format('Y/m/d')],['集合場所',$event?->meeting_place],['実施場所',$event?->venue],['必要人数',($event?->required_staff ?? 0).'人'],['ステータス',$event?->status]] as [$label,$value])<div class="detail-box"><span>{{ $label }}</span><strong>{{ $value }}</strong></div>@endforeach
</div></section><aside class="panel"><div class="panel-heading"><div><h2>操作</h2><p>権限に応じた操作</p></div></div><div class="grid gap-3 p-5"><button class="primary-button">保存する</button><button class="secondary-button">複製する</button><button class="danger-button">中止にする</button></div></aside></div>
<section class="panel mt-6"><div class="panel-heading"><div><h2>詳細・履歴</h2><p>関連する記録をまとめて確認できます</p></div></div><div class="grid gap-4 p-6 sm:grid-cols-2 xl:grid-cols-3">@foreach($assignments->take(6) as $assignment)<article class="record-card"><span class="badge">{{ $assignment->status }}</span><strong>{{ $assignment->user->name }}</strong><p>{{ $assignment->special_notes ?: '特記事項はありません' }}</p><small>謝金 ¥{{ number_format($assignment->fee) }}</small></article>@endforeach</div></section>
@endif
@endsection
