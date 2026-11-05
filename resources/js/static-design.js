const routeMap = {
    'Main.dc.html': '/dashboard',
    'Staff.dc.html': '/my/home',
    'SubMain.dc.html': '/screens/semi-home',
    'Assign.dc.html': '/assignments',
    'EventEdit.dc.html': '/events/create',
    'Duplicate.dc.html': '/screens/event-copy',
    'EventDetail.dc.html': '/screens/event-detail',
    'Respond.dc.html': '/screens/assignment-response',
    'AttendanceComplete.dc.html': '/screens/attendance-confirmed',
    'Report.dc.html': '/screens/report-submit',
    'PayManage.dc.html': '/finance/payments',
    'Profit.dc.html': '/finance/profit',
    'Payslip.dc.html': '/my/payments',
    'Profile.dc.html': '/profile',
    'ReportView.dc.html': '/reports',
    'Clients.dc.html': '/clients',
    'ClientEdit.dc.html': '/screens/client-detail',
    'EventList.dc.html': '/events',
    'StaffList.dc.html': '/staff',
    'StaffDetail.dc.html': '/screens/staff-detail',
    'Shortage.dc.html': '/shortages',
    'Accounts.dc.html': '/screens/accounts',
    'SendLog.dc.html': '/notifications',
    'MyRequests.dc.html': '/my/requests',
    'Login.dc.html': '/login',
    'SetPassword.dc.html': '/screens/first-password',
    'AreaSwitch.dc.html': '/screens/mobile-areas',
    'SalesHome.dc.html': '/sales',
    'SalesBoard.dc.html': '/sales/proposals',
    'Meetings.dc.html': '/meetings',
    'Coaching.dc.html': '/interviews',
    'InterviewSchedule.dc.html': '/screens/interview-select',
    'PhoneLog.dc.html': '/phone-logs',
    'LineConnect.dc.html': '/profile',
    'PaymentDetails.dc.html': '/screens/temporary-bank',
};

document.querySelectorAll('a[href]').forEach((link) => {
    const href = link.getAttribute('href');

    if (routeMap[href]) {
        link.setAttribute('href', routeMap[href]);
    }
});

const areaDestinations = { '事業': '/dashboard', '営業': '/sales', '会計': '/finance/payments', 'メニュー': '/profile' };
document.querySelectorAll('.page--areaswitch [aria-label="エリア切り替え"] button').forEach((button) => {
    button.addEventListener('click', () => {
        const destination = areaDestinations[button.textContent.trim()];
        if (destination) window.location.assign(destination);
    });
});

document.querySelectorAll('[data-filter-group]').forEach((group) => {
    group.addEventListener('click', (event) => {
        const button = event.target.closest('button');

        if (! button) {
            return;
        }

        group.querySelectorAll('button').forEach((candidate) => {
            candidate.classList.toggle('is-selected', candidate === button);
        });
    });
});

document.querySelectorAll('[data-auto-filter-form] select, [data-auto-filter-form] input[type="checkbox"], [data-auto-filter-form] input[type="date"]').forEach((control) => {
    control.addEventListener('change', () => control.form?.requestSubmit());
});

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

function showMessage(message, isError = false) {
    let notice = document.querySelector('[data-app-notice]');

    if (! notice) {
        notice = document.createElement('div');
        notice.dataset.appNotice = '';
        Object.assign(notice.style, {
            position: 'fixed', right: '24px', bottom: '24px', zIndex: '9999',
            padding: '12px 18px', borderRadius: '8px', color: '#fff',
            font: '500 14px "Noto Sans JP", sans-serif', boxShadow: '0 8px 24px rgba(0,0,0,.18)',
        });
        document.body.append(notice);
    }

    notice.style.background = isError ? '#b42318' : '#1c7a4a';
    notice.textContent = message;
    notice.hidden = false;
    window.clearTimeout(notice.hideTimer);
    notice.hideTimer = window.setTimeout(() => { notice.hidden = true; }, 3000);
}

async function sendAction(action, values) {
    const response = await fetch(`/ui-actions/${action}`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(values),
    });

    const result = await response.json().catch(() => ({}));

    if (! response.ok) {
        throw new Error(result.message || '保存できませんでした。入力内容をご確認ください。');
    }

    showMessage(result.message || '保存しました。');
    return result;
}

const selectedClassPairs = [
    ['control-006', 'control-007'], ['control-009', 'control-008'],
    ['control-020', 'control-021'], ['control-045', 'control-046'],
    ['control-049', 'control-050'], ['control-061', 'control-062'],
    ['control-071', 'control-070'], ['control-073', 'control-074'],
];

