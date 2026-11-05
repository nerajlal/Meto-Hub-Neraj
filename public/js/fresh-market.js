(() => {
  'use strict';
  document.querySelectorAll('[data-market-carousel]').forEach(carousel => {
    const slides = [...carousel.querySelectorAll('.sf-merchant-banner')];
    const controls = carousel.querySelector('.sf-market-carousel-controls');
    if (slides.length < 2 || !controls) return;
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const dots = [...controls.querySelectorAll('[data-slide-dot]')];
    let index = 0, timer, paused = reduced.matches;
    const show = next => {
      index = (next + slides.length) % slides.length;
      slides.forEach((slide, i) => {
        slide.hidden = i !== index;
        slide.setAttribute('role', 'group');
        slide.setAttribute('aria-roledescription', 'slide');
        slide.setAttribute('aria-label', `${i + 1} of ${slides.length}`);
      });
      dots.forEach((dot, i) => dot.setAttribute('aria-current', String(i === index)));
    };
    const schedule = () => {
      clearInterval(timer);
      if (!paused && !document.hidden && !carousel.matches(':hover') && !carousel.contains(document.activeElement)) timer = setInterval(() => show(index + 1), 5000);
    };
    controls.hidden = false;
    carousel.classList.add('is-enhanced');
    show(0);
    dots.forEach((dot, i) => dot.addEventListener('click', () => { show(i); schedule(); }));
    carousel.addEventListener('mouseenter', schedule);
    carousel.addEventListener('mouseleave', schedule);
    carousel.addEventListener('focusin', schedule);
    carousel.addEventListener('focusout', () => queueMicrotask(schedule));
    document.addEventListener('visibilitychange', schedule);
    reduced.addEventListener('change', () => { if (reduced.matches) paused = true; schedule(); });
    schedule();
  });
})();
