<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>スタッフ一覧</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--stafflist" data-static-screen="StaffList">
    <div class="app-root">
      <div class="screen">
        <div class="layout-061">
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
              <a href="Main.dc.html" class="layout-010"><span>ホーム</span></a><a href="EventList.dc.html" class="layout-010"><span>イベント</span></a><a href="Assign.dc.html" class="layout-010"><span>アサイン</span></a><a href="Shortage.dc.html" class="layout-010"><span>人数不足一覧</span></a><a href="ReportView.dc.html" class="layout-010"><span>報告書</span></a><a href="#" aria-current="page" class="layout-009"><span>スタッフ</span></a><a href="Clients.dc.html" class="layout-010"><span>依頼先</span><span class="surface-001">共通</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              <div>
                <h1 class="text-044">スタッフ</h1>
                <div class="text-060">社員・準社員・スタッフ・その他(単発・トレーナー)全員の一覧です。名前を押すと詳細が開きます。新しい人の登録は「アカウント管理」から行います。</div>
              </div>
              <section aria-label="スタッフの状況" class="layout-235">
                <div class="surface-030">
                  <div class="text-007">登録人数</div>
                  <div class="text-063">{{ isset($expiringCount) ? $staffMembers->total() : 48 }}<span class="text-010">人</span></div>
                </div>
                <div class="surface-031">
                  <div class="text-011">資格の期限が60日以内</div>
                  <div class="text-064">{{ $expiringCount ?? 2 }}<span class="text-010">人</span></div>
                </div>
              </section>
              <section aria-labelledby="sl-list" class="surface-006">
                @if(isset($expiringCount))<form method="GET" action="{{ route('staff.index') }}" class="layout-071">
                  <div class="layout-051"><label for="sl-q" class="text-002">名前で探す</label><input id="sl-q" name="keyword" value="{{ request('keyword') }}" type="search" placeholder="名前の一部" class="text-049"></div><div class="layout-051"><label for="sl-role" class="text-002">区分</label><select id="sl-role" name="role" class="surface-017"><option value="">すべて</option>@foreach(['employee'=>'社員','semi_employee'=>'準社員','staff'=>'スタッフ','temporary'=>'その他(単発)','trainer'=>'その他(トレーナー)'] as $value=>$label)<option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>@endforeach</select></div><label class="control-010"><input name="student" value="1" type="checkbox" @checked(request()->boolean('student')) class="space-006">学生</label><label class="control-010"><input name="has_car" value="1" type="checkbox" @checked(request()->boolean('has_car')) class="space-006">車あり</label><button type="submit" class="control-024">検索</button></form>@else<div class="layout-071">
                  <div class="layout-051"><label for="sl-q" class="text-002">名前で探す</label><input id="sl-q" type="text" placeholder="名前の一部" class="text-049"></div>
                  <div class="layout-051"><label for="sl-role" class="text-002">区分</label><select id="sl-role" class="surface-017"><option>すべて</option><option>社員</option><option>準社員</option><option>スタッフ</option><option>その他(単発)</option><option>その他(トレーナー)</option></select></div>
                  <label class="control-010"><input type="checkbox" class="space-006">学生</label><label class="control-010"><input type="checkbox" class="space-006">車あり</label></div>@endif
                <h2 id="sl-list" class="text-233"><span>{{ isset($expiringCount) ? $staffMembers->total() : 9 }}</span>人</h2>
                <div class="utility-024">
                  <table class="text-069">
                    <thead>
                      <tr class="text-023"><th class="cell-001">名前</th><th class="cell-002">最寄り駅</th><th class="cell-002">携帯電話</th><th class="cell-002">車</th><th class="cell-002">LINE</th><th class="cell-092">今年の勤務</th><th class="cell-002">資格の期限</th><th class="cell-020"><span class="space-004">操作</span></th></tr>
                    </thead>
                    <tbody>
                      @if(isset($expiringCount))
                      @php $roleLabels=['employee'=>'社員','semi_employee'=>'準社員','staff'=>'スタッフ','temporary'=>'その他(単発)','trainer'=>'その他(トレーナー)']; @endphp
                      @forelse($staffMembers as $listedStaff)
                      @php $expiring=$listedStaff->qualifications->first(fn($qualification) => $qualification->expires_at && $qualification->expires_at->between(today(), today()->addDays(60))); @endphp
                      <tr><td class="cell-093"><a href="{{ route('staff.show', $listedStaff) }}" class="text-072">{{ $listedStaff->name }}</a><div class="layout-236"><span class="surface-144">{{ $roleLabels[$listedStaff->role] ?? $listedStaff->role }}</span>@if($listedStaff->is_student)<span class="surface-145">学生</span>@endif</div></td><td class="cell-005">{{ $listedStaff->nearest_station ?: '—' }}</td><td class="cell-005">{{ $listedStaff->phone ?: '—' }}</td><td class="cell-005">{{ $listedStaff->has_car ? 'あり' : 'なし' }}</td><td class="cell-005"><span class="{{ $listedStaff->line_user_id ? 'surface-032' : 'surface-027' }}">{{ $listedStaff->line_user_id ? '連携済み' : '未連携' }}</span></td><td class="cell-094">{{ $listedStaff->year_work_count }}回</td><td class="{{ $expiring ? 'cell-096' : 'cell-095' }}">{{ $expiring ? $expiring->name.' '.$expiring->expires_at->format('Y/m/d') : '—' }}</td><td class="cell-013"><a href="{{ route('staff.show', $listedStaff) }}" class="text-073">詳細を見る</a></td></tr>
                      @empty<tr><td colspan="8" class="cell-093">条件に一致するスタッフはいません。</td></tr>@endforelse
                      @else
                      <tr><td class="cell-093"><a href="StaffDetail.dc.html" class="text-072"><span>佐藤 花子</span></a>
                        <div class="layout-236"><span class="surface-144"><span>スタッフ</span></span><span class="surface-145">学生</span></div>
                        </td><td class="cell-005"><span>池袋駅</span></td><td class="cell-005"><span>090-XXXX-XXXX</span></td><td class="cell-005"><span>なし</span></td><td class="cell-005"><span class="surface-032"><span>連携済み</span></span></td><td class="cell-094"><span>14回</span></td><td class="cell-095"><span>—</span></td><td class="cell-013"><a href="StaffDetail.dc.html" class="text-073">詳細を見る</a></td></tr>
                      <tr><td class="cell-093"><a href="StaffDetail.dc.html" class="text-072"><span>鈴木 一郎</span></a>
                        <div class="layout-236"><span class="surface-146"><span>準社員</span></span></div>
                        </td><td class="cell-005"><span>中野駅</span></td><td class="cell-005"><span>080-XXXX-XXXX</span></td><td class="cell-005"><span>あり</span></td><td class="cell-005"><span class="surface-032"><span>連携済み</span></span></td><td class="cell-094"><span>22回</span></td><td class="cell-095"><span>—</span></td><td class="cell-013"><a href="StaffDetail.dc.html" class="text-073">詳細を見る</a></td></tr>
                      <tr><td class="cell-093"><a href="StaffDetail.dc.html" class="text-072"><span>田中 健</span></a>
                        <div class="layout-236"><span class="surface-144"><span>スタッフ</span></span></div>
                        </td><td class="cell-005"><span>高田馬場駅</span></td><td class="cell-005"><span>090-XXXX-XXXX</span></td><td class="cell-005"><span>あり</span></td><td class="cell-005"><span class="surface-032"><span>連携済み</span></span></td><td class="cell-094"><span>18回</span></td><td class="cell-096"><span>普通自動車免許 2026/11/08</span></td><td class="cell-013"><a href="StaffDetail.dc.html" class="text-073">詳細を見る</a></td></tr>
                      <tr><td class="cell-093"><a href="StaffDetail.dc.html" class="text-072"><span>高橋 美咲</span></a>
                        <div class="layout-236"><span class="surface-144"><span>スタッフ</span></span><span class="surface-145">学生</span></div>
                        </td><td class="cell-005"><span>大塚駅</span></td><td class="cell-005"><span>080-XXXX-XXXX</span></td><td class="cell-005"><span>なし</span></td><td class="cell-005"><span class="surface-027"><span>未連携</span></span></td><td class="cell-094"><span>6回</span></td><td class="cell-095"><span>—</span></td><td class="cell-013"><a href="StaffDetail.dc.html" class="text-073">詳細を見る</a></td></tr>
                      <tr><td class="cell-093"><a href="StaffDetail.dc.html" class="text-072"><span>伊藤 愛</span></a>
                        <div class="layout-236"><span class="surface-144"><span>スタッフ</span></span></div>
                        </td><td class="cell-005"><span>目白駅</span></td><td class="cell-005"><span>090-XXXX-XXXX</span></td><td class="cell-005"><span>なし</span></td><td class="cell-005"><span class="surface-027"><span>未連携</span></span></td><td class="cell-094"><span>9回</span></td><td class="cell-096"><span>防災士 2026/11/30</span></td><td class="cell-013"><a href="StaffDetail.dc.html" class="text-073">詳細を見る</a></td></tr>
                      <tr><td class="cell-093"><a href="StaffDetail.dc.html" class="text-072"><span>前田 由紀</span></a>
                        <div class="layout-236"><span class="surface-147"><span>その他(トレーナー)</span></span></div>
                        </td><td class="cell-005"><span>恵比寿駅</span></td><td class="cell-005"><span>080-XXXX-XXXX</span></td><td class="cell-005"><span>あり</span></td><td class="cell-005"><span class="surface-032"><span>連携済み</span></span></td><td class="cell-094"><span>7回</span></td><td class="cell-095"><span>健康運動指導士</span></td><td class="cell-013"><a href="StaffDetail.dc.html" class="text-073">詳細を見る</a></td></tr>
                      <tr><td class="cell-093"><a href="StaffDetail.dc.html" class="text-072"><span>小川 拓也</span></a>
                        <div class="layout-236"><span class="surface-148"><span>その他(単発)</span></span></div>
                        </td><td class="cell-005"><span>大宮駅</span></td><td class="cell-005"><span>090-XXXX-XXXX</span></td><td class="cell-005"><span>なし</span></td><td class="cell-005"><span class="surface-032"><span>連携済み</span></span></td><td class="cell-094"><span>1回</span></td><td class="cell-095"><span>—</span></td><td class="cell-013"><a href="StaffDetail.dc.html" class="text-073">詳細を見る</a></td></tr>
                      <tr><td class="cell-093"><a href="StaffDetail.dc.html" class="text-072"><span>佐々木 恵</span></a>
                        <div class="layout-236"><span class="surface-149"><span>社員</span></span></div>
                        </td><td class="cell-005"><span>新宿駅</span></td><td class="cell-005"><span>080-XXXX-XXXX</span></td><td class="cell-005"><span>あり</span></td><td class="cell-005"><span class="surface-032"><span>連携済み</span></span></td><td class="cell-094"><span>11回</span></td><td class="cell-095"><span>—</span></td><td class="cell-013"><a href="StaffDetail.dc.html" class="text-073">詳細を見る</a></td></tr>
                      <tr><td class="cell-093"><a href="StaffDetail.dc.html" class="text-072"><span>田村 優子</span></a>
                        <div class="layout-236"><span class="surface-149"><span>社員</span></span></div>
                        </td><td class="cell-005"><span>荻窪駅</span></td><td class="cell-005"><span>090-XXXX-XXXX</span></td><td class="cell-005"><span>なし</span></td><td class="cell-005"><span class="surface-032"><span>連携済み</span></span></td><td class="cell-094"><span>8回</span></td><td class="cell-095"><span>—</span></td><td class="cell-013"><a href="StaffDetail.dc.html" class="text-073">詳細を見る</a></td></tr>
                      @endif
                    </tbody>
                  </table>
                </div>
                @if(isset($expiringCount)){{ $staffMembers->links() }}@endif
                <div class="text-058">準社員の画面でも同じ一覧が見られます。住所と口座は詳細ページでも準社員には表示されません。</div>
              </section>
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