document.querySelectorAll('[role="group"], [role="tablist"], fieldset').forEach((group) => {
    const selectableButtons = group.querySelectorAll('button[aria-pressed], button[aria-selected]');

    selectableButtons.forEach((button) => {
        button.addEventListener('click', () => {
            selectableButtons.forEach((candidate) => {
                const selected = candidate === button;
                if (candidate.hasAttribute('aria-pressed')) candidate.setAttribute('aria-pressed', String(selected));
                if (candidate.hasAttribute('aria-selected')) candidate.setAttribute('aria-selected', String(selected));

                selectedClassPairs.forEach(([activeClass, inactiveClass]) => {
                    if (candidate.classList.contains(activeClass) || candidate.classList.contains(inactiveClass)) {
                        candidate.classList.toggle(activeClass, selected);
                        candidate.classList.toggle(inactiveClass, ! selected);
                    }
                });
            });
        });
    });
});

document.querySelectorAll('[data-interview-value]').forEach((button) => {
    button.addEventListener('click', () => {
        const form = button.closest('form');
        form?.querySelector('input[name="selected_at"]')?.setAttribute('value', button.dataset.interviewValue);
    });
});

document.querySelectorAll('form[action="#"]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        showMessage('画面確認用です。送信リンクから開くと保存できます。');
    });
});

document.querySelectorAll('button').forEach((button) => {
    const label = button.textContent.trim();

    if (label === 'コピー') {
        button.addEventListener('click', async () => {
            const container = button.closest('div');
            const text = container?.previousElementSibling?.textContent?.trim() || container?.parentElement?.textContent?.replace('コピー', '').trim();
            await navigator.clipboard.writeText(text || '');
            showMessage('クリップボードにコピーしました。');
        });
    }

    if (label === 'CSVで書き出す') {
        button.addEventListener('click', () => {
            const rows = [...document.querySelectorAll('table tr')].map((row) =>
                [...row.querySelectorAll('th,td')].map((cell) => `"${cell.textContent.trim().replaceAll('"', '""')}"`).join(','),
            );
            const blob = new Blob([`\uFEFF${rows.join('\n')}`], { type: 'text/csv;charset=utf-8' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = '収支一覧.csv';
            link.click();
            URL.revokeObjectURL(link.href);
        });
    }

    if (label.includes('キャンセル')) {
        button.addEventListener('click', () => window.history.back());
    }
});

document.querySelectorAll('[data-ui-action]').forEach((element) => {
    element.addEventListener('click', async () => {
        element.disabled = true;
        try {
            await sendAction(element.dataset.uiAction, JSON.parse(element.dataset.uiValues || '{}'));
            if (element.dataset.successText) element.textContent = element.dataset.successText;
        } catch (error) {
            showMessage(error.message, true);
        } finally {
            element.disabled = false;
        }
    });
});

document.querySelectorAll('[data-phone-log-form]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const values = Object.fromEntries(new FormData(form));
        values.recipient_id = values.recipient_id || null;

        try {
            await sendAction('phone-log-create', values);
            form.reset();
        } catch (error) {
            showMessage(error.message, true);
        }
    });
});

const accountDialog = document.querySelector('#account-create-dialog');
document.querySelector('[data-create-account]')?.addEventListener('click', () => accountDialog?.showModal());
document.querySelector('[data-close-account-dialog]')?.addEventListener('click', () => accountDialog?.close());
document.querySelector('[data-account-create-form]')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;

    try {
        await sendAction('account-create', Object.fromEntries(new FormData(form)));
        accountDialog?.close();
        window.location.reload();
    } catch (error) {
        showMessage(error.message, true);
    } finally {
        submitButton.disabled = false;
    }
});

document.querySelectorAll('[data-account-role]').forEach((select) => {
    select.addEventListener('change', async () => {
        select.disabled = true;
        try {
            await sendAction('account-role', { user_id: Number(select.dataset.userId), role: select.value });
            window.location.reload();
        } catch (error) {
            showMessage(error.message, true);
            window.location.reload();
        }
    });
});

document.querySelectorAll('[data-account-state]').forEach((button) => {
    button.addEventListener('click', async () => {
        if (button.dataset.accountState === 'suspended' && ! window.confirm('このアカウントのログインを停止しますか？')) return;

        button.disabled = true;
        try {
            await sendAction('account-status', {
                user_id: Number(button.dataset.userId),
                account_status: button.dataset.accountState,
            });
            window.location.reload();
        } catch (error) {
            showMessage(error.message, true);
            button.disabled = false;
        }
    });
});

