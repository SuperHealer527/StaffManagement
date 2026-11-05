(() => {
  const page = location.pathname.split('/').pop();
  const go = (target) => { location.href = target; };
  const buttonByText = (text) => [...document.querySelectorAll('button')]
    .find((button) => button.textContent.trim() === text);

  const navigateOnClick = (text, target) => {
    buttonByText(text)?.addEventListener('click', () => go(target));
  };

  if (page === 'Login.dc.html') {
    const form = document.querySelector('form');
    const email = document.querySelector('#lg-mail');
    const password = document.querySelector('#lg-pass');
    email?.setAttribute('required', '');
    password?.setAttribute('required', '');
    form?.addEventListener('submit', (event) => {
      event.preventDefault();
      if (form.reportValidity()) go('Main.dc.html');
    });
    buttonByText('ログイン')?.addEventListener('click', () => form?.requestSubmit());
    const forgot = [...document.querySelectorAll('a')]
      .find((link) => link.textContent.includes('パスワードを忘れた方'));
    if (forgot) forgot.href = 'SetPassword.dc.html';
  }

  if (page === 'SetPassword.dc.html') {
    const form = document.querySelector('form');
    const password = document.querySelector('#sp-pass');
    const confirmation = document.querySelector('#sp-pass2');
    password?.setAttribute('required', '');
    confirmation?.setAttribute('required', '');
    password?.setAttribute('minlength', '8');
    confirmation?.setAttribute('minlength', '8');
    const validateMatch = () => confirmation?.setCustomValidity(
      password?.value === confirmation?.value ? '' : 'パスワードが一致しません'
    );
    confirmation?.addEventListener('input', validateMatch);
    form?.addEventListener('submit', (event) => {
      event.preventDefault();
      validateMatch();
      if (form.reportValidity()) go('Staff.dc.html');
    });
    buttonByText('設定してはじめる')?.addEventListener('click', () => form?.requestSubmit());
  }

  if (page === 'EventEdit.dc.html') {
    buttonByText('保存する')?.addEventListener('click', () => go('EventList.dc.html'));
  }

  if (page === 'EventDetail.dc.html') {
    navigateOnClick('編集する', 'EventEdit.dc.html');
    navigateOnClick('コピー', 'Duplicate.dc.html');
  }

  if (page === 'Duplicate.dc.html') {
    const create = [...document.querySelectorAll('button')]
      .find((button) => button.textContent.includes('件のイベントを作成'));
    create?.addEventListener('click', () => go('EventList.dc.html'));
  }

  if (page === 'ClientEdit.dc.html') navigateOnClick('保存する', 'Clients.dc.html');
  if (page === 'LineConnect.dc.html') navigateOnClick('LINEで友だち追加して連携する', 'PaymentDetails.dc.html');
  if (page === 'PaymentDetails.dc.html') navigateOnClick('送信する', 'Staff.dc.html');
  if (page === 'Report.dc.html') navigateOnClick('報告書を提出する', 'Staff.dc.html');

  if (page === 'Respond.dc.html') {
    const section = document.querySelector('#ask')?.closest('section');
    const choices = section ? [...section.querySelectorAll('button')] : [];
    choices.forEach((button) => button.addEventListener('click', () => {
      choices.forEach((choice) => {
        choice.classList.toggle('is-selected', choice === button);
        choice.setAttribute('aria-pressed', String(choice === button));
      });
    }));
  }

  if (page === 'InterviewSchedule.dc.html') {
    const submit = buttonByText('この内容で送る');
    const options = [...document.querySelectorAll('button')].filter((button) => button !== submit);
    options.forEach((button) => button.addEventListener('click', () => {
      options.forEach((option) => option.classList.toggle('is-selected', option === button));
      button.setAttribute('aria-pressed', 'true');
    }));
    submit?.addEventListener('click', () => {
      submit.textContent = '送信しました';
      submit.disabled = true;
    });
  }
})();
