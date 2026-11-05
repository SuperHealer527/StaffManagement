(() => {
  document.querySelectorAll('a[href="#"]').forEach((link) => {
    link.addEventListener('click', (event) => event.preventDefault());
  });

  document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', (event) => event.preventDefault());
  });

  document.addEventListener('click', (event) => {
    const button = event.target.closest('button[data-nav]');
    if (button) location.href = button.dataset.nav;
  });
})();