const accountStatusFilters = { '利用中': 'active', '招待中': 'invited', '停止中': 'suspended', 'ログインなし': 'no_login' };
document.querySelectorAll('.page--accounts [aria-label="状態で絞り込み"] button').forEach((button) => {
    button.addEventListener('click', () => {
        const selectedStatus = accountStatusFilters[button.textContent.trim()];
        document.querySelectorAll('[data-account-row]').forEach((row) => {
            row.hidden = selectedStatus !== undefined && row.dataset.accountStatus !== selectedStatus;
        });
    });
});

let nextContactIndex = document.querySelectorAll('[data-contact-row]').length;
document.querySelector('[data-add-contact]')?.addEventListener('click', () => {
    const template = document.querySelector('#contact-row-template');
    const rows = document.querySelector('[data-contact-rows]');
    if (! template || ! rows) return;

    const html = template.innerHTML.replaceAll('__INDEX__', String(nextContactIndex));
    nextContactIndex += 1;
    rows.insertAdjacentHTML('beforeend', html);
});

document.addEventListener('click', (event) => {
    const removeButton = event.target.closest('[data-remove-contact]');
    if (! removeButton || ! window.confirm('この担当者を削除しますか？')) return;
    removeButton.closest('[data-contact-row]')?.remove();
});

const meetingDialog = document.querySelector('#meeting-dialog');
const meetingForm = document.querySelector('[data-meeting-form]');
document.querySelector('[data-add-meeting]')?.addEventListener('click', () => {
    meetingForm?.reset();
    meetingForm.action = `/clients/${meetingForm.dataset.clientId}/meetings`;
    meetingForm.querySelector('[data-meeting-method]').value = 'POST';
    meetingForm.querySelector('[data-meeting-dialog-title]').textContent = '打合せを登録';
    meetingDialog?.showModal();
});
document.querySelectorAll('[data-edit-meeting]').forEach((button) => {
    button.addEventListener('click', () => {
        const meeting = JSON.parse(window.atob(button.dataset.meeting));
        meetingForm.action = `/clients/${meetingForm.dataset.clientId}/meetings/${meeting.id}`;
        meetingForm.querySelector('[data-meeting-method]').value = 'PATCH';
        meetingForm.querySelector('[data-meeting-dialog-title]').textContent = '打合せを編集';
        meetingForm.elements.meeting_date.value = String(meeting.meeting_date).slice(0, 10);
        meetingForm.elements.type.value = meeting.type;
        meetingForm.elements.method.value = meeting.method;
        meetingForm.elements.participants.value = meeting.participants || '';
        meetingForm.elements.content.value = meeting.content;
        meetingDialog?.showModal();
    });
});
document.querySelector('[data-close-meeting]')?.addEventListener('click', () => meetingDialog?.close());
document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (! window.confirm('この記録を削除しますか？')) event.preventDefault();
    });
});

document.querySelector('[data-cancel-event-url]')?.addEventListener('click', async (event) => {
    if (! window.confirm('このイベントを中止しますか？依頼・承諾・確定済みのアサインもキャンセルされます。')) return;
    const button = event.currentTarget;
    button.disabled = true;
    try {
        const response = await fetch(button.dataset.cancelEventUrl, {
            method: 'POST',
            headers: { 'Accept': 'text/html', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ confirmation: '1' }),
        });
        if (! response.ok) throw new Error('イベントを中止できませんでした。');
        window.location.assign(response.url);
    } catch (error) {
        showMessage(error.message, true);
        button.disabled = false;
    }
});

let nextQuestionIndex = document.querySelectorAll('[data-question-row]').length;
document.querySelectorAll('[data-add-question]').forEach((button) => {
    button.addEventListener('click', () => {
        const template = document.querySelector(`#question-template-${button.dataset.addQuestion}`);
        const rows = document.querySelector('[data-question-rows]');
        if (! template || ! rows) return;
        rows.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(nextQuestionIndex)));
        nextQuestionIndex += 1;
        document.querySelector('[data-question-count]').textContent = document.querySelectorAll('[data-question-row]').length;
    });
});

document.addEventListener('click', (event) => {
    const removeQuestion = event.target.closest('[data-remove-question]');
    if (removeQuestion) {
        removeQuestion.closest('[data-question-row]')?.remove();
        document.querySelector('[data-question-count]').textContent = document.querySelectorAll('[data-question-row]').length;
        return;
    }

    const addOption = event.target.closest('[data-add-option]');
    if (addOption) {
        const optionRows = addOption.closest('[data-option-rows]');
        const firstOption = optionRows?.querySelector('input');
        if (! firstOption) return;
        const input = firstOption.cloneNode();
        input.value = '';
        optionRows.insertBefore(input, addOption);
    }
});

