(() => {
  'use strict';
  const section = document.getElementById('pricing');
  if (!section) return;
  const monthly = section.querySelector('#btn-monthly');
  const yearly = section.querySelector('#btn-yearly');
  if (!monthly || !yearly) return;
  function select(annual) {
    monthly.classList.toggle('toggle-active', !annual);
    yearly.classList.toggle('toggle-active', annual);
    monthly.setAttribute('aria-pressed', String(!annual));
    yearly.setAttribute('aria-pressed', String(annual));
    section.querySelectorAll('.price-show-monthly').forEach(price => price.hidden = annual);
    section.querySelectorAll('.price-show-yearly').forEach(price => price.hidden = !annual);
  }
  monthly.addEventListener('click', () => select(false));
  yearly.addEventListener('click', () => select(true));
})();
