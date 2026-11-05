<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>アカウント管理</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--accounts" data-static-screen="Accounts">
    <div class="app-root">
      <div class="screen">
        <div class="layout-061">
          <div class="layout-002">
            <nav aria-label="エリア切り替え" class="layout-003">
              <div aria-hidden="true" class="layout-004">HC</div>
              <a href="Main.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="16" y1="3" x2="16" y2="7"></line></svg><span class="text-001">事業</span></a><a href="SalesHome.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 17 9 11 13 15 21 7"></polyline><polyline points="14 7 21 7 21 14"></polyline></svg><span class="text-001">営業</span></a><a href="Coaching.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H9l-4 3v-3a2 2 0 0 1-2-2z"></path><path d="M19 9a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2v3l-4-3h-3"></path></svg><span class="text-001">面談</span></a><a href="PhoneLog.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><rect x="9" y="2" width="6" height="4" rx="1"></rect><line x1="9" y1="11" x2="15" y2="11"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg><span class="text-001">総務</span></a><a href="PayManage.dc.html" class="layout-006"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4l5 7 5-7"></path><line x1="12" y1="11" x2="12" y2="20"></line><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg><span class="text-001">会計</span></a>
              <div class="layout-007"><a href="#" aria-current="page" class="layout-005"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"></line><circle cx="9" cy="7" r="2.2" fill="#16213A"></circle><line x1="4" y1="17" x2="20" y2="17"></line><circle cx="15" cy="17" r="2.2" fill="#16213A"></circle></svg><span class="text-001">設定</span></a></div>
            </nav>
            <nav aria-label="メインメニュー" class="layout-008">
              <div class="space-001">
                <div class="text-002">ハイクラス業務管理システム</div>
                <div class="text-003">設定</div>
              </div>
              <a href="#" aria-current="page" class="layout-009"><span>アカウント管理</span></a><a href="SendLog.dc.html" class="layout-010"><span>送信履歴</span></a><a href="Staff.dc.html" aria-label="山田 太郎(社員)の自分のページ(自分への依頼・支払明細・マイプロフィール)" class="layout-011"><span>山田 太郎(社員)</span><span class="text-004">自分のページを開く ›</span></a></nav>
          </div>
          <main class="space-005">
            <div class="layout-040">
              <div class="layout-013">
                <div>
                  <h1 class="text-044">アカウント管理</h1>
                  <div class="text-060">アプリを使う人のアカウントの作成・招待、区分の変更、ログインの停止・再開を行います。単発の方は区分「その他(単発)」で「ログインなし」として登録し、連絡は公式LINEで届きます。トレーナーは「その他(トレーナー)」で、スタッフと同じように招待してログインしてもらいます。社員だけが使える画面です。</div>
                </div>
                <button type="button" data-create-account class="control-014">+ アカウントを作成して招待</button></div>
              <section aria-labelledby="ac-list" class="surface-006">
                <div class="layout-041">
                  <h2 id="ac-list" class="text-020">アカウント一覧(<span>{{ $staffMembers->count() }}</span>件)</h2>
                  <div role="group" aria-label="状態で絞り込み" class="layout-024"><button type="button" aria-pressed="true" class="control-009"><span>すべて</span></button><button type="button" aria-pressed="false" class="control-008"><span>利用中</span></button><button type="button" aria-pressed="false" class="control-008"><span>招待中</span></button><button type="button" aria-pressed="false" class="control-008"><span>停止中</span></button><button type="button" aria-pressed="false" class="control-008"><span>ログインなし</span></button></div>
                </div>
                <div class="utility-001">
                  <table class="text-081">
                    <thead>
                      <tr class="text-023"><th class="cell-001">名前</th><th class="cell-002">メールアドレス・連絡先</th><th class="cell-010">区分</th><th class="cell-024">状態</th><th class="cell-024">LINE連携</th><th class="cell-024">最終ログイン</th><th class="cell-026">操作</th></tr>
                    </thead>
                    <tbody>
                      @php
                        $roleLabels = ['employee' => '社員', 'semi_employee' => '準社員', 'staff' => 'スタッフ', 'temporary' => 'その他(単発)', 'trainer' => 'その他(トレーナー)'];
                        $statusLabels = ['active' => '利用中', 'invited' => '招待中', 'suspended' => '停止中', 'no_login' => 'ログインなし'];
                        $statusClasses = ['active' => 'surface-012', 'invited' => 'surface-013', 'suspended' => 'surface-016', 'no_login' => 'surface-015'];
                      @endphp
                      @forelse ($staffMembers as $member)
                        <tr class="{{ $member->account_status === 'suspended' ? 'utility-008' : 'utility-004' }}" data-account-row data-account-status="{{ $member->account_status }}">
                          <td class="cell-027"><span>{{ $member->name }}</span></td>
                          <td class="cell-028"><span>{{ $member->email }}</span></td>
                          <td class="cell-004"><select aria-label="{{ $member->name }}の区分" data-account-role data-user-id="{{ $member->id }}" class="surface-035" @disabled($member->is(auth()->user()))>@foreach ($roleLabels as $value => $label)<option value="{{ $value }}" @selected($member->role === $value)>{{ $label }}</option>@endforeach</select></td>
                          <td class="cell-028"><span class="{{ $statusClasses[$member->account_status] ?? 'surface-011' }}"><span>{{ $statusLabels[$member->account_status] ?? $member->account_status }}</span></span></td>
                          <td class="cell-028"><span class="{{ $member->line_linked_at ? 'surface-012' : 'surface-011' }}"><span>{{ $member->line_linked_at ? '連携済み' : '未連携' }}</span></span></td>
                          <td class="cell-029"><span>{{ $member->last_login_at?->timezone('Asia/Tokyo')->format('Y/m/d H:i') ?? '—' }}</span></td>
                          <td class="cell-013">
                            @if (! $member->is(auth()->user()) && $member->account_status === 'invited')<button type="button" data-account-state="invited" data-user-id="{{ $member->id }}" class="control-016">招待を再送</button>@endif
                            @if (! $member->is(auth()->user()) && in_array($member->account_status, ['active', 'invited', 'no_login'], true))<button type="button" data-account-state="suspended" data-user-id="{{ $member->id }}" class="control-015">{{ $member->role === 'temporary' ? '利用停止' : 'ログイン停止' }}</button>@endif
                            @if (! $member->is(auth()->user()) && $member->account_status === 'suspended')<button type="button" data-account-state="{{ $member->role === 'temporary' ? 'no_login' : 'active' }}" data-user-id="{{ $member->id }}" class="control-017">再開</button>@endif
                          </td>
                        </tr>
                      @empty
                        <tr><td colspan="7" class="cell-028">アカウントはまだありません。</td></tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
                <div class="text-082">ログインを停止しても、これまでの勤務・支払・報告書の記録は残ります。退職した人は「ログイン停止」にしてください。自分自身の区分と停止は変更できません。「その他(単発)」の人をスタッフ・その他(トレーナー)など、ログインする区分に変えると、その時点で招待メールが届きます(メールアドレスが必要です)。LINE連携が「未連携」の人には、マイプロフィールの「LINEと連携する」から連携するよう声をかけてください。勤務・支払・報告書の記録はそのまま引き継がれます。</div>
              </section>
              <dialog id="account-create-dialog" class="surface-006">
                <form method="dialog" data-account-create-form class="layout-158">
                  <h2 class="text-020">アカウントを作成して招待</h2>
                  <div class="layout-055"><label class="text-042" for="account-name">名前</label><input id="account-name" name="name" required maxlength="255" class="text-076"></div>
                  <div class="layout-055"><label class="text-042" for="account-email">メールアドレス</label><input id="account-email" name="email" type="email" required maxlength="255" class="text-076"></div>
                  <div class="layout-055"><label class="text-042" for="account-role">区分</label><select id="account-role" name="role" required class="text-076"><option value="employee">社員</option><option value="semi_employee">準社員</option><option value="staff" selected>スタッフ</option><option value="temporary">その他(単発)</option><option value="trainer">その他(トレーナー)</option></select></div>
                  <div class="layout-024"><button type="button" data-close-account-dialog class="control-015">キャンセル</button><button type="submit" class="control-014">作成して招待</button></div>
                </form>
              </dialog>
            </div>
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