let nextInvoiceIndex = document.querySelectorAll('[data-invoice-rows] [data-money-row]').length;
let nextExpenseIndex = document.querySelectorAll('[data-expense-rows] [data-money-row]').length;
function appendMoneyRow(templateSelector, rowsSelector, index) {
    const template = document.querySelector(templateSelector);
    const rows = document.querySelector(rowsSelector);
    if (! template || ! rows) return;
    rows.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(index)));
    updateMoneyTotals();
}
document.querySelector('[data-add-invoice]')?.addEventListener('click', () => appendMoneyRow('#invoice-row-template', '[data-invoice-rows]', nextInvoiceIndex++));
document.querySelector('[data-add-expense]')?.addEventListener('click', () => appendMoneyRow('#expense-row-template', '[data-expense-rows]', nextExpenseIndex++));
document.addEventListener('click', (event) => {
    const removeMoneyRow = event.target.closest('[data-remove-money-row]');
    if (! removeMoneyRow) return;
    removeMoneyRow.closest('[data-money-row]')?.remove();
    updateMoneyTotals();
});

function sumMoneyRows(selector) {
    return [...document.querySelectorAll(`${selector} [data-money-amount]`)]
        .reduce((total, input) => total + (Number(input.value) || 0), 0);
}

function formatYen(amount) {
    return `¥${amount.toLocaleString('ja-JP')}`;
}

function updateMoneyTotals() {
    const invoiceTotal = sumMoneyRows('[data-invoice-rows]');
    const expenseTotal = sumMoneyRows('[data-expense-rows]');
    const costElement = document.querySelector('[data-profit-cost]');
    const baseCost = Number(costElement?.dataset.baseCost || 0);
    const totalCost = baseCost + expenseTotal;
    const profit = invoiceTotal - totalCost;
    const margin = invoiceTotal > 0 ? (profit / invoiceTotal) * 100 : 0;
    if (document.querySelector('[data-invoice-total]')) document.querySelector('[data-invoice-total]').textContent = formatYen(invoiceTotal);
    if (document.querySelector('[data-expense-total]')) document.querySelector('[data-expense-total]').textContent = formatYen(expenseTotal);
    if (document.querySelector('[data-profit-invoice]')) document.querySelector('[data-profit-invoice]').textContent = formatYen(invoiceTotal);
    if (costElement) costElement.textContent = formatYen(totalCost);
    if (document.querySelector('[data-profit-expense]')) document.querySelector('[data-profit-expense]').textContent = formatYen(expenseTotal);
    if (document.querySelector('[data-profit-value]')) document.querySelector('[data-profit-value]').textContent = formatYen(profit);
    if (document.querySelector('[data-profit-margin]')) document.querySelector('[data-profit-margin]').textContent = `${margin.toFixed(1)}%`;
}
document.addEventListener('input', (event) => {
    if (event.target.matches('[data-money-amount]')) updateMoneyTotals();
});

const duplicateDates = new Set();
function renderDuplicateDates() {
    const container = document.querySelector('[data-duplicate-dates]');
    if (! container) return;
    container.replaceChildren();
    [...duplicateDates].sort().forEach((dateValue) => {
        const date = new Date(`${dateValue}T00:00:00`);
        const chip = document.createElement('span');
        chip.className = 'surface-062';
        chip.innerHTML = `<span>${date.toLocaleDateString('ja-JP', { month: 'numeric', day: 'numeric', weekday: 'short' })}</span><input type="hidden" name="dates[]" value="${dateValue}"><button type="button" class="control-032" data-remove-duplicate-date="${dateValue}" aria-label="日付を外す">×</button>`;
        container.append(chip);
    });
    const count = document.querySelector('[data-duplicate-count]');
    if (count) count.textContent = duplicateDates.size;
}

document.querySelector('[data-add-duplicate-date]')?.addEventListener('click', () => {
    const input = document.querySelector('#dup-date');
    if (! input?.value) return;
    duplicateDates.add(input.value);
    renderDuplicateDates();
});
document.querySelector('[data-add-weekly-dates]')?.addEventListener('click', () => {
    const input = document.querySelector('#dup-date');
    if (! input?.value) return;
    const firstDate = new Date(`${input.value}T00:00:00`);
    for (let week = 0; week < 4; week += 1) {
        const date = new Date(firstDate);
        date.setDate(date.getDate() + (week * 7));
        duplicateDates.add(date.toISOString().slice(0, 10));
    }
    renderDuplicateDates();
});
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-remove-duplicate-date]');
    if (! button) return;
    duplicateDates.delete(button.dataset.removeDuplicateDate);
    renderDuplicateDates();
});
document.querySelector('[data-duplicate-form]')?.addEventListener('submit', (event) => {
    if (duplicateDates.size > 0) return;
    event.preventDefault();
    showMessage('作成する日付を1件以上追加してください。', true);
});

