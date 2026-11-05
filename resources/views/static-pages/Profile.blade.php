<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>マイプロフィール</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
  @vite(['resources/css/static-design.css', 'resources/js/static-design.js'])
</head>
<body class="page page--profile" data-static-screen="Profile">
    <div class="app-root">
      <div class="screen">
        <div class="layout-174">
          <header class="layout-030">
            <div class="text-035">ハイクラス業務管理システム</div>
            <div class="text-002">{{ isset($dynamicProfile) ? $staffMember->name : '佐藤 花子' }}(スタッフ)</div>
          </header>
          <main class="layout-057">
            @if(isset($dynamicProfile))
            @php $slots=['8時前','8〜10時','10〜12時','12〜13時','13〜15時','15〜17時','17〜19時','19時以降']; $weekdays=[1=>'月',2=>'火',3=>'水',4=>'木',5=>'金',6=>'土',0=>'日']; $availabilityMap=$staffMember->availabilities->keyBy(fn($item)=>$item->weekday.'|'.$item->time_slot); $bank=$staffMember->bank_details ?? []; @endphp
            <div class="layout-063"><div><h1 class="text-036">マイプロフィール</h1><div class="text-037">登録内容を確認し、変更後に保存してください</div></div></div>
            @if(session('status'))<div role="status" class="surface-045">{{ session('status') }}</div>@endif @if($errors->any())<div role="alert" class="surface-045">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('profile.update') }}" class="layout-175">@csrf @method('PATCH')
              <section aria-labelledby="pf-basic" class="layout-060"><h2 id="pf-basic" class="text-014">基本情報</h2><div class="layout-051"><label for="pf-name" class="text-002">名前</label><input id="pf-name" name="name" type="text" value="{{ old('name',$staffMember->name) }}" required class="text-182"></div><div class="layout-176"><div class="layout-051"><label for="pf-gender" class="text-002">性別</label><select id="pf-gender" name="gender" class="surface-090"><option value="">未回答</option><option value="male" @selected(old('gender',$staffMember->gender)==='male')>男性</option><option value="female" @selected(old('gender',$staffMember->gender)==='female')>女性</option><option value="other" @selected(old('gender',$staffMember->gender)==='other')>回答しない</option></select></div><div class="layout-051"><label for="pf-birth" class="text-002">生年月日</label><input id="pf-birth" name="birth_date" type="date" value="{{ old('birth_date',$staffMember->birth_date?->format('Y-m-d')) }}" class="text-183"></div></div><label class="control-053"><input name="is_student" value="1" type="checkbox" @checked(old('is_student',$staffMember->is_student)) class="space-009">学生</label><div class="layout-051"><label for="pf-station" class="text-002">最寄り駅</label><input id="pf-station" name="nearest_station" value="{{ old('nearest_station',$staffMember->nearest_station) }}" class="text-182"></div></section>
              <section aria-labelledby="pf-contact" class="layout-060"><h2 id="pf-contact" class="text-014">住所・連絡先</h2><div class="layout-051"><label for="pf-zip" class="text-002">郵便番号</label><input id="pf-zip" name="postal_code" value="{{ old('postal_code',$staffMember->postal_code) }}" class="text-184"></div><div class="layout-051"><label for="pf-addr" class="text-002">住所</label><input id="pf-addr" name="address" value="{{ old('address',$staffMember->address) }}" class="text-182"></div><div class="layout-051"><label for="pf-bldg" class="text-002">ビル名・部屋番号</label><input id="pf-bldg" name="building" value="{{ old('building',$staffMember->building) }}" class="text-182"></div><div class="layout-051"><label for="pf-tel" class="text-002">携帯電話</label><input id="pf-tel" name="phone" type="tel" value="{{ old('phone',$staffMember->phone) }}" class="text-182"></div><div class="layout-051"><label for="pf-mail1" class="text-002">メール1</label><input id="pf-mail1" name="email" type="email" value="{{ old('email',$staffMember->email) }}" required class="text-182"></div><div class="layout-051"><label for="pf-mail2" class="text-002">メール2</label><input id="pf-mail2" name="secondary_email" type="email" value="{{ old('secondary_email',$staffMember->secondary_email) }}" class="text-182"></div><div class="layout-178"><label class="control-055"><input type="radio" name="notification_method" value="line" @checked(old('notification_method',$staffMember->notification_method)==='line') @disabled(!$staffMember->line_user_id) class="space-009">LINE</label><label class="control-055"><input type="radio" name="notification_method" value="email" @checked(old('notification_method',$staffMember->notification_method)!=='line') class="space-009">メール</label></div></section>
              <section aria-labelledby="pf-line" class="layout-060"><h2 id="pf-line" class="text-014">LINE連携・通知方法</h2><div class="layout-177"><div class="text-185">{{ $staffMember->line_linked_at ? 'LINE連携済みです。依頼や出勤確認をLINEで受け取れます。' : '公式LINEと連携すると、依頼や出勤確認の連絡がLINEに届きます。' }}</div><button type="submit" form="{{ $staffMember->line_linked_at ? 'line-disconnect-form' : 'line-connect-form' }}" class="control-054">{{ $staffMember->line_linked_at ? 'LINE連携を解除する' : 'LINEと連携する' }}</button></div></section>
              <section aria-labelledby="pf-car" class="layout-060"><h2 id="pf-car" class="text-014">車</h2><label class="control-055"><input type="radio" name="has_car" value="1" @checked(old('has_car',$staffMember->has_car))>あり</label><label class="control-055"><input type="radio" name="has_car" value="0" @checked(!old('has_car',$staffMember->has_car))>なし</label><div class="layout-051"><label for="pf-plate" class="text-002">ナンバー</label><input id="pf-plate" name="car_number" value="{{ old('car_number',$staffMember->car_number) }}" class="text-182"></div></section>
              <section aria-labelledby="pf-time" class="layout-060"><h2 id="pf-time" class="text-014">対応可能時間</h2><div role="table" class="layout-180"><div></div>@foreach($weekdays as $label)<div class="text-183">{{ $label }}</div>@endforeach @foreach($slots as $slot)<div class="text-183">{{ $slot }}</div>@foreach($weekdays as $weekday=>$label)@php $value=old('availabilities.'.$weekday.'|'.$slot,$availabilityMap->get($weekday.'|'.$slot)?->availability ?? 'unknown'); @endphp<select name="availabilities[{{ $weekday }}|{{ $slot }}]" aria-label="{{ $label }}曜 {{ $slot }}" class="{{ ['available'=>'control-058','unknown'=>'control-057','unavailable'=>'control-056'][$value] }}"><option value="available" @selected($value==='available')>可</option><option value="unknown" @selected($value==='unknown')>未定</option><option value="unavailable" @selected($value==='unavailable')>不可</option></select>@endforeach @endforeach</div></section>
              <section aria-labelledby="pf-bank" class="layout-060"><h2 id="pf-bank" class="text-014">振込先口座</h2><div class="layout-051"><label>銀行名</label><input name="bank_name" value="{{ old('bank_name',data_get($bank,'bank_name')) }}" class="text-182"></div><div class="layout-051"><label>銀行番号</label><input name="bank_code" value="{{ old('bank_code',data_get($bank,'bank_code')) }}" class="text-182"></div><div class="layout-051"><label>支店名</label><input name="branch_name" value="{{ old('branch_name',data_get($bank,'branch_name')) }}" class="text-182"></div><div class="layout-051"><label>支店番号</label><input name="branch_code" value="{{ old('branch_code',data_get($bank,'branch_code')) }}" class="text-182"></div><div class="layout-051"><label>種別</label><select name="account_type" class="surface-094"><option value="ordinary" @selected(old('account_type',data_get($bank,'account_type'))==='ordinary')>普通</option><option value="current" @selected(old('account_type',data_get($bank,'account_type'))==='current')>当座</option></select></div><div class="layout-051"><label>口座番号</label><input name="account_number" value="{{ old('account_number',data_get($bank,'account_number')) }}" class="text-182"></div><div class="layout-051"><label>口座名義(カナ)</label><input name="account_name" value="{{ old('account_name',data_get($bank,'account_name')) }}" class="text-182"></div></section>
              <button type="submit" class="control-004">プロフィールを保存する</button>
            </form>
            @if($staffMember->line_linked_at)<form id="line-disconnect-form" method="POST" action="{{ route('line.connect.disconnect') }}">@csrf @method('DELETE')</form>@else<form id="line-connect-form" method="POST" action="{{ route('line.connect.start') }}">@csrf</form>@endif
            <section aria-labelledby="pf-qual" class="layout-060"><h2 id="pf-qual" class="text-014">資格</h2>@foreach($staffMember->qualifications as $qualification)<div class="layout-183"><div>{{ $qualification->name }} ・ 期限 {{ $qualification->expires_at?->format('Y/m/d') ?? 'なし' }}</div><div>@if($qualification->certificate_path)<a href="{{ route('staff.qualifications.file',[$staffMember,$qualification]) }}" class="text-190">資格証を開く</a>@endif<form method="POST" action="{{ route('staff.qualifications.destroy',[$staffMember,$qualification]) }}">@csrf @method('DELETE')<button type="submit" class="control-001" data-confirm="この資格を削除しますか?">削除</button></form></div></div>@endforeach<form method="POST" action="{{ route('staff.qualifications.store',$staffMember) }}" enctype="multipart/form-data" class="layout-183">@csrf<input name="name" placeholder="資格名" required class="text-182"><input name="expires_at" type="date" class="text-183"><input name="certificate" type="file" accept=".pdf,.jpg,.jpeg,.png"><button type="submit" class="control-060">+ 資格を追加</button></form></section>
            <section aria-labelledby="pf-contract" class="layout-060"><h2 id="pf-contract" class="text-014">契約書</h2><div class="text-187">社員が添付します。ご本人は閲覧・ダウンロードのみできます。</div>@if($staffMember->contract_path)<a href="{{ route('staff.contract.file',$staffMember) }}" class="text-190">契約書を開く</a>@else<div>契約書は未添付です。</div>@endif</section>
            @else
            <div class="layout-063">
              <div>
                <h1 class="text-036">マイプロフィール</h1>
                <div class="text-037">変更するときは「編集する」を押してください</div>
              </div>
              <button type="button" class="control-052">編集する</button></div>
            <fieldset disabled="" class="layout-175">
              <section aria-labelledby="pf-basic" class="layout-060">
                <h2 id="pf-basic" class="text-014">基本情報</h2>
                <div class="layout-051"><label for="pf-name" class="text-002">名前</label><input id="pf-name" type="text" value="佐藤 花子" class="text-182"></div>
                <div class="layout-176">
                  <div class="layout-051"><label for="pf-gender" class="text-002">性別</label><select id="pf-gender" class="surface-090"><option>男性</option><option>女性</option><option>回答しない</option></select></div>
                  <div class="layout-051"><label for="pf-birth" class="text-002">生年月日(21歳)</label><input id="pf-birth" type="date" value="2005-04-12" class="text-183"></div>
                </div>
                <label class="control-053"><input type="checkbox" checked="" class="space-009">学生</label>
                <div class="layout-051"><label for="pf-station" class="text-002">最寄り駅</label><input id="pf-station" type="text" value="池袋駅" class="text-182"></div>
              </section>
              <section aria-labelledby="pf-contact" class="layout-060">
                <h2 id="pf-contact" class="text-014">住所・連絡先</h2>
                <div class="layout-051"><label for="pf-zip" class="text-002">郵便番号</label><input id="pf-zip" type="text" inputmode="numeric" value="171-0021" class="text-184"></div>
                <div class="layout-051"><label for="pf-addr" class="text-002">住所</label><input id="pf-addr" type="text" value="東京都豊島区西池袋〇-〇-〇" class="text-182"></div>
                <div class="layout-051"><label for="pf-bldg" class="text-002">ビル名・部屋番号</label><input id="pf-bldg" type="text" value="サンプルハイツ 201" class="text-182"></div>
                <div class="layout-051"><label for="pf-tel" class="text-002">携帯電話</label><input id="pf-tel" type="tel" value="090-XXXX-XXXX" class="text-182"></div>
                <div class="layout-051"><label for="pf-mail1" class="text-002">メール1(依頼・確認メールが届きます)</label><input id="pf-mail1" type="email" value="hanako@example.com" class="text-182"></div>
                <div class="layout-051"><label for="pf-mail2" class="text-002">メール2</label><input id="pf-mail2" type="email" placeholder="予備のメールアドレス" class="text-182"></div>
              </section>
              <section aria-labelledby="pf-line" class="layout-060">
                <h2 id="pf-line" class="text-014">LINE連携・通知方法</h2>
                <div class="layout-177">
                  <div class="text-185">公式LINEを友だち追加してから連携すると、依頼や出勤確認の連絡がLINEに届きます。</div>
                  <button type="button" class="control-054">LINEと連携する</button></div>
                <fieldset class="space-007"><legend class="text-186">連絡の受け取り方</legend>
                  <div class="layout-178"><label class="control-055"><input type="radio" name="notify" disabled="" class="space-009">LINE</label><label class="control-055"><input type="radio" name="notify" checked="" class="space-009">メール</label></div>
                  <div class="text-002">LINEと連携していない場合は、メール1に届きます。</div>
                </fieldset>
              </section>
              <section aria-labelledby="pf-car" class="layout-060">
                <h2 id="pf-car" class="text-014">車</h2>
                <fieldset class="space-007"><legend class="text-186">車の有無</legend>
                  <div class="layout-179"><label class="control-055"><input type="radio" name="car" checked="" class="space-009">あり</label><label class="control-055"><input type="radio" name="car" class="space-009">なし</label></div>
                </fieldset>
                <div class="layout-051"><label for="pf-plate" class="text-002">ナンバー</label><input id="pf-plate" type="text" value="品川 な 2040" class="text-182"></div>
              </section>
              <section aria-labelledby="pf-time" class="surface-010">
                <h2 id="pf-time" class="text-014">対応可能時間</h2>
                <div class="text-187">マスを押すたびに「可 → 未定 → 不可」と切り替わります</div>
                <div class="layout-180"><span class="utility-005"><span class="surface-091"></span>可</span><span class="utility-005"><span class="surface-092"></span>未定</span><span class="utility-005"><span class="surface-093"></span>不可</span></div>
                <div role="grid" aria-label="曜日と時間帯ごとの対応可否" class="layout-181">
                  <div></div>
                  <div class="text-188">月</div>
                  <div class="text-188">火</div>
                  <div class="text-188">水</div>
                  <div class="text-188">木</div>
                  <div class="text-188">金</div>
                  <div class="text-188">土</div>
                  <div class="text-188">日</div>
                  <div class="layout-064"><span>8時前</span></div>
                  <button type="button" aria-label="月曜 8時前:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="火曜 8時前:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="水曜 8時前:未定" class="control-057"><span>未定</span></button><button type="button" aria-label="木曜 8時前:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="金曜 8時前:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="土曜 8時前:可" class="control-058"><span>可</span></button><button type="button" aria-label="日曜 8時前:可" class="control-058"><span>可</span></button>
                  <div class="layout-064"><span>8〜10時</span></div>
                  <button type="button" aria-label="月曜 8〜10時:可" class="control-058"><span>可</span></button><button type="button" aria-label="火曜 8〜10時:可" class="control-058"><span>可</span></button><button type="button" aria-label="水曜 8〜10時:可" class="control-058"><span>可</span></button><button type="button" aria-label="木曜 8〜10時:可" class="control-058"><span>可</span></button><button type="button" aria-label="金曜 8〜10時:可" class="control-058"><span>可</span></button><button type="button" aria-label="土曜 8〜10時:可" class="control-058"><span>可</span></button><button type="button" aria-label="日曜 8〜10時:可" class="control-058"><span>可</span></button>
                  <div class="layout-064"><span>10〜12時</span></div>
                  <button type="button" aria-label="月曜 10〜12時:可" class="control-058"><span>可</span></button><button type="button" aria-label="火曜 10〜12時:可" class="control-058"><span>可</span></button><button type="button" aria-label="水曜 10〜12時:可" class="control-058"><span>可</span></button><button type="button" aria-label="木曜 10〜12時:可" class="control-058"><span>可</span></button><button type="button" aria-label="金曜 10〜12時:可" class="control-058"><span>可</span></button><button type="button" aria-label="土曜 10〜12時:可" class="control-058"><span>可</span></button><button type="button" aria-label="日曜 10〜12時:可" class="control-058"><span>可</span></button>
                  <div class="layout-064"><span>12〜13時</span></div>
                  <button type="button" aria-label="月曜 12〜13時:可" class="control-058"><span>可</span></button><button type="button" aria-label="火曜 12〜13時:可" class="control-058"><span>可</span></button><button type="button" aria-label="水曜 12〜13時:可" class="control-058"><span>可</span></button><button type="button" aria-label="木曜 12〜13時:可" class="control-058"><span>可</span></button><button type="button" aria-label="金曜 12〜13時:可" class="control-058"><span>可</span></button><button type="button" aria-label="土曜 12〜13時:可" class="control-058"><span>可</span></button><button type="button" aria-label="日曜 12〜13時:可" class="control-058"><span>可</span></button>
                  <div class="layout-064"><span>13〜15時</span></div>
                  <button type="button" aria-label="月曜 13〜15時:可" class="control-058"><span>可</span></button><button type="button" aria-label="火曜 13〜15時:可" class="control-058"><span>可</span></button><button type="button" aria-label="水曜 13〜15時:可" class="control-058"><span>可</span></button><button type="button" aria-label="木曜 13〜15時:可" class="control-058"><span>可</span></button><button type="button" aria-label="金曜 13〜15時:可" class="control-058"><span>可</span></button><button type="button" aria-label="土曜 13〜15時:可" class="control-058"><span>可</span></button><button type="button" aria-label="日曜 13〜15時:可" class="control-058"><span>可</span></button>
                  <div class="layout-064"><span>15〜17時</span></div>
                  <button type="button" aria-label="月曜 15〜17時:可" class="control-058"><span>可</span></button><button type="button" aria-label="火曜 15〜17時:未定" class="control-057"><span>未定</span></button><button type="button" aria-label="水曜 15〜17時:可" class="control-058"><span>可</span></button><button type="button" aria-label="木曜 15〜17時:未定" class="control-057"><span>未定</span></button><button type="button" aria-label="金曜 15〜17時:可" class="control-058"><span>可</span></button><button type="button" aria-label="土曜 15〜17時:可" class="control-058"><span>可</span></button><button type="button" aria-label="日曜 15〜17時:可" class="control-058"><span>可</span></button>
                  <div class="layout-064"><span>17〜19時</span></div>
                  <button type="button" aria-label="月曜 17〜19時:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="火曜 17〜19時:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="水曜 17〜19時:未定" class="control-057"><span>未定</span></button><button type="button" aria-label="木曜 17〜19時:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="金曜 17〜19時:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="土曜 17〜19時:可" class="control-058"><span>可</span></button><button type="button" aria-label="日曜 17〜19時:未定" class="control-057"><span>未定</span></button>
                  <div class="layout-064"><span>19時以降</span></div>
                  <button type="button" aria-label="月曜 19時以降:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="火曜 19時以降:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="水曜 19時以降:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="木曜 19時以降:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="金曜 19時以降:不可" class="control-056"><span>不可</span></button><button type="button" aria-label="土曜 19時以降:未定" class="control-057"><span>未定</span></button><button type="button" aria-label="日曜 19時以降:不可" class="control-056"><span>不可</span></button></div>
              </section>
              <section aria-labelledby="pf-qual" class="layout-060">
                <h2 id="pf-qual" class="text-014">保有資格</h2>
                <div class="layout-182">
                  <div class="layout-032">
                    <div class="text-017">普通自動車免許</div>
                    <button type="button" aria-label="普通自動車免許を削除" class="control-001"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"></path></svg></button></div>
                  <div class="text-053">期限 2029/04/12</div>
                  <div class="layout-183"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1C7A4A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6v18h12V7z"></path><path d="M14 3v4h4"></path></svg><span class="text-189">資格証 添付済み(免許証.jpg)</span></div>
                </div>
                <div class="layout-182">
                  <div class="layout-032">
                    <div class="text-017">防災士</div>
                    <button type="button" aria-label="防災士を削除" class="control-001"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"></path></svg></button></div>
                  <div class="text-011">期限 2026/11/30(まもなく期限切れ)</div>
                  <button type="button" class="control-059">資格証を添付する</button></div>
                <button type="button" class="control-060">+ 資格を追加</button></section>
              <section aria-labelledby="pf-bank" class="layout-060">
                <div>
                  <h2 id="pf-bank" class="text-014">振込先口座</h2>
                  <div class="text-033">ご本人と社員だけが見られます</div>
                </div>
                <div class="layout-184">
                  <div class="layout-051"><label for="pf-bank-name" class="text-002">銀行名</label><input id="pf-bank-name" type="text" value="サンプル銀行" class="text-182"></div>
                  <div class="layout-051"><label for="pf-bank-no" class="text-002">銀行番号</label><input id="pf-bank-no" type="text" inputmode="numeric" value="0000" class="text-182"></div>
                  <div class="layout-051"><label for="pf-branch" class="text-002">支店名</label><input id="pf-branch" type="text" value="池袋支店" class="text-182"></div>
                  <div class="layout-051"><label for="pf-branch-no" class="text-002">支店番号</label><input id="pf-branch-no" type="text" inputmode="numeric" value="000" class="text-182"></div>
                </div>
                <div class="layout-185">
                  <div class="layout-051"><label for="pf-kind" class="text-002">種別</label><select id="pf-kind" class="surface-094"><option>普通</option><option>当座</option></select></div>
                  <div class="layout-051"><label for="pf-acct" class="text-002">口座番号</label><input id="pf-acct" type="text" inputmode="numeric" value="0000000" class="text-182"></div>
                </div>
                <div class="layout-051"><label for="pf-holder" class="text-002">口座名義(カナ)</label><input id="pf-holder" type="text" value="サトウ ハナコ" class="text-182"></div>
              </section>
            </fieldset>
            <section aria-labelledby="pf-contract" class="layout-060">
              <div>
                <h2 id="pf-contract" class="text-014">契約書</h2>
                <div class="text-187">社員が添付します。ご本人は閲覧・ダウンロードのみできます。</div>
              </div>
              <div class="layout-186">
                <div class="layout-187"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3F4550" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6v18h12V7z"></path><path d="M14 3v4h4"></path><path d="M9 13h6M9 17h4"></path></svg>
                  <div class="space-003">
                    <div class="text-032">業務委託契約書_2026.pdf</div>
                    <div class="text-018">2026/04/01 添付 ・ 山田 太郎</div>
                  </div>
                </div>
                <a href="#" class="text-190">開く</a></div>
              <button type="button" class="control-005">契約書を添付する(社員のみ表示)</button></section>
            @endif
          </main>
        </div>
      </div>
    </div>
    
    
  </body>
</html>
