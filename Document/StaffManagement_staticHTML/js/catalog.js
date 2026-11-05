const screens = [
  ['Main', '社員のホーム(PC)', 1440, 1720],
  ['Staff', 'スタッフのホーム(スマホ・下までの全体)', 390, 1380],
  ['SubMain', '準社員のホーム(PC)', 1440, 1720],
  ['Assign', 'アサイン(社員・準社員)', 1440, 2100],
  ['EventEdit', 'イベント登録・編集(社員)', 1440, 3900],
  ['Duplicate', 'イベントの複製(まとめて複製)', 640, 860],
  ['EventDetail', 'イベントの詳細(閲覧用・準社員)', 1440, 1100],
  ['Respond', '依頼への回答(スマホ)', 390, 1300],
  ['AttendanceComplete', '出勤確認の完了(スマホ)', 390, 880],
  ['Report', '報告書の提出(スマホ)', 390, 1100],
  ['PayManage', '支払管理(社員)', 1440, 1200],
  ['Profit', '収支一覧(社員)', 1440, 1250],
  ['Payslip', '支払明細(スマホ)', 390, 1000],
  ['Profile', 'マイプロフィール(スマホ)', 390, 3500],
  ['ReportView', '報告書の確認(社員・準社員)', 1440, 1300],
  ['Clients', '依頼先一覧(社員・準社員)', 1440, 1000],
  ['ClientEdit', '依頼先の詳細・編集(社員のみ)', 1440, 2500],
  ['EventList', 'イベント一覧(社員・準社員・編集は社員)', 1440, 1200],
  ['StaffList', 'スタッフ一覧(社員・準社員)', 1440, 1200],
  ['StaffDetail', 'スタッフ詳細(社員・準社員)', 1440, 2300],
  ['Shortage', '人数不足一覧(社員・準社員)', 1440, 1000],
  ['Accounts', 'アカウント管理(社員)', 1440, 1200],
  ['SendLog', '送信履歴(社員)', 1440, 1650],
  ['MyRequests', '自分への依頼一覧(スマホ)', 390, 1000],
  ['Login', 'ログイン(スマホ・PC共通)', 390, 844],
  ['SetPassword', '初回パスワード設定(招待から)', 390, 844],
  ['AreaSwitch', 'エリア切り替え(スマホ・画面下のタブ)', 390, 844],
  ['SalesHome', '営業ホーム(社員のみ)', 1440, 1100],
  ['SalesBoard', '提案状況ボード(社員のみ)', 1440, 1000],
  ['Meetings', '打合せ履歴(社員のみ)', 1440, 1100],
  ['Coaching', '面談の予定・記録(社員のみ)', 1440, 1500],
  ['InterviewSchedule', '面談の日程を選ぶ(スマホ・LINEから)', 390, 844],
  ['PhoneLog', '電話記録簿(総務)', 1440, 1300],
  ['LineConnect', '単発の人のLINE連携(初回)', 390, 844],
  ['PaymentDetails', '単発の人の振込先・住所の入力(確定後)', 390, 1130]
].map(([slug, title, width, height]) => ({ slug, title, width, height }));

const grid = document.querySelector('#screen-grid');
const count = document.querySelector('#screen-count');
const buttons = [...document.querySelectorAll('[data-filter]')];

count.textContent = `${screens.length}画面（PC・スマホ）`;
grid.innerHTML = screens.map((screen, index) => {
  const device = screen.width <= 640 ? 'mobile' : 'desktop';
  const label = device === 'mobile' ? 'スマホ' : 'PC';
  return `<a class="screen-card" data-device="${device}" href="pages/${screen.slug}.dc.html">
    <span class="screen-number">SCREEN ${String(index + 1).padStart(2, '0')}</span>
    <h2>${screen.title}</h2>
    <span class="screen-meta">${label} · ${screen.width} × ${screen.height}</span>
  </a>`;
}).join('');

buttons.forEach((button) => button.addEventListener('click', () => {
  buttons.forEach((item) => item.classList.toggle('is-active', item === button));
  document.querySelectorAll('.screen-card').forEach((card) => {
    card.hidden = button.dataset.filter !== 'all' && card.dataset.device !== button.dataset.filter;
  });
}));
