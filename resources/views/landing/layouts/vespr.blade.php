<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Vespr — The Ultimate Grocery Platform')</title>
  <meta name="description"
    content="@yield('meta_description', 'Vespr gives independent supermarkets and grocery stores everything they need to sell online — beautiful stores, smart tools, and zero complexity.')">
  <meta name="keywords"
    content="@yield('meta_keywords', 'grocery saas, grocery e-commerce, supermarket website, vespr, grocery business solution')">
  <link rel="canonical" href="{{ url()->current() }}">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --cream: #F5F0E8;
      --warm: #EDE6D6;
      --sand: #D4C9B0;
      --stone: #9C9080;
      --bark: #5C5147;
      --ink: #1E1A16;
      --violet: #7C5C8A;
      --violet-l: #C4A8D4;
      --violet-d: #4A3556;
      --white: #FDFAF5;
      --serif: 'Inter', system-ui, sans-serif;
      --sans: 'Inter', system-ui, sans-serif;
      --radius: 6px;
      --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: var(--sans);
      background: var(--cream);
      color: var(--ink);
      font-size: 16px;
      line-height: 1.7;
      -webkit-font-smoothing: antialiased;
    }

    /* ── NAV ── */
    nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 20px 48px;
      background: rgba(245, 240, 232, 0.9);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--sand);
    }

    .nav-logo {
      font-family: var(--serif);
      font-size: 28px;
      font-weight: 300;
      letter-spacing: 8px;
      color: var(--ink);
      text-decoration: none;
    }

    .nav-logo span {
      color: var(--violet);
    }

    .nav-links {
      display: flex;
      gap: 36px;
      list-style: none;
      align-items: center;
    }

    .nav-links a {
      font-family: var(--sans);
      font-size: 13px;
      font-weight: 400;
      letter-spacing: 1.5px;
      color: var(--bark);
      text-decoration: none;
      text-transform: uppercase;
      transition: color var(--transition);
    }

    .nav-links a:hover {
      color: var(--violet);
    }

    .nav-cta {
      background: var(--ink);
      color: var(--white) !important;
      padding: 10px 24px;
      border-radius: 2px;
      letter-spacing: 1.5px;
    }

    .nav-cta:hover {
      background: var(--violet);
      color: var(--white) !important;
    }

    /* Mobile Menu Toggle */
    .menu-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 24px;
      color: var(--ink);
      cursor: pointer;
      padding: 8px;
    }

    /* Mobile Nav Overlay */
    .mobile-nav {
      position: fixed;
      inset: 0;
      background: var(--cream);
      z-index: 1000;
      padding: 48px;
      display: flex;
      flex-direction: column;
      gap: 32px;
      transform: translateX(100%);
      transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .mobile-nav.active {
      transform: translateX(0);
    }

    .mobile-nav-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 32px;
    }

    .mobile-nav .nav-links {
      display: flex;
      flex-direction: column;
      gap: 24px;
      align-items: flex-start;
    }

    .mobile-nav .nav-links a {
      font-size: 24px;
      font-family: var(--serif);
      text-transform: none;
      letter-spacing: 0;
    }

    .mobile-close {
      background: none;
      border: none;
      font-size: 32px;
      color: var(--stone);
      cursor: pointer;
    }

    /* ── FOOTER ── */
    footer {
      background: var(--ink);
      padding: 60px 48px 40px;
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 48px;
    }

    .footer-brand .nav-logo {
      display: block;
      margin-bottom: 16px;
      color: var(--cream);
    }

    .footer-brand p {
      font-size: 13px;
      font-weight: 400;
      color: rgba(255, 255, 255, 0.75);
      max-width: 260px;
      line-height: 1.8;
    }

    footer h5 {
      font-size: 11px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--stone);
      margin-bottom: 20px;
      font-weight: 500;
    }

    footer ul {
      list-style: none;
    }

    footer ul li {
      margin-bottom: 10px;
    }

    footer ul a {
      font-size: 14px;
      font-weight: 400;
      color: rgba(255, 255, 255, 0.65);
      text-decoration: none;
      transition: color var(--transition);
    }

    footer ul a:hover {
      color: var(--white);
    }

    .footer-bottom {
      background: var(--ink);
      padding: 20px 48px;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .footer-bottom p {
      font-size: 12px;
      color: var(--stone);
      font-weight: 400;
    }

    @media (max-width: 900px) {
      nav {
        padding: 16px 24px;
      }

      .nav-links {
        display: none;
      }

      .menu-toggle {
        display: block;
      }

      footer {
        grid-template-columns: 1fr 1fr;
        gap: 40px 24px;
        padding: 48px 24px;
      }

      .footer-brand {
        grid-column: 1 / -1;
        margin-bottom: 12px;
      }

      .footer-bottom {
        flex-direction: column;
        gap: 12px;
        text-align: center;
        padding: 24px;
      }
    }

    /* ── GLOBAL TYPOGRAPHY SYSTEM ── */
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    p,
    a,
    li,
    button,
    input,
    textarea {
      font-family: var(--sans);
    }

    .section-label {
      font-size: 11px;
      font-weight: 500;
      letter-spacing: 4px;
      text-transform: uppercase;
      color: var(--violet);
      margin-bottom: 16px;
    }

    .section-title {
      font-size: 40px;
      font-weight: 700;
      line-height: 1.25;
      color: var(--ink);
      margin-bottom: 20px;
    }

    .section-title em {
      font-style: italic;
      color: var(--violet);
    }

    .section-desc {
      font-size: 16px;
      font-weight: 400;
      line-height: 1.8;
      color: var(--bark);
    }

    /* Subheadings & Card/Grid Titles */
    .template-title,
    .t-preview-info h3,
    .step-title,
    .faq-question,
    .v-feature-content h4,
    .admin-feature-content h4 {
      font-size: 24px;
      font-weight: 500;
      line-height: 1.35;
      color: var(--ink);
      text-transform: none;
      letter-spacing: normal;
    }

    /* Body copy and descriptions */
    .template-desc,
    .t-preview-info p,
    .step-desc,
    .faq-answer-content,
    .v-feature-content p,
    .admin-feature-content p {
      font-size: 16px;
      font-weight: 400;
      line-height: 1.7;
      color: var(--bark);
    }

    .btn-primary {
      background: var(--ink);
      color: var(--white);
      padding: 16px 40px;
      font-family: var(--sans);
      font-size: 13px;
      font-weight: 500;
      letter-spacing: 2px;
      text-transform: uppercase;
      text-decoration: none;
      border-radius: 2px;
      transition: background var(--transition);
      border: none;
      cursor: pointer;
      display: inline-block;
    }

    .btn-primary:hover {
      background: var(--violet);
    }

    .btn-ghost {
      background: transparent;
      color: var(--bark);
      padding: 16px 40px;
      font-family: var(--sans);
      font-size: 13px;
      font-weight: 400;
      letter-spacing: 2px;
      text-transform: uppercase;
      text-decoration: none;
      border-radius: 2px;
      border: 1px solid var(--sand);
      transition: border-color var(--transition), color var(--transition);
      cursor: pointer;
      display: inline-block;
    }

    .btn-ghost:hover {
      border-color: var(--violet);
      color: var(--violet);
    }
  </style>
  @yield('styles')
</head>

<body>

  <nav>
    <a href="{{ route('landing') }}" class="nav-logo">vesp<span>r</span></a>
    <ul class="nav-links">
      <li><a href="{{ route('landing') }}#onboarding">How it works</a></li>
      <li><a href="{{ route('landing') }}#features">Features</a></li>
      <li><a href="{{ route('landing.templates') }}">Templates</a></li>
      <li><a href="{{ route('landing') }}#walkthrough">Walkthrough</a></li>
      <li><a href="{{ route('landing') }}#testimonials">Stories</a></li>
      <li><a href="{{ route('landing') }}#pricing">Pricing</a></li>
      <li><a href="{{ route('admin.common.login') }}" class="nav-cta">Start free</a></li>
    </ul>
    <button class="menu-toggle" id="openMobileMenu">
      <i class="fa-solid fa-bars-staggered"></i>
    </button>
  </nav>

  <div class="mobile-nav" id="mobileNav">
    <div class="mobile-nav-header">
      <a href="{{ route('landing') }}" class="nav-logo">vesp<span>r</span></a>
      <button class="mobile-close" id="closeMobileMenu">&times;</button>
    </div>
    <ul class="nav-links">
      <li><a href="{{ route('landing') }}#onboarding">How it works</a></li>
      <li><a href="{{ route('landing') }}#features">Features</a></li>
      <li><a href="{{ route('landing.templates') }}">Templates</a></li>
      <li><a href="{{ route('landing') }}#walkthrough">Walkthrough</a></li>
      <li><a href="{{ route('landing') }}#testimonials">Stories</a></li>
      <li><a href="{{ route('landing') }}#pricing">Pricing</a></li>
      <li><a href="{{ route('admin.common.login') }}" class="nav-cta">Start free</a></li>
    </ul>
  </div>

  <main>
    @yield('content')
  </main>

  <footer>
    <div class="footer-brand">
      <a href="{{ route('landing') }}" class="nav-logo">vesp<span>r</span></a>
      <p>The e-commerce platform built for groceries. From local stores to regional supermarkets.</p>
    </div>
    <div>
      <h5>Platform</h5>
      <ul>
        <li><a href="{{ route('landing') }}#features">Features</a></li>
        <li><a href="{{ route('landing.templates') }}">Templates</a></li>
        <li><a href="{{ route('landing') }}#walkthrough">Walkthrough</a></li>
        <li><a href="{{ route('landing') }}#pricing">Pricing</a></li>
        <li><a href="{{ route('landing') }}#faq">FAQ</a></li>
      </ul>
    </div>
    <div>
      <h5>Company</h5>
      <ul>
        <li><a href="#">About</a></li>
        <li><a href="#">Contact</a></li>
        <li><a href="#">Privacy policy</a></li>
        <li><a href="#">Terms of service</a></li>
      </ul>
    </div>
  </footer>
  <div class="footer-bottom">
    <p>© 2026 Vespr Grocery. All rights reserved.</p>
    <p>support@vespr.com</p>
  </div>

  <!-- Demo Modal (Re-used from old layout for functionality) -->
  <div id="demoModal"
    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 2000; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
    <div
      style="background: var(--white); padding: 3rem; border-radius: 2px; width: 90%; max-width: 500px; position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.2);">
      <div id="closeDemoModal"
        style="position: absolute; top: 20px; right: 20px; font-size: 1.5rem; color: var(--stone); cursor: pointer;">
        &times;</div>
      <div style="text-align: center; margin-bottom: 2rem;">
        <h3 style="font-family: var(--serif); font-size: 1.8rem; margin-bottom: 0.5rem;">Experience Vespr Grocery</h3>
        <p style="color: var(--stone); font-size: 0.9rem;">Please provide your details to access the live demo.</p>
      </div>
      <form id="demoAccessForm">
        @csrf
        <input type="hidden" id="targetDemoUrl">
        <div style="margin-bottom: 1.5rem;">
          <label
            style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem; color: var(--stone);">Business
            Name</label>
          <input type="text" name="business_name"
            style="width: 100%; padding: 1rem; border: 1px solid var(--sand); border-radius: 2px; background: #fcfcfc; font-family: var(--sans);"
            required placeholder="Your Brand Name">
        </div>
        <div style="margin-bottom: 2rem;">
          <label
            style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem; color: var(--stone);">Business
            Email</label>
          <input type="email" name="email"
            style="width: 100%; padding: 1rem; border: 1px solid var(--sand); border-radius: 2px; background: #fcfcfc; font-family: var(--sans);"
            required placeholder="name@company.com">
        </div>
        <button type="submit" id="submitDemoBtn" class="btn-primary" style="width: 100%;">Access Live Demo</button>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Demo Modal Logic
    const demoModal = document.getElementById('demoModal');
    const demoForm = document.getElementById('demoAccessForm');
    const closeDemoBtn = document.getElementById('closeDemoModal');
    const targetInput = document.getElementById('targetDemoUrl');

    window.openDemoAccess = function (e, url) {
      if (e) e.preventDefault();
      if (localStorage.getItem('vespr_demo_accessed')) {
        window.open(url, '_blank');
        return;
      }
      targetInput.value = url;
      demoModal.style.display = 'flex';
      setTimeout(() => {
        demoModal.style.opacity = '1';
      }, 10);
    };

    closeDemoBtn.addEventListener('click', () => {
      demoModal.style.opacity = '0';
      setTimeout(() => {
        demoModal.style.display = 'none';
      }, 300);
    });

    demoForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const submitBtn = document.getElementById('submitDemoBtn');
      const originalText = submitBtn.innerText;
      submitBtn.innerText = 'Processing...';
      submitBtn.disabled = true;

      const formData = new FormData(demoForm);
      formData.append('target_url', targetInput.value);

      fetch('{{ route("demo.request") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(response => response.json())
        .then(data => {
          localStorage.setItem('vespr_demo_accessed', 'true');
          window.open(targetInput.value, '_blank');
          closeDemoBtn.click();
          submitBtn.innerText = originalText;
          submitBtn.disabled = false;
        })
        .catch(error => {
          console.error('Error:', error);
          // Even if mail fails, let them see the demo but alert developer
          localStorage.setItem('vespr_demo_accessed', 'true');
          window.open(targetInput.value, '_blank');
          closeDemoBtn.click();
          submitBtn.innerText = originalText;
          submitBtn.disabled = false;
        });
    });

    // Mobile Menu Toggle
    const mobileNav = document.getElementById('mobileNav');
    const openBtn = document.getElementById('openMobileMenu');
    const closeBtn = document.getElementById('closeMobileMenu');
    const mobileLinks = mobileNav.querySelectorAll('a');

    openBtn.addEventListener('click', () => mobileNav.classList.add('active'));
    closeBtn.addEventListener('click', () => mobileNav.classList.remove('active'));

    mobileLinks.forEach(link => {
      link.addEventListener('click', () => mobileNav.classList.remove('active'));
    });
  </script>
  @yield('scripts')
</body>

</html>