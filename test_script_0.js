
    (function () {
      'use strict';

      /* ---------- Sticky glass header ---------- */
      const header = document.getElementById('siteHeader');
      const onScroll = () => {
        if (window.scrollY > 24) header.classList.add('scrolled');
        else header.classList.remove('scrolled');
      };
      document.addEventListener('scroll', onScroll, { passive: true });
      onScroll();

      /* ---------- Mobile nav toggle ---------- */
      const navToggle = document.getElementById('navToggle');
      const mainNav = document.getElementById('mainNav');
      navToggle.addEventListener('click', () => {
        const open = mainNav.classList.toggle('open');
        navToggle.setAttribute('aria-expanded', open);
        navToggle.classList.toggle('active', open);
      });
      mainNav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
          mainNav.classList.remove('open');
          navToggle.setAttribute('aria-expanded', 'false');
        });
      });

      /* ---------- Reveal-on-scroll ---------- */
      const revealTargets = document.querySelectorAll(
        '.feature-card, .promo-card, .theme-card, .an-card, .timeline-step, .plan-card, .faq-item, .pay-chip, .section-head, .rule-card, .pm-card, .compare-table-wrap'
      );
      revealTargets.forEach((el) => el.classList.add('reveal'));

      const io = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add('in-view');
              io.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.15 }
      );
      revealTargets.forEach((el) => io.observe(el));

      /* ---------- Animated counters ---------- */
      const counters = document.querySelectorAll('[data-count]');
      const animateCounter = (el) => {
        const target = parseInt(el.getAttribute('data-count'), 10);
        const prefix = el.getAttribute('data-prefix') || '';
        const duration = 1400;
        const start = performance.now();
        const step = (now) => {
          const progress = Math.min((now - start) / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          const value = Math.floor(eased * target);
          el.textContent = prefix + value.toLocaleString();
          if (progress < 1) requestAnimationFrame(step);
          else el.textContent = prefix + target.toLocaleString();
        };
        requestAnimationFrame(step);
      };
      const counterIO = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              animateCounter(entry.target);
              counterIO.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.4 }
      );
      counters.forEach((el) => counterIO.observe(el));

      /* ---------- Testimonial carousel ---------- */
      const slides = document.querySelectorAll('.testi-slide');
      const dotsWrap = document.getElementById('testiDots');
      let activeSlide = 0;
      let testiTimer;

      slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.setAttribute('aria-label', 'Show testimonial ' + (i + 1));
        if (i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => showSlide(i, true));
        dotsWrap.appendChild(dot);
      });
      const dots = dotsWrap.querySelectorAll('button');

      function showSlide(index, manual) {
        slides[activeSlide].classList.remove('active');
        dots[activeSlide].classList.remove('active');
        activeSlide = index;
        slides[activeSlide].classList.add('active');
        dots[activeSlide].classList.add('active');
        if (manual) restartTestiTimer();
      }
      function nextSlide() { showSlide((activeSlide + 1) % slides.length, false); }
      function restartTestiTimer() {
        clearInterval(testiTimer);
        testiTimer = setInterval(nextSlide, 5500);
      }
      if (slides.length) restartTestiTimer();

      /* ---------- FAQ accordion ---------- */
      document.querySelectorAll('.faq-item').forEach((item) => {
        const btn = item.querySelector('.faq-question');
        btn.addEventListener('click', () => {
          const isOpen = item.classList.contains('open');
          document.querySelectorAll('.faq-item.open').forEach((other) => {
            if (other !== item) {
              other.classList.remove('open');
              other.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            }
          });
          item.classList.toggle('open', !isOpen);
          btn.setAttribute('aria-expanded', String(!isOpen));
        });
      });

      /* ---------- Button ripple effect ---------- */
      document.querySelectorAll('.btn').forEach((btn) => {
        btn.addEventListener('click', function (e) {
          const rect = btn.getBoundingClientRect();
          const ripple = document.createElement('span');
          const size = Math.max(rect.width, rect.height);
          ripple.className = 'ripple';
          ripple.style.width = ripple.style.height = size + 'px';
          ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
          ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
          btn.appendChild(ripple);
          setTimeout(() => ripple.remove(), 650);
        });
      });

      /* ---------- Newsletter form ---------- */
      const newsletterForm = document.getElementById('newsletterForm');
      if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
          e.preventDefault();
          const input = newsletterForm.querySelector('input');
          const btn = newsletterForm.querySelector('button');
          const original = btn.textContent;
          btn.innerHTML = 'Subscribed <i class="fa-solid fa-check"></i>';
          input.value = '';
          setTimeout(() => { btn.textContent = original; }, 2200);
        });
      }

      /* ---------- Smooth anchor scroll offset ---------- */
      document.querySelectorAll('a[href^="#"]').forEach((link) => {
        link.addEventListener('click', (e) => {
          const id = link.getAttribute('href');
          if (id.length > 1) {
            const target = document.querySelector(id);
            if (target) {
              e.preventDefault();
              const y = target.getBoundingClientRect().top + window.pageYOffset - 90;
              window.scrollTo({ top: y, behavior: 'smooth' });
            }
          }
        });
      });
    })();
  