document.querySelectorAll('button[type="button"]:not([data-ui-action]):not([data-create-account]):not([data-account-state]):not([data-close-account-dialog]):not([data-add-contact]):not([data-remove-contact]):not([data-add-meeting]):not([data-edit-meeting]):not([data-close-meeting]):not([data-cancel-event-url]):not([data-add-question]):not([data-remove-question]):not([data-add-option]):not([data-add-invoice]):not([data-add-expense]):not([data-remove-money-row]):not([data-add-duplicate-date]):not([data-add-weekly-dates]):not([data-remove-duplicate-date])').forEach((button) => {
    const label = button.textContent.trim();

    if (label.startsWith('+') && ! label.includes('アカウント')) {
        button.addEventListener('click', () => {
            const list = button.previousElementSibling?.matches('table, tbody')
                ? button.previousElementSibling
                : button.parentElement?.querySelector('tbody');
            const sourceRow = list?.querySelector('tr:last-child');
            if (sourceRow) {
                const newRow = sourceRow.cloneNode(true);
                newRow.querySelectorAll('input').forEach((input) => {
                    if (input.type !== 'checkbox' && input.type !== 'radio') input.value = '';
                    else input.checked = false;
                });
                list.append(newRow);
                showMessage('入力行を追加しました。');
            } else {
                showMessage('入力欄を開きました。');
            }
        });
    }

    if (label === '削除' || button.getAttribute('aria-label')?.includes('削除')) {
        button.addEventListener('click', () => {
            if (! window.confirm('この項目を削除しますか？')) return;
            (button.closest('tr') || button.closest('[class*="layout-"]'))?.remove();
            showMessage('項目を削除しました。保存すると反映されます。');
        });
    }

    if (label.includes('編集')) {
        button.addEventListener('click', () => {
            const target = button.closest('section, article, tr') || button.parentElement;
            const editing = button.dataset.editing !== 'true';
            target?.querySelectorAll('input, textarea, select').forEach((field) => { field.disabled = ! editing; });
            target?.querySelectorAll('p, [class*="text-"]').forEach((field) => {
                if (! field.closest('button')) field.contentEditable = String(editing);
            });
            button.dataset.editing = String(editing);
            button.textContent = editing ? '編集を終える' : '編集する';
            showMessage(editing ? '編集できます。' : '編集を終了しました。');
        });
    }

    if (label.includes('添付') || label.includes('写真を付ける')) {
        button.addEventListener('click', () => {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*,.pdf';
            input.addEventListener('change', () => {
                if (input.files?.[0]) {
                    button.textContent = input.files[0].name;
                    showMessage('ファイルを選択しました。');
                }
            });
            input.click();
        });
    }

    if (['承諾', '未定', '辞退', '確認しました', '再送する'].includes(label)) {
        button.addEventListener('click', () => {
            button.textContent = label === '確認しました' ? '確認済み' : `${label}済み`;
            showMessage('状態を更新しました。');
        });
    }
});

document.querySelectorAll('[data-availability], .page--profile button[aria-label*="時:"]').forEach((button) => {
    button.addEventListener('click', async () => {
        const states = [
            { label: '可', value: 'available', className: 'control-058' },
            { label: '未定', value: 'unknown', className: 'control-057' },
            { label: '不可', value: 'unavailable', className: 'control-056' },
        ];
        const currentIndex = states.findIndex((state) => state.label === button.textContent.trim());
        const nextState = states[(currentIndex + 1) % states.length];

        try {
            const ariaLabel = button.getAttribute('aria-label') || '';
            const weekdayLabels = ['日', '月', '火', '水', '木', '金', '土'];
            await sendAction('availability', {
                weekday: button.dataset.weekday ? Number(button.dataset.weekday) : weekdayLabels.indexOf(ariaLabel.charAt(0)),
                time_slot: button.dataset.timeSlot || ariaLabel.split(':')[0].slice(2),
                availability: nextState.value,
            });
            button.classList.remove('control-056', 'control-057', 'control-058');
            button.classList.add(nextState.className);
            button.querySelector('span').textContent = nextState.label;
        } catch (error) {
            showMessage(error.message, true);
        }
    });
});
