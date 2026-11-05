<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>面談の予定・記録</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--coaching" data-static-screen="Coaching">
    <div class="app-root">
      <div class="screen">
        <div class="layout-108">
          <div class="layout-002">
            <nav aria-label="エリア切り替え" class="layout-003">
              <div aria-hidden="true" class="layout-004">HC</div>
              <a href="Main.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="16" y1="3" x2="16" y2="7"></line></svg><span class="text-001">事業</span></a><a href="SalesHome.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 17 9 11 13 15 21 7"></polyline><polyline points="14 7 21 7 21 14"></polyline></svg><span class="text-001">営業</span></a><a href="#" aria-current="page" class="layout-005"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H9l-4 3v-3a2 2 0 0 1-2-2z"></path><path d="M19 9a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2v3l-4-3h-3"></path></svg><span class="text-001">面談</span></a><a href="PhoneLog.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><rect x="9" y="2" width="6" height="4" rx="1"></rect><line x1="9" y1="11" x2="15" y2="11"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg><span class="text-001">総務</span></a><a href="PayManage.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4l5 7 5-7"></path><line x1="12" y1="11" x2="12" y2="20"></line><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg><span class="text-001">会計</span></a>
              <div class="layout-007"><a href="Accounts.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"></line><circle cx="9" cy="7" r="2.2" fill="#16213A"></circle><line x1="4" y1="17" x2="20" y2="17"></line><circle cx="15" cy="17" r="2.2" fill="#16213A"></circle></svg><span class="text-001">設定</span></a></div>
            </nav>
            <nav aria-label="メインメニュー" class="layout-008">
              <div class="space-001">
                <div class="text-002">ハイクラス業務管理システム</div>
                <div class="text-003">面談(指導記録)</div>
              </div>
              <a href="#" aria-current="page" class="layout-009"><span>面談の予定・記録</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              @if(isset($selectedInterview) || isset($managers))
              @php $typeLabels=['regular'=>'定期面談','individual_guidance'=>'個別指導','evaluation'=>'評価面談','other'=>'その他']; $methodLabels=['in_person'=>'対面','online'=>'オンライン','phone'=>'電話']; @endphp
              <div class="layout-013"><div><h1 class="text-044">面談の予定・記録</h1><div class="text-060">面談候補日時を送り、決定日時と指導記録を管理します。社員だけが開けます。</div></div></div>
              @if(session('status'))<div role="status" class="surface-045">{{ session('status') }}</div>@endif @if($errors->any())<div role="alert" class="surface-045">{{ $errors->first() }}</div>@endif
              <form method="POST" action="{{ route('interviews.store') }}" class="surface-006">@csrf<h2 class="text-020">+ 面談を設定</h2><div class="layout-046"><label class="layout-051">相手<select name="subject_user_id" required class="surface-017"><option value="">選択</option>@foreach($staffMembers as $member)<option value="{{ $member->id }}">{{ $member->name }}</option>@endforeach</select></label><label class="layout-051">担当<select name="manager_id" required class="surface-017">@foreach($managers as $manager)<option value="{{ $manager->id }}" @selected($manager->id===auth()->id())>{{ $manager->name }}</option>@endforeach</select></label><label class="layout-051">種別<select name="type" class="surface-017">@foreach($typeLabels as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></label><label class="layout-051">方法<select name="method" class="surface-017">@foreach($methodLabels as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></label><label class="layout-051">場所<input name="location" class="surface-034"></label></div><div class="layout-046">@for($i=0;$i<3;$i++)<label class="layout-051">候補{{ $i+1 }}<input name="candidate_times[]" type="datetime-local" @required($i===0) class="surface-034"></label>@endfor<button type="submit" class="control-011">候補日時を送る</button></div></form>
              <div role="note" class="layout-109">面談記録を開いた社員と日時は監査ログに残ります。</div>
              <section aria-labelledby="cf-plan" class="surface-006"><h2 id="cf-plan" class="text-020">これからの面談</h2><div class="utility-001"><table class="text-074"><thead><tr class="text-023"><th>日時</th><th>相手</th><th>種別・方法</th><th>担当</th><th>状態</th></tr></thead><tbody>@forelse($interviews->filter(fn($item)=>$item->scheduled_at===null || $item->scheduled_at->isFuture()) as $item)<tr><td class="cell-003">{{ $item->scheduled_at?->format('n/j H:i') ?? '候補'.count($item->candidate_times).'つを送信済み' }}</td><td class="cell-047">{{ $item->subject->name }}</td><td class="cell-004">{{ $typeLabels[$item->type]??$item->type }}・{{ $methodLabels[$item->method]??$item->method }}</td><td class="cell-005">{{ $item->manager->name }}</td><td class="cell-005"><span class="{{ $item->scheduled_at?'surface-019':'surface-033' }}">{{ $item->scheduled_at?'確定':'調整中' }}</span></td></tr>@empty<tr><td colspan="5">これからの面談はありません。</td></tr>@endforelse</tbody></table></div></section>
              <div class="layout-066"><section aria-labelledby="cf-log" class="surface-057"><h2 id="cf-log" class="text-020">面談の記録</h2><div class="utility-001"><table class="text-077"><thead><tr class="text-023"><th>日付</th><th>相手</th><th>種別</th><th>担当</th><th>操作</th></tr></thead><tbody>@forelse($interviews as $item)<tr><td class="cell-003">{{ $item->scheduled_at?->format('n/j') ?? '未定' }}</td><td class="cell-047">{{ $item->subject->name }}</td><td class="cell-005">{{ $typeLabels[$item->type]??$item->type }}</td><td class="cell-005">{{ $item->manager->name }}</td><td class="cell-006"><a href="{{ route('interviews.index',['interview_id'=>$item->id]) }}" class="control-012">内容を見る</a></td></tr>@empty<tr><td colspan="5">記録はありません。</td></tr>@endforelse</tbody></table></div></section>
              <section aria-labelledby="cf-detail" class="surface-059">@if($selectedInterview)<h2 id="cf-detail" class="text-020">{{ $selectedInterview->subject->name }} さんとの面談</h2><form method="POST" action="{{ route('interviews.update',$selectedInterview) }}" class="layout-110">@csrf @method('PATCH')<div class="layout-046"><label>種別<select name="type" class="surface-017">@foreach($typeLabels as $value=>$label)<option value="{{ $value }}" @selected($selectedInterview->type===$value)>{{ $label }}</option>@endforeach</select></label><label>方法<select name="method" class="surface-017">@foreach($methodLabels as $value=>$label)<option value="{{ $value }}" @selected($selectedInterview->method===$value)>{{ $label }}</option>@endforeach</select></label><label>場所<input name="location" value="{{ $selectedInterview->location }}" class="surface-034"></label><label>日時<input name="scheduled_at" type="datetime-local" value="{{ $selectedInterview->scheduled_at?->format('Y-m-d\TH:i') }}" class="surface-034"></label></div><label class="layout-044">話した内容<textarea name="content" class="surface-060">{{ $selectedInterview->content }}</textarea></label><label class="layout-044">次回までの課題・約束<textarea name="next_tasks" class="surface-060">{{ $selectedInterview->next_tasks }}</textarea></label><label class="layout-044">次回の目安<input name="next_timing" value="{{ $selectedInterview->next_timing }}" class="surface-034"></label><button class="control-012">保存する</button></form><div class="layout-109"><div class="text-120">この記録を開いた人</div>@forelse($auditAccessLogs as $accessLog)<div>{{ $accessLog->user?->name ?? '不明' }}・{{ $accessLog->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</div>@empty<div>閲覧履歴はありません。</div>@endforelse</div>@else<h2 id="cf-detail" class="text-020">面談を選択してください</h2>@endif</section></div>
              @else
              <div class="layout-013">
                <div>
                  <h1 class="text-044">面談の予定・記録</h1>
                  <div class="text-060">担当の社員が、いつ誰とどんな面談・指導をしたかを記録します。社員だけが開けます。</div>
                </div>
                <button type="button" aria-expanded="false" class="control-011">+ 面談を設定</button></div>
              <div role="note" class="layout-109"><span class="utility-007"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg></span>
                <div>面談の記録は社員全員が見られます(準社員・スタッフは開けません)。心の不調などの内容を含むため、記録を開いた人と日時は残ります。</div>
              </div>
              <section aria-labelledby="cf-plan" class="surface-006">
                <h2 id="cf-plan" class="text-020">これからの面談</h2>
                <div class="utility-001">
                  <table class="text-074">
                    <thead>
                      <tr class="text-023"><th class="cell-001">日時</th><th class="cell-002">相手</th><th class="cell-002">種別・方法</th><th class="cell-002">担当</th><th class="cell-002">状態</th></tr>
                    </thead>
                    <tbody>
                      <tr><td class="cell-003"><span>10/27(火) 18:00</span></td><td class="cell-047"><span>小林 さくら</span></td><td class="cell-004"><span>定期面談・オンライン</span></td><td class="cell-005"><span>山田 太郎</span></td><td class="cell-005"><span class="surface-019"><span>確定</span></span></td></tr>
                      <tr><td class="cell-003"><span>候補3つを送信済み</span></td><td class="cell-047"><span>中村 翔</span></td><td class="cell-004"><span>個別指導・対面</span></td><td class="cell-005"><span>山田 太郎</span></td><td class="cell-005"><span class="surface-033"><span>調整中</span></span></td></tr>
                      <tr><td class="cell-003"><span>11/4(水) 17:30</span></td><td class="cell-047"><span>鈴木 一郎</span></td><td class="cell-004"><span>定期面談・対面</span></td><td class="cell-005"><span>田村 優子</span></td><td class="cell-005"><span class="surface-019"><span>確定</span></span></td></tr>
                    </tbody>
                  </table>
                </div>
              </section>
              <div class="layout-066">
                <section aria-labelledby="cf-log" class="surface-057">
                  <div class="layout-041">
                    <h2 id="cf-log" class="text-020">面談の記録</h2>
                    <input type="text" aria-label="相手の名前で探す" placeholder="相手の名前で探す" class="surface-058"></div>
                  <div class="utility-001">
                    <table class="text-077">
                      <thead>
                        <tr class="text-023"><th class="cell-001">日付</th><th class="cell-002">相手</th><th class="cell-002">種別</th><th class="cell-002">担当</th><th class="cell-002"><span class="space-004">操作</span></th></tr>
                      </thead>
                      <tbody>
                        <tr class="utility-010"><td class="cell-003"><span>10/14(水)</span></td><td class="cell-047"><span>中村 翔</span></td><td class="cell-005"><span>個別指導</span></td><td class="cell-005"><span>山田 太郎</span></td><td class="cell-006"><button type="button" class="control-012">内容を見る</button></td></tr>
                        <tr class="utility-011"><td class="cell-003"><span>10/9(金)</span></td><td class="cell-047"><span>小林 さくら</span></td><td class="cell-005"><span>定期面談</span></td><td class="cell-005"><span>山田 太郎</span></td><td class="cell-006"><button type="button" class="control-012">内容を見る</button></td></tr>
                        <tr class="utility-011"><td class="cell-003"><span>10/2(金)</span></td><td class="cell-047"><span>鈴木 一郎</span></td><td class="cell-005"><span>相談対応</span></td><td class="cell-005"><span>田村 優子</span></td><td class="cell-006"><button type="button" class="control-012">内容を見る</button></td></tr>
                        <tr class="utility-011"><td class="cell-003"><span>9/25(金)</span></td><td class="cell-047"><span>中村 翔</span></td><td class="cell-005"><span>定期面談</span></td><td class="cell-005"><span>山田 太郎</span></td><td class="cell-006"><button type="button" class="control-012">内容を見る</button></td></tr>
                      </tbody>
                    </table>
                  </div>
                </section>
                <section aria-labelledby="cf-detail" class="surface-059">
                  <h2 id="cf-detail" class="text-020"><span>中村 翔</span> さんとの面談</h2>
                  <div class="text-060"><span>10/14(水)</span> ・ <span>個別指導</span> ・ <span>対面(ハイクラス 事務所)</span> ・ 担当 <span>山田 太郎</span></div>
                  <div class="layout-110">
                    <div class="layout-044">話した内容<div class="surface-060"><span>集合時間に遅れが続いた件。原因は前日の確認不足。7日前確認のリンクを押したあと、地図を前日に見ておくことを一緒に決めた。</span></div>
                    </div>
                    <div class="layout-044">次回までの課題・約束<div class="surface-060"><span>次の3回は前日夜に集合場所を確認して、LINEで一言送る</span></div>
                    </div>
                    <div class="layout-111"><span class="utility-002">次回の目安</span><span class="text-067"><span>11月中旬</span></span></div>
                  </div>
                  <div class="surface-061">
                    <div class="text-120">この記録を開いた人</div>
                    <span>10/15 9:12 田村 優子 ・ 10/14 19:25 山田 太郎</span></div>
                  <div class="layout-112"><span class="text-002">最終更新 <span>10/14 19:20</span></span><button type="button" class="control-012">編集する</button></div>
                </section>
              </div>
              @endif
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
