(() => {
  'use strict';
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));
  const config = JSON.parse($('#landing-config').textContent);
  function initializeCarousel(prefix) {
    const carousel = $(`#${prefix}Carousel`);
    const viewport = $(`#${prefix}Viewport`);
    const track = $(`#${prefix}Track`);
    if (!carousel || !viewport || !track) return () => {};
    const cards = $$(`[data-${prefix}]`, track);
    if (cards.length < 2) return () => {};
    const motion = $(`#${prefix}Motion`);
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const copies = cards.map(card => {
      const copy = card.cloneNode(true);
      copy.removeAttribute(`data-${prefix}`);
      copy.setAttribute(prefix === 'demo' ? 'data-carousel-clone' : 'data-review-clone', '');
      copy.setAttribute('aria-hidden', 'true');
      copy.tabIndex = -1;
      $$('[id]', copy).forEach(element => element.removeAttribute('id'));
      $$('a,button,input,select,[tabindex]', copy).forEach(element => element.tabIndex = -1);
      track.appendChild(copy);
      return copy;
    });
    $(`#${prefix}CarouselControls`).hidden = false;
    let userPaused = false;
    let hovered = false;
    let visible = false;
    let frame = null;
    let lastTime = null;
    let position = 0;
    let loopWidth = 0;
    const blocked = () => userPaused || reduced.matches || hovered || !visible || document.hidden || viewport.contains(document.activeElement) || Boolean($('dialog[open]'));
    function animate(time) {
      frame = null;
      if (blocked() || !loopWidth) return;
      if (lastTime !== null) position = (position + Math.min(time - lastTime, 50) * 0.032) % loopWidth;
      lastTime = time;
      viewport.scrollLeft = position;
      frame = requestAnimationFrame(animate);
    }
    function synchronize() {
      motion.hidden = reduced.matches;
      motion.textContent = userPaused ? 'Play' : 'Pause';
      motion.setAttribute('aria-label', `${userPaused ? 'Resume' : 'Pause'} automatic ${prefix} scrolling`);
      motion.setAttribute('aria-pressed', String(userPaused));
      if (blocked()) {
        if (frame !== null) cancelAnimationFrame(frame);
        frame = null;
        lastTime = null;
      } else if (frame === null) {
        position = viewport.scrollLeft % loopWidth;
        lastTime = null;
        frame = requestAnimationFrame(animate);
      }
    }
    function measure() {
      loopWidth = copies[0].offsetLeft - cards[0].offsetLeft;
      if (loopWidth) viewport.scrollLeft %= loopWidth;
      synchronize();
    }
    function pauseForInteraction() { userPaused = true; synchronize(); }
    function move(direction) {
      pauseForInteraction();
      if (!loopWidth) return;
      const stepWidth = cards[1].offsetLeft - cards[0].offsetLeft;
      let start = viewport.scrollLeft % loopWidth;
      if (direction < 0 && start < stepWidth) start += loopWidth;
      viewport.scrollLeft = start;
      viewport.scrollBy({left: direction * stepWidth, behavior: reduced.matches ? 'instant' : 'smooth'});
    }
    motion.addEventListener('click', () => { userPaused = !userPaused; synchronize(); });
    $(`#${prefix}Previous`).addEventListener('click', () => move(-1));
    $(`#${prefix}Next`).addEventListener('click', () => move(1));
    viewport.addEventListener('mouseenter', () => { hovered = true; synchronize(); });
    viewport.addEventListener('mouseleave', () => { hovered = false; synchronize(); });
    carousel.addEventListener('focusin', synchronize);
    carousel.addEventListener('focusout', () => queueMicrotask(synchronize));
    viewport.addEventListener('pointerdown', event => { if (event.pointerType !== 'mouse') pauseForInteraction(); }, {passive: true});
    viewport.addEventListener('wheel', event => { if (Math.abs(event.deltaX) > 0 || event.shiftKey) pauseForInteraction(); }, {passive: true});
    viewport.addEventListener('keydown', event => {
      if (event.target !== viewport || !['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
      event.preventDefault();
      move(event.key === 'ArrowRight' ? 1 : -1);
    });
    reduced.addEventListener('change', synchronize);
    document.addEventListener('visibilitychange', synchronize);
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(entries => { visible = entries[0].isIntersecting; synchronize(); }, {threshold: 0.05}).observe(viewport);
    } else visible = true;
    if ('ResizeObserver' in window) new ResizeObserver(measure).observe(viewport);
    else addEventListener('resize', measure);
    measure();
    return synchronize;
  }
  const synchronizeDemos = initializeCarousel('demo');
  const synchronizeReviews = initializeCarousel('review');
  const synchronizeCarousels = () => { synchronizeDemos(); synchronizeReviews(); };
  const menuButton = $('#menuBtn');
  const mobileMenu = $('#mm');
  function closeMenu() {
    mobileMenu.hidden = true;
    menuButton.setAttribute('aria-expanded', 'false');
    menuButton.setAttribute('aria-label', 'Open menu');
  }
  menuButton.addEventListener('click', () => {
    const open = mobileMenu.hidden;
    mobileMenu.hidden = !open;
    menuButton.setAttribute('aria-expanded', String(open));
    menuButton.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
  });
  $$('a', mobileMenu).forEach(link => link.addEventListener('click', closeMenu));
  document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });
  // All account creation uses the existing SaaS endpoint and its real verification result.
  const dialog = $('#wizard');
  const form = $('#wForm');
  const next = $('#wNext');
  const back = $('#wBack');
  const planSelect = $('#planSel');
  const formStatus = $('#formStatus');
  const done = $('#wDone');
  const resendButton = $('#resendVerification');
  const fields = {name: $('#f_name'), email: $('#f_email'), whatsapp: $('#f_phone'), business: $('#f_biz'), country: $('#f_country'), password: $('#f_pw'), password_confirmation: $('#f_pw2')};
  const steps = {name: 1, email: 1, whatsapp: 1, business: 2, country: 2, password: 3, password_confirmation: 3, theme: 3};
  let step = 1;
  let opener = null;
  let pending = false;
  let createdEmail = '';
  let resendTimer = null;
  let resendSeconds = 0;
  function plan(id) {
    const selected = Object.hasOwn(config.plans, id) ? id : 'base';
    planSelect.value = selected;
    const details = config.plans[selected];
    $('#planName').textContent = `${details.name}, ₹${Number(details.monthly_price).toLocaleString('en-IN')}/month`;
  }
  planSelect.addEventListener('change', () => plan(planSelect.value));
  function showStep(number) {
    step = number;
    $$('.pane', form).forEach(pane => pane.classList.toggle('active', Number(pane.dataset.step) === number));
    $('#wBar').style.width = `${number / 3 * 100}%`;
    $('#wLabel').textContent = `Step ${number} of 3: ${['About you', 'Your business', 'Account and theme'][number - 1]}`;
    back.hidden = number === 1;
    next.textContent = number === 3 ? 'Create my store' : 'Continue';
    if (dialog.open) $('.pane.active input,.pane.active select', form)?.focus({preventScroll: true});
  }
  function fieldError(name, message) {
    const element = fields[name];
    const error = $(`#err_${name}`);
    if (error) error.textContent = message;
    if (element) {
      element.closest('.signup-field').classList.toggle('bad', Boolean(message));
      element.setAttribute('aria-invalid', String(Boolean(message)));
    }
  }
  function validate(number) {
    const errors = {};
    if (number === 1) {
      if (!fields.name.value.trim()) errors.name = 'Enter your name.';
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(fields.email.value.trim())) errors.email = 'Enter a valid email address.';
      const phoneValue = fields.whatsapp.value.trim();
      const digits = phoneValue.replace(/\D/g, '');
      if (!/^[+\d\s().-]+$/.test(phoneValue) || digits.length < 7 || digits.length > 18) errors.whatsapp = 'Enter a valid phone number, including your country code.';
    }
    if (number === 2) {
      if (!fields.business.value.trim()) errors.business = 'Enter your business name.';
      if (!fields.country.value) errors.country = 'Choose your country.';
    }
    if (number === 3) {
      if (fields.password.value.length < 8) errors.password = 'Use at least 8 characters.';
      if (!fields.password_confirmation.value || fields.password.value !== fields.password_confirmation.value) errors.password_confirmation = 'Passwords do not match.';
      if (!$('input[name="theme"]:checked', form)) errors.theme = 'Choose a starting theme.';
    }
    Object.keys(steps).filter(name => steps[name] === number).forEach(name => fieldError(name, errors[name] || ''));
    const first = Object.keys(errors)[0];
    if (first) (fields[first] || $('input[name="theme"]', form))?.focus();
    return !first;
  }
  function message(text) { formStatus.textContent = text; formStatus.hidden = !text; }
  function open(planId, email, trigger) {
    closeMenu();
    opener = trigger || null;
    if (!createdEmail && !pending) {
      plan(planId || 'base');
      if (email !== undefined) fields.email.value = email;
      form.hidden = false;
      done.hidden = true;
      message('');
      showStep(1);
    }
    if (!dialog.open) dialog.showModal();
    document.body.classList.add('wizard-open');
    synchronizeCarousels();
    if (createdEmail) done.focus();
    else $('.pane.active input,.pane.active select', form)?.focus({preventScroll: true});
  }
  $$('[data-start]').forEach(button => button.addEventListener('click', event => { event.preventDefault(); open(button.dataset.plan, undefined, button); }));
  back.addEventListener('click', () => { if (!pending && step > 1) showStep(step - 1); });
  $('#wClose').addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', event => {
    const bounds = dialog.getBoundingClientRect();
    if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) dialog.close();
  });
  dialog.addEventListener('close', () => { document.body.classList.remove('wizard-open'); opener?.focus(); synchronizeCarousels(); });
  $$('input,select', form).forEach(input => input.addEventListener('input', () => {
    fieldError(input.name, '');
  }));
  async function post(url, payload) {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 30000);
    try {
      const response = await fetch(url, {
        method: 'POST', credentials: 'same-origin', signal: controller.signal,
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').content},
        body: JSON.stringify(payload),
      });
      const data = await response.json().catch(() => ({}));
      return {response, data};
    } finally { clearTimeout(timeout); }
  }
  function statusMessage(status, fallback) {
    if (status === 419) return 'Your session expired. Refresh this page before trying again.';
    if (status === 429) return 'Too many requests. Please wait a minute before trying again.';
    return fallback;
  }
  function cooldown(seconds) {
    clearInterval(resendTimer);
    resendSeconds = seconds;
    resendButton.disabled = seconds > 0;
    resendButton.textContent = seconds > 0 ? `Resend link in ${seconds}s` : 'Resend verification link';
    if (seconds > 0) resendTimer = setInterval(() => {
      resendSeconds--;
      resendButton.textContent = resendSeconds > 0 ? `Resend link in ${resendSeconds}s` : 'Resend verification link';
      if (resendSeconds <= 0) { clearInterval(resendTimer); resendButton.disabled = false; }
    }, 1000);
  }
  form.addEventListener('submit', async event => {
    event.preventDefault();
    if (pending || createdEmail || !validate(step)) return;
    if (step < 3) { showStep(step + 1); return; }
    for (const number of [1, 2]) if (!validate(number)) { showStep(number); return; }
    const payload = Object.fromEntries(Object.entries(fields).map(([key, field]) => [key, key.startsWith('password') ? field.value : field.value.trim()]));
    payload.plan = planSelect.value;
    payload.theme = $('input[name="theme"]:checked', form).value;
    pending = true;
    next.disabled = true;
    back.disabled = true;
    planSelect.disabled = true;
    next.textContent = 'Creating your store…';
    form.setAttribute('aria-busy', 'true');
    message('');
    try {
      const {response, data} = await post(config.register, payload);
      if (response.ok && data.success === true) {
        createdEmail = data.email || payload.email;
        $('#doneEmail').textContent = createdEmail;
        $('#doneTitle').textContent = data.email_sent === true ? 'Check your email' : 'Your account is created';
        $('#doneMessage').textContent = data.message || 'Your account is created. Verify your email before signing in.';
        fields.password.value = '';
        fields.password_confirmation.value = '';
        form.hidden = true;
        done.hidden = false;
        cooldown(data.email_sent === true ? 60 : 0);
        done.focus();
      } else if (response.status === 422 && data.errors) {
        let firstStep = 3;
        let firstField = null;
        Object.entries(data.errors).forEach(([name, errors]) => {
          fieldError(name, Array.isArray(errors) ? errors[0] : String(errors));
          if (steps[name] && (!firstField || steps[name] < firstStep)) { firstStep = steps[name]; firstField = name; }
        });
        showStep(firstStep);
        (fields[firstField] || $('input[name="theme"]', form))?.focus();
        message('Please check the highlighted details.');
      } else {
        message(statusMessage(response.status, 'We could not create your account. Please try again later.'));
      }
    } catch (_) {
      message('We could not confirm the result. Check your email before retrying. If your account was created, use the verification resend option on the merchant login page.');
    } finally {
      pending = false;
      next.disabled = false;
      back.disabled = false;
      planSelect.disabled = false;
      form.removeAttribute('aria-busy');
      next.textContent = step === 3 ? 'Create my store' : 'Continue';
    }
  });
  resendButton.addEventListener('click', async () => {
    if (!createdEmail || resendButton.disabled) return;
    resendButton.disabled = true;
    $('#resendStatus').textContent = 'Requesting a verification link…';
    try {
      const {response, data} = await post(config.resend, {email: createdEmail});
      $('#resendStatus').textContent = response.ok ? (data.message || 'Please check your inbox for a verification link.') : statusMessage(response.status, data.message || 'Unable to send a verification link. Please try again later.');
      cooldown(response.ok || response.status === 429 ? 60 : 0);
    } catch (_) {
      $('#resendStatus').textContent = 'Connection failed. Please try again when you are back online.';
      cooldown(0);
    }
  });
  const parameters = new URLSearchParams(location.search);
  if (parameters.has('get_started')) {
    const selectedTheme = $$('input[name="theme"]', form).find(input => input.value === parameters.get('theme'));
    if (selectedTheme) {
      selectedTheme.checked = true;
      selectedTheme.dispatchEvent(new Event('change', {bubbles: true}));
    }
    open(parameters.get('plan') || 'base');
  }
})();
