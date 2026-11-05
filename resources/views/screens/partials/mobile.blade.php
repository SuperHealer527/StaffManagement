<div class="mobile-card">
    <header class="mobile-page-head">
        <p>{{ auth()->user()->name }}さん</p>
        <h1>{{ $screen === 'my-requests' ? '依頼' : $title }}</h1>
    </header>
    <div class="mobile-card-body">
        @switch($screen)
            @case('staff-home')
                <p class="text-sm text-[#5b6270]">スタッフ用ホームはログイン中の役割に合わせて表示します。</p>
                <a href="{{ route('dashboard') }}" class="primary-button">ホームを開く</a>
                @break

            @case('my-requests')
                <div class="flex gap-2"><button class="primary-button flex-1">未回答</button><button class="secondary-button flex-1">確定</button><button class="secondary-button flex-1">すべて</button></div>
                @forelse($myAssignments as $assignment)
                    <article class="mobile-event">
                        <div class="flex items-start justify-between gap-3"><div><span class="badge {{ $assignment->status === 'requested' ? 'badge-orange' : 'badge-green' }}">{{ $assignment->status === 'requested' ? '未回答' : '確定' }}</span><h2 class="mt-2">{{ $assignment->event->name }}</h2></div><strong>{{ $assignment->event->event_date->format('m/d') }}</strong></div>
                        <p>集合 {{ substr($assignment->event->meeting_time, 0, 5) }}　{{ $assignment->event->meeting_place }}<br>{{ $assignment->event->client->name }}</p>
                        <a class="primary-button" href="{{ route('screens.show', 'assignment-response') }}">内容を確認する</a>
                    </article>
                @empty
                    <div class="mobile-event"><p>表示できる依頼はありません。</p></div>
                @endforelse
                @break

            @case('assignment-response')
                <div><span class="badge badge-orange">回答期限 10月20日 18:00</span><h2 class="mt-3 text-xl">{{ $event?->name }}</h2><p class="mt-1 text-sm text-[#5b6270]">{{ $event?->client?->name }}</p></div>
                <dl class="detail-list">
                    <div><dt>実施日</dt><dd>{{ $event?->event_date?->isoFormat('M月D日（ddd）') }}</dd></div>
                    <div><dt>集合</dt><dd>{{ substr($event?->meeting_time ?? '', 0, 5) }}　{{ $event?->meeting_place }}</dd></div>
                    <div><dt>勤務時間</dt><dd>{{ substr($event?->start_time ?? '', 0, 5) }}〜{{ substr($event?->end_time ?? '', 0, 5) }}</dd></div>
                    <div><dt>仕事内容</dt><dd>{{ $event?->work_content }}</dd></div>
                    <div><dt>謝金</dt><dd>¥{{ number_format($myAssignments->first()?->fee ?? 9000) }}</dd></div>
                    <div><dt>交通費</dt><dd>¥{{ number_format($myAssignments->first()?->transportation ?? 480) }}</dd></div>
                    <div><dt>持ち物</dt><dd>{{ implode('・', $event?->items ?? []) }}</dd></div>
                </dl>
                <section><h2 class="m-0 text-[17px]">このお仕事を引き受けられますか？</h2><p class="mt-2 text-sm leading-6 text-[#5b6270]">回答後に予定が変わった場合は、ハイクラスまで直接ご連絡ください。</p></section>
                <div class="mobile-actions"><button class="primary-button">引き受ける</button><button class="secondary-button">まだ分からない</button><button class="danger-button">辞退する</button></div>
                @break

            @case('attendance-confirmed')
                <div class="success-message"><span class="text-4xl">✓</span><strong>確認しました</strong><p class="mt-2 text-sm">ありがとうございます。本日もよろしくお願いします。</p></div>
                <section class="mobile-event"><span class="badge badge-green">本日のお仕事</span><h2>{{ $event?->name }}</h2><p>集合 {{ substr($event?->meeting_time ?? '', 0, 5) }}　{{ $event?->meeting_place }}<br>緊急連絡先：ハイクラスLINE / 山田 太郎</p></section>
                <a href="{{ route('dashboard') }}" class="secondary-button">ホームへ戻る</a>
                @break

            @case('report-submit')
                <div><h2 class="m-0 text-[18px]">{{ $event?->name }}</h2><p class="mt-1 text-sm text-[#5b6270]">{{ $event?->event_date?->isoFormat('M月D日（ddd）') }}　{{ $event?->client?->name }}</p></div>
                <label class="form-label">本日の事業は、無事終了しましたか？<select class="form-input"><option>問題なく終了した</option><option>報告事項あり</option><option>事故・トラブルあり</option></select></label>
                <label class="form-label">参加人数<input class="form-input" type="number" value="120"></label>
                <label class="form-label">依頼先への報告<textarea class="form-input" rows="5" placeholder="当日の状況や引き継ぎ事項を入力してください"></textarea></label>
                <label class="form-label">写真・資料<input class="form-input" type="file" multiple></label>
                <p class="rounded-lg bg-[#fff4d6] p-3 text-xs leading-5 text-[#6b4e00]">提出後は編集できません。内容を確認してから送信してください。</p>
                <button class="primary-button min-h-12">報告書を提出する</button>
                @break

            @case('payment-statement')
                <div class="rounded-xl bg-[#16213a] p-5 text-white"><p class="m-0 text-xs text-[#c3cbda]">2026年10月分</p><strong class="mt-2 block text-3xl">¥{{ number_format($myAssignments->sum('total_payment')) }}</strong><p class="mt-2 mb-0 text-xs text-[#c3cbda]">支払予定日 2026年11月25日</p></div>
                <section><h2 class="m-0 text-[16px]">勤務ごとの明細</h2><div class="mt-3 grid gap-3">@foreach($myAssignments as $assignment)<article class="mobile-event"><div class="flex justify-between gap-3"><strong>{{ $assignment->event->event_date->format('m/d') }} {{ $assignment->event->name }}</strong><strong>¥{{ number_format($assignment->total_payment) }}</strong></div><p>謝金 ¥{{ number_format($assignment->fee) }} / 交通費 ¥{{ number_format($assignment->transportation) }} / 立替 ¥{{ number_format($assignment->reimbursement) }} / 源泉税 -¥{{ number_format($assignment->withholding_tax) }}</p></article>@endforeach</div></section>
                <button class="secondary-button">PDFをダウンロード</button>
                @break

            @case('profile')
                @php($profile = auth()->user())
                <section class="panel"><div class="panel-heading"><h2>基本情報</h2><button class="table-link">編集</button></div><div class="panel-body"><dl class="detail-list"><div><dt>名前</dt><dd>{{ $profile->name }}</dd></div><div><dt>区分</dt><dd>{{ $profile->role }}</dd></div><div><dt>生年月日</dt><dd>{{ $profile->birth_date?->format('Y年m月d日') ?? '未登録' }}</dd></div><div><dt>最寄り駅</dt><dd>{{ $profile->nearest_station }}</dd></div></dl></div></section>
                <section class="panel"><div class="panel-heading"><h2>住所・連絡先</h2><button class="table-link">編集</button></div><div class="panel-body"><dl class="detail-list"><div><dt>住所</dt><dd>{{ $profile->address ?: '未登録' }}</dd></div><div><dt>携帯電話</dt><dd>{{ $profile->phone }}</dd></div><div><dt>メール</dt><dd>{{ $profile->email }}</dd></div></dl></div></section>
                <section class="panel"><div class="panel-heading"><h2>LINE連携・通知方法</h2></div><div class="panel-body"><p class="m-0 text-sm">{{ $profile->line_linked_at ? 'LINE連携済み' : 'LINE未連携' }} / 通知：{{ $profile->notification_method }}</p></div></section>
                @foreach(['車', '対応可能時間', '保有資格', '振込先口座', '契約書'] as $sectionTitle)<section class="panel"><div class="panel-heading"><h2>{{ $sectionTitle }}</h2><button class="table-link">{{ in_array($sectionTitle, ['保有資格', '対応可能時間']) ? '追加・編集' : '編集' }}</button></div><div class="panel-body text-sm text-[#5b6270]">登録内容を安全に管理しています。変更がある場合は編集してください。</div></section>@endforeach
                @break

            @case('first-password')
                <div><h2 class="m-0 text-[18px]">ログインに使うパスワードを決めてください</h2><p class="mt-2 text-sm leading-6 text-[#5b6270]">8文字以上で、英字と数字を組み合わせてください。</p></div>
                <label class="form-label">メールアドレス<input class="form-input" value="{{ auth()->user()->email }}" disabled></label>
                <label class="form-label">新しいパスワード<input class="form-input" type="password"></label>
                <label class="form-label">もう一度入力<input class="form-input" type="password"></label>
                <button class="primary-button min-h-12">パスワードを設定する</button>
                @break

            @case('mobile-areas')
                <p class="text-sm text-[#5b6270]">開くエリアを選んでください。</p>
                <div class="grid grid-cols-2 gap-3">@foreach([['事業','イベント運営','dashboard'],['営業','提案・打合せ','sales-home'],['面談','予定・記録','interviews'],['総務','電話記録簿','phone-log'],['会計','支払・収支','payments'],['設定','アカウント','accounts']] as [$name,$desc,$target])<a class="record-card text-center no-underline" href="{{ $target === 'dashboard' ? route('dashboard') : route('screens.show', $target) }}"><strong class="text-[#1a1d23]">{{ $name }}</strong><small>{{ $desc }}</small></a>@endforeach</div>
                @break

            @case('interview-select')
                <div><h2 class="m-0 text-[18px]">面談の日程を選んでください</h2><p class="mt-2 text-sm leading-6 text-[#5b6270]">山田 太郎との定期面談です。都合のよい日時を1つ選択してください。</p></div>
                <div class="grid gap-3">@foreach(['11月10日（火）10:00〜10:30','11月11日（水）14:00〜14:30','11月13日（金）16:00〜16:30'] as $slot)<label class="flex items-center gap-3 rounded-xl border border-[#e3e6eb] p-4 text-sm font-medium"><input type="radio" name="slot">{{ $slot }}</label>@endforeach</div>
                <button class="primary-button min-h-12">この日時で回答する</button>
                @break

            @case('line-connect')
                <div class="rounded-xl bg-[#e8f5ed] p-5 text-center text-[#075d2c]"><div class="text-4xl">LINE</div><h2 class="mt-3 mb-0 text-[18px]">お仕事の連絡をLINEで受け取る</h2></div>
                <p class="m-0 text-sm leading-7 text-[#5b6270]">依頼への回答、出勤確認、当日の連絡をLINEで受け取れます。このページは本人専用です。</p>
                <button class="success-button min-h-12">LINEで友だち追加して連携する</button>
                <p class="m-0 text-center text-xs text-[#5b6270]">連携できない場合はハイクラス担当者へご連絡ください。</p>
                @break

            @case('temporary-bank')
                <div><h2 class="m-0 text-[18px]">お仕事の確定ありがとうございます</h2><p class="mt-2 text-sm leading-6 text-[#5b6270]">謝金のお振込みと必要書類のため、住所と口座を入力してください。</p></div>
                <section><h2 class="text-[16px]">住所</h2><div class="grid gap-3"><label class="form-label">郵便番号<input class="form-input" placeholder="123-4567"></label><label class="form-label">都道府県・市区町村<input class="form-input"></label><label class="form-label">番地・建物名<input class="form-input"></label></div></section>
                <section><h2 class="text-[16px]">振込先口座</h2><div class="grid gap-3"><label class="form-label">銀行名<input class="form-input"></label><label class="form-label">支店名<input class="form-input"></label><div class="grid grid-cols-2 gap-3"><label class="form-label">口座種別<select class="form-input"><option>普通</option><option>当座</option></select></label><label class="form-label">口座番号<input class="form-input"></label></div><label class="form-label">口座名義（カナ）<input class="form-input"></label></div></section>
                <label class="flex items-start gap-3 text-xs leading-5"><input class="mt-1" type="checkbox">入力内容と個人情報の取扱いを確認しました。</label>
                <button class="primary-button min-h-12">入力内容を送信する</button>
                @break
        @endswitch
    </div>
</div>
