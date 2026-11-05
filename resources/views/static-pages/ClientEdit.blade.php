<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>依頼先の詳細・編集</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--clientedit" data-static-screen="ClientEdit">
    <div class="app-root">
      <div class="screen">
        <div class="layout-098">
          <div class="layout-002">
            <nav aria-label="エリア切り替え" class="layout-003">
              <div aria-hidden="true" class="layout-004">HC</div>
              <a href="Main.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="16" y1="3" x2="16" y2="7"></line></svg><span class="text-001">事業</span></a><a href="#" aria-current="page" class="layout-005"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 17 9 11 13 15 21 7"></polyline><polyline points="14 7 21 7 21 14"></polyline></svg><span class="text-001">営業</span></a><a href="Coaching.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H9l-4 3v-3a2 2 0 0 1-2-2z"></path><path d="M19 9a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2v3l-4-3h-3"></path></svg><span class="text-001">面談</span></a><a href="PhoneLog.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><rect x="9" y="2" width="6" height="4" rx="1"></rect><line x1="9" y1="11" x2="15" y2="11"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg><span class="text-001">総務</span></a><a href="PayManage.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4l5 7 5-7"></path><line x1="12" y1="11" x2="12" y2="20"></line><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg><span class="text-001">会計</span></a>
              <div class="layout-007"><a href="Accounts.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"></line><circle cx="9" cy="7" r="2.2" fill="#16213A"></circle><line x1="4" y1="17" x2="20" y2="17"></line><circle cx="15" cy="17" r="2.2" fill="#16213A"></circle></svg><span class="text-001">設定</span></a></div>
            </nav>
            <nav aria-label="メインメニュー" class="layout-008">
              <div class="space-001">
                <div class="text-002">ハイクラス業務管理システム</div>
                <div class="text-003">営業</div>
              </div>
              <a href="SalesHome.dc.html" class="layout-010"><span>営業ホーム</span></a><a href="#" aria-current="page" class="layout-009"><span>依頼先(顧客リスト)</span><span class="surface-001">共通</span></a><a href="SalesBoard.dc.html" class="layout-010"><span>提案状況</span></a><a href="Meetings.dc.html" class="layout-010"><span>打合せ履歴</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              <div class="text-007"><a href="Clients.dc.html">依頼先</a> / サンプル商事 株式会社</div>
              <div class="layout-013">
                <div>
                  <div class="layout-042">
                    <h1 class="text-044">サンプル商事 株式会社</h1>
                    <span class="surface-029">法人</span><span class="surface-047">提案中</span></div>
                  <div class="text-060">地域連携部 ・ 最終更新 2026/10/15 山田 太郎 ・ この詳細ページは社員のみ閲覧できます</div>
                </div>
                <a href="#" class="text-109">+ この依頼先でイベントを登録</a></div>
              <form method="POST" action="{{ $client ? route('clients.update', $client) : route('clients.store') }}">
                @csrf
                @if($client) @method('PATCH') @endif
              <div class="layout-099">
                <section aria-labelledby="ce-basic" class="layout-100">
                  <h2 id="ce-basic" class="text-020">基本情報</h2>
                  <div class="layout-101">
                    <div class="layout-051"><label for="ce-name" class="text-042">依頼先名<span class="text-050">必須</span></label><input id="ce-name" name="name" type="text" value="{{ old('name', $client?->name) }}" required maxlength="255" class="text-051"></div>
                    <div class="layout-051"><label for="ce-kind" class="text-042">種別</label><select id="ce-kind" name="type" class="surface-026">@foreach (['corporation' => '法人', 'government' => '行政', 'individual' => '個人', 'organization' => '団体', 'other' => 'その他'] as $value => $label)<option value="{{ $value }}" @selected(old('type', $client?->type ?? 'corporation') === $value)>{{ $label }}</option>@endforeach</select></div>
                  </div>
                  <div class="layout-051"><label for="ce-dept" class="text-042">部署名</label><input id="ce-dept" name="department" type="text" value="{{ old('department', $client?->department) }}" maxlength="255" class="text-051"></div>
                  <div class="layout-050">
                    <div class="layout-051"><label for="ce-tel" class="text-042">電話番号</label><input id="ce-tel" name="phone" type="tel" value="{{ old('phone', $client?->phone) }}" maxlength="30" class="text-051"></div>
                    <div class="layout-051"><label for="ce-zip" class="text-042">郵便番号</label><input id="ce-zip" name="postal_code" type="text" inputmode="numeric" value="{{ old('postal_code', $client?->postal_code) }}" maxlength="12" class="text-051"></div>
                  </div>
                  <div class="layout-051"><label for="ce-addr" class="text-042">住所</label><input id="ce-addr" name="address" type="text" value="{{ old('address', $client?->address) }}" maxlength="1000" class="text-051"></div>
                </section>
                <section aria-labelledby="ce-note" class="layout-102"><label id="ce-note" for="ce-note-text" class="text-035">特記事項</label><textarea id="ce-note-text" name="notes" rows="12" maxlength="5000" class="text-110">{{ old('notes', $client?->notes) }}</textarea></section>
                @if ($errors->any())<div role="alert" class="text-050">{{ $errors->first() }}</div>@endif
                @if (session('status'))<div role="status" class="text-060">{{ session('status') }}</div>@endif
              </div>
              <section aria-labelledby="ce-person" class="surface-022">
                <div class="layout-021">
                  <h2 id="ce-person" class="text-020">担当者</h2>
                  <button type="button" data-add-contact class="control-028">+ 担当者を追加</button></div>
                <div class="utility-001">
                  <table class="text-111">
                    <thead>
                      <tr class="text-065"><th class="cell-035">役職</th><th class="cell-036">担当者名</th><th class="cell-036">携帯電話</th><th class="cell-036">メールアドレス</th><th class="cell-037">退職</th><th class="cell-036"><span class="space-004">操作</span></th></tr>
                    </thead>
                    <tbody data-contact-rows>
                      @foreach ($client?->contacts ?? [] as $contact)
                        <tr class="{{ $contact->is_retired ? 'utility-009' : 'utility-004' }}" data-contact-row><input type="hidden" name="contacts[{{ $loop->index }}][id]" value="{{ $contact->id }}"><td class="cell-038"><input name="contacts[{{ $loop->index }}][position]" type="text" aria-label="役職" placeholder="例:課長" value="{{ $contact->position }}" class="text-112"></td><td class="cell-039"><input name="contacts[{{ $loop->index }}][name]" type="text" aria-label="担当者名" value="{{ $contact->name }}" required class="text-112"></td><td class="cell-039"><input name="contacts[{{ $loop->index }}][mobile]" type="tel" aria-label="携帯電話" value="{{ $contact->mobile }}" class="text-112"></td><td class="cell-039"><input name="contacts[{{ $loop->index }}][email]" type="email" aria-label="メールアドレス" value="{{ $contact->email }}" class="text-112"></td><td class="cell-040"><input type="hidden" name="contacts[{{ $loop->index }}][is_retired]" value="0"><input name="contacts[{{ $loop->index }}][is_retired]" value="1" type="checkbox" aria-label="{{ $contact->name }}は退職" @checked($contact->is_retired) class="space-006"></td><td class="cell-041"><button type="button" data-remove-contact aria-label="この担当者を削除" class="control-029">削除</button></td></tr>
                      @endforeach
                    </tbody>
                  </table>
                  <template id="contact-row-template"><tr class="utility-004" data-contact-row><td class="cell-038"><input name="contacts[__INDEX__][position]" type="text" aria-label="役職" placeholder="例:課長" class="text-112"></td><td class="cell-039"><input name="contacts[__INDEX__][name]" type="text" aria-label="担当者名" required class="text-112"></td><td class="cell-039"><input name="contacts[__INDEX__][mobile]" type="tel" aria-label="携帯電話" class="text-112"></td><td class="cell-039"><input name="contacts[__INDEX__][email]" type="email" aria-label="メールアドレス" class="text-112"></td><td class="cell-040"><input type="hidden" name="contacts[__INDEX__][is_retired]" value="0"><input name="contacts[__INDEX__][is_retired]" value="1" type="checkbox" aria-label="退職" class="space-006"></td><td class="cell-041"><button type="button" data-remove-contact aria-label="この担当者を削除" class="control-029">削除</button></td></tr></template>
                </div>
              </section>
              <section aria-labelledby="ce-prop" class="layout-049">
                <h2 id="ce-prop" class="text-020">提案状況</h2>
                <div class="layout-050">
                  <div class="layout-051"><label for="ce-pstat" class="text-042">状況</label><select id="ce-pstat" name="proposal_status" class="surface-026">@foreach (['uncontacted' => '未接触', 'approaching' => 'アプローチ中', 'proposing' => '提案中', 'won' => '受注', 'lost' => '失注', 'on_hold' => '保留'] as $value => $label)<option value="{{ $value }}" @selected(old('proposal_status', $client?->proposal_status ?? 'uncontacted') === $value)>{{ $label }}</option>@endforeach</select></div>
                  <div class="layout-051"><label for="ce-next" class="text-042">次回アクション日</label><input id="ce-next" name="next_action_date" type="date" value="{{ old('next_action_date', $client?->next_action_date?->format('Y-m-d')) }}" class="text-051"></div>
                  <div class="layout-051"><label for="ce-owner" class="text-042">社内の担当</label><select id="ce-owner" name="sales_owner_id" class="surface-026"><option value="">未設定</option>@foreach ($employees ?? [] as $employee)<option value="{{ $employee->id }}" @selected((string) old('sales_owner_id', $client?->sales_owner_id) === (string) $employee->id)>{{ $employee->name }}</option>@endforeach</select></div>
                  <div class="layout-051">
                    <div class="text-042">状況を変えた日</div>
                    <div class="layout-103">{{ $client?->proposal_status_changed_at?->timezone('Asia/Tokyo')->format('Y/m/d') ?? '—' }}(状況を変えると自動で記録)</div>
                  </div>
                </div>
                <div class="layout-051"><label for="ce-pmemo" class="text-042">提案内容・次回アクション</label><textarea id="ce-pmemo" name="proposal_notes" rows="4" maxlength="5000" class="text-113">{{ old('proposal_notes', $client?->proposal_notes) }}</textarea></div>
              </section>
              <div class="layout-104"><a href="{{ route('clients.index') }}" class="text-057">一覧に戻る</a><button type="submit" class="control-003">保存する</button></div>
              </form>
              <section aria-labelledby="ce-meet" class="surface-022">
                <div class="layout-021">
                  <h2 id="ce-meet" class="text-020">打合せ・イベント履歴(<span>{{ ($client?->meetings->count() ?? 0) + ($client?->events->count() ?? 0) }}</span>件)</h2>
                  @if ($client)<button type="button" data-add-meeting class="control-028">+ 打合せを登録</button>@endif</div>
                <div class="surface-048">営業メールの宛先(CCまたはBCC)に <strong>log@example.co.jp</strong> を入れると、この依頼先の履歴にメールとして自動で登録されます。この依頼先のイベントも自動で履歴に並びます。</div>
                <div class="layout-047">
                  @if ($client)
                    @php
                      $meetingTypeLabels = ['sales' => '営業', 'preparation' => '事前準備', 'consultation' => '相談', 'follow_up' => '事後フォロー', 'other' => 'その他'];
                      $meetingMethodLabels = ['visit' => '訪問', 'phone' => '電話', 'online' => 'オンライン', 'email' => 'メール', 'other' => 'その他'];
                      $eventStatusLabels = ['draft' => '下書き', 'recruiting' => '募集中', 'confirmed' => '確定', 'completed' => '実施済み', 'cancelled' => '中止'];
                      $history = $client->meetings->map(fn ($meeting) => ['date' => $meeting->meeting_date, 'kind' => 'meeting', 'record' => $meeting])
                        ->concat($client->events->map(fn ($eventRecord) => ['date' => $eventRecord->event_date, 'kind' => 'event', 'record' => $eventRecord]))
                        ->sortByDesc('date');
                    @endphp
                    @forelse ($history as $historyItem)
                      @if ($historyItem['kind'] === 'event')
                        @php $historyEvent = $historyItem['record']; @endphp
                        <div class="cell-014"><div class="layout-063"><div class="layout-105"><span class="text-114"><span>{{ $historyEvent->event_date->format('Y/m/d') }}</span></span><span class="surface-049"><span>当日</span></span><span class="surface-050"><span>イベント</span></span><span><span>自動登録</span></span></div><a href="{{ route('screens.show', 'event-detail') }}" class="text-115">イベントを開く</a></div><div class="text-116"><span>{{ $historyEvent->name ?: $historyEvent->business_name }}({{ $historyEvent->business_name }})・必要{{ $historyEvent->required_staff }}人・{{ $eventStatusLabels[$historyEvent->status] ?? $historyEvent->status }}</span></div></div>
                      @else
                        @php $historyMeeting = $historyItem['record']; @endphp
                        <div class="cell-014"><div class="layout-063"><div class="layout-105"><span class="text-114"><span>{{ $historyMeeting->meeting_date->format('Y/m/d') }}</span></span><span class="surface-051"><span>{{ $meetingTypeLabels[$historyMeeting->type] ?? $historyMeeting->type }}</span></span><span class="surface-050"><span>{{ $meetingMethodLabels[$historyMeeting->method] ?? $historyMeeting->method }}</span></span><span><span>先方:{{ $historyMeeting->participants ?: '—' }} 記録:{{ $historyMeeting->recorder->name }}</span></span></div><div class="layout-106"><button type="button" data-edit-meeting data-meeting="{{ base64_encode(json_encode($historyMeeting->only(['id', 'type', 'meeting_date', 'method', 'participants', 'content']))) }}" class="control-030">編集</button><form method="POST" action="{{ route('clients.meetings.destroy', [$client, $historyMeeting]) }}" data-confirm-delete><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="control-031">削除</button></form></div></div><div class="text-116"><span>{{ $historyMeeting->content }}</span></div></div>
                      @endif
                    @empty
                      <div class="cell-014"><div class="text-116"><span>打合せ・イベント履歴はまだありません。</span></div></div>
                    @endforelse
                  @else
                  <div class="cell-014">
                    <div class="layout-063">
                      <div class="layout-105"><span class="text-114"><span>2026/10/21</span></span><span class="surface-049"><span>当日</span></span><span class="surface-050"><span>イベント</span></span><span><span>自動登録</span></span></div>
                      <a href="#" class="text-115">イベントを開く</a></div>
                    <div class="text-116"><span>秋季健康フェア(地域イベント運営)・必要5人・募集中</span></div>
                  </div>
                  <div class="cell-014">
                    <div class="layout-063">
                      <div class="layout-105"><span class="text-114"><span>2026/10/16</span></span><span class="surface-051"><span>営業</span></span><span class="surface-050"><span>メール(自動登録)</span></span><span><span>先方:青木 様 記録:山田 太郎</span></span></div>
                      <div class="layout-106"><button type="button" aria-label="2026/10/16の記録を編集" class="control-030">編集</button><button type="button" aria-label="2026/10/16の記録を削除" class="control-031">削除</button></div>
                    </div>
                    <div class="surface-052">
                      <div class="text-067">件名:<span>来年度 体力測定会の運営スタッフについて</span></div>
                      <div class="text-117"><span>青木様 いつもお世話になっております。先日はお時間をいただきありがとうございました。来年度の体力測定会(年4回)につきまして、各回6名の体制でお見積りを作成し…</span></div>
                      <a href="#" class="text-118">メール全文を見る</a></div>
                  </div>
                  <div class="cell-014">
                    <div class="layout-063">
                      <div class="layout-105"><span class="text-114"><span>2026/10/15</span></span><span class="surface-051"><span>営業</span></span><span class="surface-050"><span>訪問</span></span><span><span>先方:青木 様 記録:山田 太郎</span></span></div>
                      <div class="layout-106"><button type="button" aria-label="2026/10/15の記録を編集" class="control-030">編集</button><button type="button" aria-label="2026/10/15の記録を削除" class="control-031">削除</button></div>
                    </div>
                    <div class="text-116"><span>来年度の健康づくり事業について説明を受けた。体力測定会を年4回実施予定で、運営スタッフを各回6名ほど探しているとのこと。</span></div>
                  </div>
                  <div class="cell-014">
                    <div class="layout-063">
                      <div class="layout-105"><span class="text-114"><span>2026/10/14</span></span><span class="surface-053"><span>事前準備</span></span><span class="surface-050"><span>電話</span></span><span><span>先方:青木 様 記録:山田 太郎</span></span></div>
                      <div class="layout-106"><button type="button" aria-label="2026/10/14の記録を編集" class="control-030">編集</button><button type="button" aria-label="2026/10/14の記録を削除" class="control-031">削除</button></div>
                    </div>
                    <div class="text-116"><span>秋季健康フェアの集合場所と持ち物を確認。当日はハイクラスTシャツ着用で問題なし。</span></div>
                  </div>
                  <div class="cell-014">
                    <div class="layout-063">
                      <div class="layout-105"><span class="text-114"><span>2026/09/30</span></span><span class="surface-054"><span>相談</span></span><span class="surface-050"><span>電話</span></span><span><span>先方:青木 様 記録:山田 太郎</span></span></div>
                      <div class="layout-106"><button type="button" aria-label="2026/09/30の記録を編集" class="control-030">編集</button><button type="button" aria-label="2026/09/30の記録を削除" class="control-031">削除</button></div>
                    </div>
                    <div class="text-116"><span>秋季健康フェアの人数を5名で確定。報告書は依頼先への報告用文章をメールで送ってほしいとの要望。</span></div>
                  </div>
                  <div class="cell-014">
                    <div class="layout-063">
                      <div class="layout-105"><span class="text-114"><span>2026/09/14</span></span><span class="surface-049"><span>当日</span></span><span class="surface-050"><span>イベント</span></span><span><span>自動登録</span></span></div>
                      <a href="#" class="text-115">イベントを開く</a></div>
                    <div class="text-116"><span>敬老イベント 会場案内(地域イベント運営)・4人・実施済み</span></div>
                  </div>
                  <div class="cell-014">
                    <div class="layout-063">
                      <div class="layout-105"><span class="text-114"><span>2026/08/20</span></span><span class="surface-055"><span>事後フォロー</span></span><span class="surface-050"><span>オンライン</span></span><span><span>先方:青木 様、課長 記録:佐々木 恵</span></span></div>
                      <div class="layout-106"><button type="button" aria-label="2026/08/20の記録を編集" class="control-030">編集</button><button type="button" aria-label="2026/08/20の記録を削除" class="control-031">削除</button></div>
                    </div>
                    <div class="text-116"><span>夏祭りの振り返り。受付の待ち時間について改善の要望あり。</span></div>
                  </div>
                  <div class="cell-014">
                    <div class="layout-063">
                      <div class="layout-105"><span class="text-114"><span>2026/08/02</span></span><span class="surface-049"><span>当日</span></span><span class="surface-050"><span>イベント</span></span><span><span>自動登録</span></span></div>
                      <a href="#" class="text-115">イベントを開く</a></div>
                    <div class="text-116"><span>夏祭り 運営補助(夏祭り事業)・8人・実施済み</span></div>
                  </div>
                  @endif
                </div>
              </section>
              @if ($client)
              <dialog id="meeting-dialog" class="surface-006">
                <form method="POST" action="{{ route('clients.meetings.store', $client) }}" data-meeting-form data-client-id="{{ $client->id }}" class="layout-158">
                  @csrf
                  <input type="hidden" name="_method" value="POST" data-meeting-method>
                  <h2 class="text-020" data-meeting-dialog-title>打合せを登録</h2>
                  <div class="layout-055"><label class="text-042" for="meeting-date">日付</label><input id="meeting-date" name="meeting_date" type="date" required class="text-076"></div>
                  <div class="layout-055"><label class="text-042" for="meeting-type">種別</label><select id="meeting-type" name="type" required class="text-076">@foreach ($meetingTypeLabels as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
                  <div class="layout-055"><label class="text-042" for="meeting-method">方法</label><select id="meeting-method" name="method" required class="text-076">@foreach ($meetingMethodLabels as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
                  <div class="layout-055"><label class="text-042" for="meeting-participants">先方参加者</label><input id="meeting-participants" name="participants" maxlength="1000" class="text-076"></div>
                  <div class="layout-055"><label class="text-042" for="meeting-content">内容</label><textarea id="meeting-content" name="content" required maxlength="10000" rows="6" class="text-076"></textarea></div>
                  <div class="layout-024"><button type="button" data-close-meeting class="control-015">キャンセル</button><button type="submit" class="control-014">保存する</button></div>
                </form>
              </dialog>
              @endif
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
