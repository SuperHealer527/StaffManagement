const routeMap = {
    'Main.dc.html': '/dashboard',
    'Staff.dc.html': '/screens/staff-home',
    'SubMain.dc.html': '/screens/semi-home',
    'Assign.dc.html': '/screens/assignments',
    'EventEdit.dc.html': '/screens/event-form',
    'Duplicate.dc.html': '/screens/event-copy',
    'EventDetail.dc.html': '/screens/event-detail',
    'Respond.dc.html': '/screens/assignment-response',
    'AttendanceComplete.dc.html': '/screens/attendance-confirmed',
    'Report.dc.html': '/screens/report-submit',
    'PayManage.dc.html': '/screens/payments',
    'Profit.dc.html': '/screens/profit-loss',
    'Payslip.dc.html': '/screens/payment-statement',
    'Profile.dc.html': '/screens/profile',
    'ReportView.dc.html': '/screens/reports',
    'Clients.dc.html': '/screens/clients',
    'ClientEdit.dc.html': '/screens/client-detail',
    'EventList.dc.html': '/screens/events',
    'StaffList.dc.html': '/screens/staff',
    'StaffDetail.dc.html': '/screens/staff-detail',
    'Shortage.dc.html': '/screens/shortages',
    'Accounts.dc.html': '/screens/accounts',
    'SendLog.dc.html': '/screens/notifications',
    'MyRequests.dc.html': '/screens/my-requests',
    'Login.dc.html': '/login',
    'SetPassword.dc.html': '/screens/first-password',
    'AreaSwitch.dc.html': '/screens/mobile-areas',
    'SalesHome.dc.html': '/screens/sales-home',
    'SalesBoard.dc.html': '/screens/proposal-board',
    'Meetings.dc.html': '/screens/meeting-history',
    'Coaching.dc.html': '/screens/interviews',
    'InterviewSchedule.dc.html': '/screens/interview-select',
    'PhoneLog.dc.html': '/screens/phone-log',
    'LineConnect.dc.html': '/screens/line-connect',
    'PaymentDetails.dc.html': '/screens/temporary-bank',
};

document.querySelectorAll('a[href]').forEach((link) => {
    const href = link.getAttribute('href');

    if (routeMap[href]) {
        link.setAttribute('href', routeMap[href]);
    }
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

document.querySelectorAll('[data-create-account]').forEach((button) => {
    button.addEventListener('click', async () => {
        const name = window.prompt('招待する方のお名前を入力してください。');
        if (! name) return;
        const email = window.prompt('メールアドレスを入力してください。');
        if (! email) return;

        try {
            await sendAction('account-create', { name, email, role: 'staff' });
            window.location.reload();
        } catch (error) {
            showMessage(error.message, true);
        }
    });
});

const accountDirectoryElement = document.querySelector('#account-directory');
if (accountDirectoryElement) {
    const accountDirectory = JSON.parse(accountDirectoryElement.textContent);
    const roleValues = {
        '社員': 'employee', '準社員': 'semi_employee', 'スタッフ': 'staff',
        'その他(単発)': 'temporary', 'その他(トレーナー)': 'trainer',
    };

    document.querySelectorAll('.page--accounts tbody tr').forEach((row) => {
        const name = row.querySelector('td:first-child')?.textContent.trim();
        const userId = accountDirectory[name];
        if (! userId) return;

        row.querySelector('select')?.addEventListener('change', async (event) => {
            try {
                await sendAction('account-role', { user_id: userId, role: roleValues[event.target.value] });
            } catch (error) {
                showMessage(error.message, true);
            }
        });

        row.querySelectorAll('button').forEach((button) => {
            button.addEventListener('click', async () => {
                const label = button.textContent.trim();
                const accountStatus = label === '再開' ? 'active' : (label === '招待を再送' ? 'invited' : 'suspended');
                try {
                    await sendAction('account-status', { user_id: userId, account_status: accountStatus });
                    if (label !== '招待を再送') button.textContent = accountStatus === 'active' ? 'ログイン停止' : '再開';
                } catch (error) {
                    showMessage(error.message, true);
                }
            });
        });
    });

    document.querySelectorAll('.page--accounts [aria-label="状態で絞り込み"] button').forEach((button) => {
        button.addEventListener('click', () => {
            const filter = button.textContent.trim();
            document.querySelectorAll('.page--accounts tbody tr').forEach((row) => {
                row.hidden = filter !== 'すべて' && ! row.children[3]?.textContent.includes(filter);
            });
        });
    });
}

document.querySelectorAll('button[type="button"]:not([data-ui-action]):not([data-create-account])').forEach((button) => {
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
