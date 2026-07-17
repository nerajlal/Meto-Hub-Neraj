<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Slot Store — Launch your grocery store</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap"
    rel="stylesheet">
  <style>
    :root {
      --paper: #FDFCFA;
      --paper-dim: #F7F5F0;
      --ink: #1C231B;
      --ink-soft: #4B5245;
      --ink-faint: #7C8177;
      --green: #3F6C4E;
      --green-deep: #2A4A35;
      --green-pale: #E7EFE7;
      --yellow: #E8B93F;
      --yellow-deep: #8A6414;
      --line: #DDD8CB;
      --card: #FFFFFF;
      --radius: 14px;
      --font-display: 'Archivo', sans-serif;
      --font-body: 'Inter', sans-serif;
      --font-mono: 'IBM Plex Mono', monospace;
    }

    * {
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      margin: 0;
      background: var(--paper);
      color: var(--ink);
      font-family: var(--font-body);
      font-size: 16px;
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    img {
      max-width: 100%;
      display: block;
    }

    .wrap {
      max-width: 1180px;
      margin: 0 auto;
      padding: 0 32px;
    }

    section {
      padding: 40px 0;
    }

    h1,
    h2,
    h3 {
      font-family: var(--font-display);
      margin: 0;
      letter-spacing: -0.02em;
    }

    .eyebrow {
      font-family: var(--font-mono);
      font-size: 12.5px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--green-deep);
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 18px;
    }

    .eyebrow::before {
      content: "";
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--yellow);
      display: inline-block;
    }

    /* Shelf-line divider: a hairline with barcode-style ticks */
    .shelf-line {
      display: flex;
      align-items: center;
      gap: 3px;
      height: 14px;
      margin: 0;
    }

    .shelf-line span {
      display: block;
      width: 1.5px;
      height: 100%;
      background: var(--line);
    }

    .shelf-line span:nth-child(3n) {
      height: 60%;
    }

    .shelf-line span:nth-child(5n) {
      height: 40%;
    }

    /* Price tag signature shape */
    .price-tag {
      position: relative;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--yellow);
      color: var(--yellow-deep);
      font-family: var(--font-mono);
      font-size: 13px;
      font-weight: 500;
      padding: 6px 14px 6px 20px;
      clip-path: polygon(14px 0, 100% 0, 100% 100%, 14px 100%, 0 50%);
    }

    .price-tag::before {
      content: "";
      position: absolute;
      left: 6px;
      top: 50%;
      width: 4px;
      height: 4px;
      border-radius: 50%;
      background: var(--paper);
      transform: translateY(-50%);
    }

    /* NAV */
    header {
      position: sticky;
      top: 0;
      z-index: 50;
      background: rgba(253, 252, 250, 0.88);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--line);
    }

    nav {
      max-width: 1180px;
      margin: 0 auto;
      padding: 0 32px;
      height: 76px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo {
      font-family: var(--font-display);
      font-weight: 800;
      font-size: 21px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .logo-mark {
      width: 22px;
      height: 22px;
      background: var(--green);
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
    }

    .nav-links {
      display: flex;
      gap: 36px;
      font-size: 14.5px;
      color: var(--ink-soft);
    }

    .nav-links a:hover {
      color: var(--ink);
    }

    .nav-cta {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .btn {
      font-family: var(--font-body);
      font-weight: 600;
      font-size: 14.5px;
      padding: 11px 22px;
      border-radius: 9px;
      display: inline-block;
      border: 1px solid transparent;
      cursor: pointer;
      transition: transform .15s ease, background .15s ease;
    }

    .btn:hover {
      transform: translateY(-1px);
    }

    .btn-primary {
      background: var(--ink);
      color: var(--paper);
    }

    .btn-primary:hover {
      background: var(--green-deep);
    }

    .btn-ghost {
      color: var(--ink);
    }

    .btn-outline {
      border-color: var(--line);
      color: var(--ink);
    }

    .btn-outline:hover {
      border-color: var(--ink);
    }

    /* HERO */
    .hero {
      padding: 88px 0 60px;
    }

    .hero-grid {
      display: grid;
      grid-template-columns: 1.05fr 0.95fr;
      gap: 64px;
      align-items: center;
    }

    .hero h1 {
      font-size: 56px;
      line-height: 1.04;
      font-weight: 700;
      margin-bottom: 22px;
    }

    .hero h1 em {
      font-style: normal;
      color: var(--green);
      position: relative;
    }

    .hero p.lede {
      font-size: 18px;
      color: var(--ink-soft);
      max-width: 460px;
      margin-bottom: 32px;
    }

    .hero-actions {
      display: flex;
      gap: 14px;
      align-items: center;
      margin-bottom: 28px;
    }

    .hero-note {
      font-size: 13.5px;
      color: var(--ink-faint);
      font-family: var(--font-mono);
    }

    /* Hero mockup: a little grocery site preview with browser chrome + price tags */
    .mockup {
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: 16px;
      overflow: hidden;
      position: relative;
    }

    .mockup-bar {
      height: 38px;
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 0 14px;
      border-bottom: 1px solid var(--line);
      background: var(--paper-dim);
    }

    .mockup-bar span {
      width: 9px;
      height: 9px;
      border-radius: 50%;
      background: var(--line);
    }

    .mockup-body {
      padding: 22px;
      position: relative;
    }

    .mockup-store-name {
      font-family: var(--font-display);
      font-weight: 700;
      font-size: 19px;
      margin-bottom: 4px;
    }

    .mockup-store-sub {
      font-size: 12.5px;
      color: var(--ink-faint);
      margin-bottom: 18px;
    }

    .mockup-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 12px;
    }

    .produce-card {
      background: var(--paper-dim);
      border-radius: 10px;
      padding: 14px 12px;
      position: relative;
      border: 1px solid var(--line);
    }

    .produce-swatch {
      width: 100%;
      height: 56px;
      border-radius: 7px;
      margin-bottom: 10px;
    }

    .produce-name {
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 2px;
    }

    .produce-tag {
      font-family: var(--font-mono);
      font-size: 11.5px;
      color: var(--green-deep);
    }

    .floating-tag {
      position: absolute;
      top: -14px;
      right: 18px;
      transform: rotate(4deg);
      z-index: 5;
    }

    /* HOW IT WORKS */
    .steps {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0;
      border-top: 1px solid var(--line);
      border-bottom: 1px solid var(--line);
    }

    .step {
      padding: 40px 36px;
      border-right: 1px solid var(--line);
    }

    .step:last-child {
      border-right: none;
    }

    .step-num {
      font-family: var(--font-mono);
      font-size: 13px;
      color: var(--green);
      margin-bottom: 16px;
      display: block;
    }

    .step h3 {
      font-size: 20px;
      margin-bottom: 10px;
      font-weight: 600;
    }

    .step p {
      color: var(--ink-soft);
      font-size: 14.5px;
      margin: 0;
    }

    /* SECTION HEADS */
    .section-head {
      max-width: 560px;
      margin-bottom: 56px;
    }

    .section-head h2 {
      font-size: 36px;
      font-weight: 700;
      line-height: 1.15;
      margin-bottom: 14px;
    }

    .section-head p {
      color: var(--ink-soft);
      font-size: 16.5px;
      margin: 0;
    }

    /* FEATURES */
    .feature-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1px;
      background: var(--line);
      border: 1px solid var(--line);
      border-radius: 16px;
      overflow: hidden;
    }

    .feature {
      background: var(--card);
      padding: 32px;
    }

    .feature-icon {
      width: 38px;
      height: 38px;
      border-radius: 9px;
      background: var(--green-pale);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 18px;
    }

    .feature-icon svg {
      width: 19px;
      height: 19px;
      stroke: var(--green-deep);
    }

    .feature h3 {
      font-size: 16.5px;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .feature p {
      font-size: 14px;
      color: var(--ink-soft);
      margin: 0;
      line-height: 1.55;
    }

    /* TEMPLATES */
    .template-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px;
    }

    .template-card {
      border: 1px solid var(--line);
      border-radius: 14px;
      overflow: hidden;
      background: var(--card);
      transition: border-color .15s ease;
    }

    .template-card:hover {
      border-color: var(--ink-faint);
    }

    .template-preview {
      height: 170px;
      position: relative;
      overflow: hidden;
    }

    .template-info {
      padding: 16px 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .template-info h4 {
      font-size: 14.5px;
      font-weight: 600;
      margin: 0;
    }

    .template-info span {
      font-family: var(--font-mono);
      font-size: 12px;
      color: var(--ink-faint);
    }

    /* PRICING — receipt style */
    .pricing-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
      align-items: start;
    }

    .plan {
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 32px;
      position: relative;
    }

    .plan.featured {
      border: 2px solid var(--green);
    }

    .plan-badge {
      position: absolute;
      top: -13px;
      left: 32px;
      background: var(--green);
      color: var(--paper);
      font-family: var(--font-mono);
      font-size: 11px;
      letter-spacing: .05em;
      padding: 4px 12px;
      border-radius: 999px;
      text-transform: uppercase;
    }

    .plan h3 {
      font-size: 15px;
      font-weight: 600;
      color: var(--ink-soft);
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: .04em;
      font-family: var(--font-mono);
      font-weight: 500;
    }

    .plan-price {
      font-family: var(--font-display);
      font-size: 40px;
      font-weight: 700;
      margin-bottom: 4px;
    }

    .plan-price span {
      font-family: var(--font-body);
      font-size: 15px;
      font-weight: 400;
      color: var(--ink-faint);
    }

    .plan-desc {
      font-size: 13.5px;
      color: var(--ink-faint);
      margin-bottom: 24px;
    }

    .receipt-divider {
      border: none;
      border-top: 1.5px dashed var(--line);
      margin: 22px 0;
    }

    .plan-list {
      list-style: none;
      padding: 0;
      margin: 0 0 28px;
      font-family: var(--font-mono);
      font-size: 13px;
    }

    .plan-list li {
      display: flex;
      justify-content: space-between;
      padding: 7px 0;
      color: var(--ink-soft);
    }

    .plan-list li b {
      color: var(--ink);
      font-weight: 500;
    }

    /* FAQ */
    .faq-layout {
      display: grid;
      grid-template-columns: 1.1fr 1.6fr;
      gap: 64px;
    }

    .faq-left h2 {
      font-size: 42px;
      margin: 16px 0 24px;
    }

    .faq-left p {
      color: var(--ink-soft);
      font-size: 15.5px;
      margin-bottom: 32px;
      max-width: 320px;
    }

    @media (max-width: 920px) {
      .faq-layout {
        grid-template-columns: 1fr;
        gap: 40px;
      }

      .faq-left p {
        max-width: 100%;
      }
    }

    .faq-item {
      border-bottom: 1px solid var(--line);
      padding: 26px 0;
      cursor: pointer;
    }

    .faq-q {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 16.5px;
      font-weight: 600;
      font-family: var(--font-display);
    }

    .faq-plus {
      font-family: var(--font-mono);
      font-size: 20px;
      color: var(--green);
      transition: transform .2s ease;
    }

    .faq-item.open .faq-plus {
      transform: rotate(45deg);
    }

    .faq-a {
      max-height: 0;
      overflow: hidden;
      transition: max-height .25s ease;
      color: var(--ink-soft);
      font-size: 14.5px;
      line-height: 1.65;
    }

    .faq-item.open .faq-a {
      max-height: 200px;
      padding-top: 14px;
    }

    /* CTA BAND */
    .cta-band {
      background: var(--ink);
      color: var(--paper);
      border-radius: 20px;
      padding: 64px 56px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 40px;
    }

    .cta-band h2 {
      font-size: 32px;
      font-weight: 700;
      color: var(--paper);
      margin-bottom: 10px;
    }

    .cta-band p {
      color: #B9BEB2;
      font-size: 15px;
      margin: 0;
    }

    .cta-band .btn-primary {
      background: var(--yellow);
      color: var(--yellow-deep);
      flex-shrink: 0;
    }

    .cta-band .btn-primary:hover {
      background: #f0c95c;
    }

    /* FOOTER */
    footer {
      border-top: 1px solid var(--line);
      padding: 56px 0 40px;
    }

    .footer-grid {
      display: grid;
      grid-template-columns: 1.4fr 1fr 1fr 1fr;
      gap: 32px;
      margin-bottom: 48px;
    }

    .footer-col h5 {
      font-family: var(--font-mono);
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--ink-faint);
      margin-bottom: 16px;
      font-weight: 500;
    }

    .footer-col a {
      display: block;
      font-size: 14px;
      color: var(--ink-soft);
      margin-bottom: 10px;
    }

    .footer-col a:hover {
      color: var(--ink);
    }

    .footer-bottom {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 28px;
      border-top: 1px solid var(--line);
      font-size: 13px;
      color: var(--ink-faint);
    }
    
    .nav-icon { 
      display: none; 
    }
    
    .contact-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 32px;
    }

    .contact-card {
      text-align: center;
      padding: 48px 32px;
      border: 1px solid var(--line);
      border-radius: 12px;
      background: #fff;
    }

    @media (max-width:920px) {
      section {
        padding: 64px 0;
      }

      .nav-links {
        display: none;
      }

      .hero-grid {
        grid-template-columns: 1fr;
      }

      .hero h1 {
        font-size: 38px;
      }

      .steps {
        grid-template-columns: 1fr;
      }

      .step {
        border-right: none;
        border-bottom: 1px solid var(--line);
      }

      .step:last-child {
        border-bottom: none;
      }

      .feature-grid {
        grid-template-columns: 1fr;
      }

      .contact-card {
        padding: 24px 16px;
      }

      .template-grid {
        grid-template-columns: 1fr;
      }

      .pricing-grid {
        grid-template-columns: 1fr;
      }

      .footer-grid {
        grid-template-columns: 1fr 1fr;
      }

      .cta-band {
        flex-direction: column;
        text-align: center;
        padding: 48px 28px;
      }

      .contact-grid {
        grid-template-columns: 1fr;
      }

      .nav-text { display: none; }
      .nav-icon { display: inline-block !important; }
    }
  </style>
</head>

<body>

  <header>
    <nav>
      <div class="logo"><span class="logo-mark"></span>Slot Store</div>
      <div class="nav-links">
        <a href="#how">How it works</a>
        <a href="#templates">Templates</a>
        <a href="#pricing">Pricing</a>
        <a href="#faq">FAQ</a>
      </div>
      <div class="nav-cta">
        <a href="{{ route('admin.common.login') }}" class="btn btn-ghost" style="display: inline-flex; align-items: center; justify-content: center;">
          <span class="nav-text">Log in</span>
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </a>
        <a href="javascript:void(0)" class="btn btn-primary pricing-btn-trigger" data-plan="sprout">
          <span class="nav-text">Start free</span>
          <span class="nav-icon">Start</span>
        </a>
      </div>
    </nav>
  </header>

  <section class="hero">
    <div class="wrap">
      <div class="hero-grid">
        <div>
          <span class="eyebrow">Built for independent grocers</span>
          <h1>Stock the shelves.<br>We'll build <em>the store.</em></h1>
          <p class="lede">Slot Store turns your inventory into a real online grocery store — orders, delivery zones,
            and payments included. No code, no developer, live by tonight.</p>
          <div class="hero-actions">
            <a href="javascript:void(0)" class="btn btn-primary pricing-btn-trigger" data-plan="sprout">Start your store
              — free</a>
            <a href="#templates" class="btn btn-outline">See templates</a>
          </div>
          <span class="hero-note">No card required · 14-day trial · cancel anytime</span>
        </div>

        <div class="mockup">
          <div class="floating-tag">
            <div class="price-tag">fresh today</div>
          </div>
          <div class="mockup-bar"><span></span><span></span><span></span></div>
          <div class="mockup-body">
            <div class="mockup-store-name">Corner Market Co.</div>
            <div class="mockup-store-sub">Open now · Delivers to 5 zip codes</div>
            <div class="mockup-grid">
              <div class="produce-card">
                <div class="produce-swatch" style="background:#C7DDB0"></div>
                <div class="produce-name">Avocados</div>
                <div class="produce-tag">$1.20 / ea</div>
              </div>
              <div class="produce-card">
                <div class="produce-swatch" style="background:#E7C77E"></div>
                <div class="produce-name">Sourdough</div>
                <div class="produce-tag">$5.50 / loaf</div>
              </div>
              <div class="produce-card">
                <div class="produce-swatch" style="background:#D69B7E"></div>
                <div class="produce-name">Heirloom tomatoes</div>
                <div class="produce-tag">$3.90 / lb</div>
              </div>
              <div class="produce-card">
                <div class="produce-swatch" style="background:#BFD4E0"></div>
                <div class="produce-name">Whole milk</div>
                <div class="produce-tag">$4.10 / gal</div>
              </div>
              <div class="produce-card">
                <div class="produce-swatch" style="background:#EAE0A8"></div>
                <div class="produce-name">Farm eggs</div>
                <div class="produce-tag">$6.20 / dz</div>
              </div>
              <div class="produce-card">
                <div class="produce-swatch" style="background:#C9A7C4"></div>
                <div class="produce-name">Red grapes</div>
                <div class="produce-tag">$2.80 / lb</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="wrap">
    <div class="shelf-line">
      <script>document.write(Array(90).fill('<span></span>').join(''))</script>
    </div>
  </div>

  <section id="how" style="padding-top:64px;">
    <div class="wrap">
      <div class="section-head">
        <span class="eyebrow">The process</span>
        <h2>From spreadsheet to storefront</h2>
        <p>Three steps, in the order they actually happen when you open a store.</p>
      </div>
      <div class="steps">
        <div class="step">
          <span class="step-num">01 · stock</span>
          <h3>Add your inventory</h3>
          <p>Import a spreadsheet or add items one by one — name, price, unit, photo. Slot Store organizes it into
            aisles automatically.</p>
        </div>
        <div class="step">
          <span class="step-num">02 · set terms</span>
          <h3>Set prices and delivery zones</h3>
          <p>Draw your delivery radius on a map, set fees and minimums, and choose which payment methods you accept.</p>
        </div>
        <div class="step">
          <span class="step-num">03 · open</span>
          <h3>Open the doors</h3>
          <p>Publish to your own domain. Orders land in one dashboard — pack, mark ready, and hand off to your driver.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="wrap">
      <div class="section-head">
        <span class="eyebrow">What's included</span>
        <h2>Everything a grocery order actually needs</h2>
        <p>Not a generic store builder with grocery icons bolted on — every feature here exists because a real order
          needs it.</p>
      </div>
      <div class="feature-grid">
        <div class="feature">
          <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8">
              <path d="M3 9h18M3 15h18M9 3v18M15 3v18" stroke-linecap="round" />
            </svg></div>
          <h3>Aisle-based catalog</h3>
          <p>Organize items by aisle and category the way shoppers already think — produce, dairy, pantry, frozen.</p>
        </div>
        <div class="feature">
          <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8">
              <path
                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
              </path>
              <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
              <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg></div>
          <h3>Zoho Integration</h3>
          <p>Seamlessly connect your storefront to Zoho. Automatically sync your inventory, customer records, and daily
            sales.</p>
        </div>

        <div class="feature">
          <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8">
              <path d="M20 12H4M14 6l6 6-6 6" />
            </svg></div>
          <h3>Delivery zone maps</h3>
          <p>Draw exactly where you deliver, set per-zone fees, and block orders from outside your reach.</p>
        </div>

        <div class="feature">
          <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8">
              <path d="M4 4h16v4H4zM4 10h16v10H4z" />
              <path d="M9 14h6" />
            </svg></div>
          <h3>One order dashboard</h3>
          <p>See every order from cart to doorstep — pack, print labels, and hand off without switching tabs.</p>
        </div>
        <div class="feature">
          <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8">
              <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
              <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg></div>
          <h3>Bundle & Pack Deals</h3>
          <p>Group items into weekly combos or offer volume pack discounts to instantly boost average order sizes.</p>
        </div>
        <div class="feature">
          <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg></div>
          <h3>Geo-Location Autofill</h3>
          <p>Reduce cart abandonment with Google Maps address autocomplete and live GPS location capture at checkout.
          </p>
        </div>

      </div>
    </div>
  </section>

  <section id="templates">
    <div class="wrap">
      <div class="section-head">
        <span class="eyebrow">Storefront templates</span>
        <h2>Pick a layout, then make it yours</h2>
        <p>Every template ships with real grocery structure already in place — swap colors, fonts, and your logo.</p>
      </div>
      <div class="template-grid">
        <div class="template-card" data-demo-url="{{ route('v1.home') }}?preview=1&tenant_id=2">
          <div class="template-preview"
            style="background:linear-gradient(180deg,#F7F5F0 0%,#F7F5F0 60%,#fff 60%);padding:16px;">
            <div style="font-family:'Archivo';font-weight:700;font-size:13px;margin-bottom:8px;">Market Basic</div>
            <div style="display:flex;gap:6px;">
              <div style="width:30%;height:60px;background:#DDD8CB;border-radius:6px;"></div>
              <div style="width:30%;height:60px;background:#E7EFE7;border-radius:6px;"></div>
              <div style="width:30%;height:60px;background:#F2E4C4;border-radius:6px;"></div>
            </div>
          </div>
          <div class="template-info">
            <h4>Market basic</h4><span>Minimal</span>
          </div>
        </div>
        <div class="template-card" data-demo-url="{{ route('velvet.home') }}?preview=1&tenant_id=2">
          <div class="template-preview" style="background:#1C231B;padding:16px;">
            <div style="font-family:'Archivo';font-weight:700;font-size:13px;margin-bottom:8px;color:#fff;">Night Grocer
            </div>
            <div style="display:flex;gap:6px;">
              <div style="width:30%;height:60px;background:#3F6C4E;border-radius:6px;"></div>
              <div style="width:30%;height:60px;background:#4B5245;border-radius:6px;"></div>
              <div style="width:30%;height:60px;background:#E8B93F;border-radius:6px;"></div>
            </div>
          </div>
          <div class="template-info">
            <h4>Night grocer</h4><span>Bold</span>
          </div>
        </div>
        <div class="template-card" data-demo-url="{{ route('v3.home') }}?preview=1&tenant_id=2">
          <div class="template-preview" style="background:#F4F6F8;padding:16px; border-bottom: 1px solid var(--line);">
            <div style="font-family:'Archivo';font-weight:700;font-size:13px;margin-bottom:8px;color:#111827;">Fresh App
            </div>
            <div style="display:flex;gap:6px;">
              <div style="width:30%;height:60px;background:#10B981;border-radius:12px;"></div>
              <div style="width:30%;height:60px;background:#EF4444;border-radius:12px;"></div>
              <div style="width:30%;height:60px;background:#F59E0B;border-radius:12px;"></div>
            </div>
          </div>
          <div class="template-info">
            <h4>Fresh App</h4><span>Modern Mobile</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="pricing">
    <div class="wrap">
      <div class="section-head">
        <span class="eyebrow">Pricing</span>
        <h2>One plan for one store. Simple as a receipt.</h2>
        <p>Every plan includes the storefront, orders, and payments. Higher tiers add more zones, staff, and volume.</p>
      </div>
      <div class="pricing-grid">
        <div class="plan">
          <h3>Corner store</h3>
          <div class="plan-price">$0<span>/mo</span></div>
          <div class="plan-desc">For testing the waters — one delivery zone, up to 100 items.</div>
          <hr class="receipt-divider">
          <ul class="plan-list">
            <li><span>Storefront</span><b>1</b></li>
            <li><span>Delivery zones</span><b>1</b></li>
            <li><span>Catalog items</span><b>100</b></li>
            <li><span>Staff accounts</span><b>1</b></li>
            <li><span>Transaction fee</span><b>2.9%</b></li>
          </ul>
          <a href="#" class="btn btn-outline" style="width:100%;text-align:center;">Start free</a>
        </div>
        <div class="plan featured">
          <span class="plan-badge">most chosen</span>
          <h3>Full aisle</h3>
          <div class="plan-price">$49<span>/mo</span></div>
          <div class="plan-desc">For a store that's actually taking orders every day.</div>
          <hr class="receipt-divider">
          <ul class="plan-list">
            <li><span>Storefront</span><b>1</b></li>
            <li><span>Delivery zones</span><b>10</b></li>
            <li><span>Catalog items</span><b>Unlimited</b></li>
            <li><span>Staff accounts</span><b>5</b></li>
            <li><span>Transaction fee</span><b>1.5%</b></li>
          </ul>
          <a href="#" class="btn btn-primary" style="width:100%;text-align:center;">Start your store</a>
        </div>
        <div class="plan">
          <h3>Warehouse</h3>
          <div class="plan-price">$149<span>/mo</span></div>
          <div class="plan-desc">For multi-location grocers and growing chains.</div>
          <hr class="receipt-divider">
          <ul class="plan-list">
            <li><span>Storefronts</span><b>5</b></li>
            <li><span>Delivery zones</span><b>Unlimited</b></li>
            <li><span>Catalog items</span><b>Unlimited</b></li>
            <li><span>Staff accounts</span><b>Unlimited</b></li>
            <li><span>Transaction fee</span><b>0.9%</b></li>
          </ul>
          <a href="#" class="btn btn-outline" style="width:100%;text-align:center;">Talk to us</a>
        </div>
      </div>
    </div>
  </section>

  <section id="faq">
    <div class="wrap">
      <div class="faq-layout">
        <div class="faq-left">
          <span class="eyebrow">FAQ</span>
          <h2>Common questions</h2>
          <p>Still not sure? Reach out to the Slot Store team — we typically respond within a few hours.</p>
          <!-- <a href="#" class="btn btn-outline">Contact us</a> -->
        </div>
        <div class="faq-right">
          <div class="faq-item open">
            <div class="faq-q"><span>Do I need any code to set this up?</span><span class="faq-plus">+</span></div>
            <div class="faq-a">No. Add your catalog, pick a template, set your delivery zones, and publish. Most stores
              go
              live in under an hour.</div>
          </div>
          <div class="faq-item">
            <div class="faq-q"><span>Can I use my own domain?</span><span class="faq-plus">+</span></div>
            <div class="faq-a">Yes — connect a domain you already own, or buy one during setup. Every plan supports a
              custom
              domain.</div>
          </div>
          <div class="faq-item">
            <div class="faq-q"><span>How does delivery actually work?</span><span class="faq-plus">+</span></div>
            <div class="faq-a">You draw your delivery zones on a map and set fees per zone. Orders route to your
              dashboard,
              where you mark them packed and ready for your own drivers or a courier partner.</div>
          </div>
          <div class="faq-item">
            <div class="faq-q"><span>What happens after the trial?</span><span class="faq-plus">+</span></div>
            <div class="faq-a">Your store stays live. Pick a plan that fits, or downgrade to Corner store — nothing is
              deleted.</div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section id="contact">
    <div class="wrap">
      <div class="section-head">
        <span class="eyebrow">Contact us</span>
        <h2>We're here to help</h2>
        <p>Have questions before you start or need technical support? Reach out to our team.</p>
      </div>
      <div class="contact-grid">
        <div class="contact-card">
          <div class="feature-icon" style="margin: 0 auto 20px;"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"
              stroke="currentColor">
              <path
                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
              </path>
            </svg></div>
          <h3>Talk to Sales</h3>
          <p style="margin-bottom:24px;">Need a custom plan or a live demo? We're available Mon-Fri, 9am-6pm.</p>
          <a href="tel:+18001234567" class="btn btn-outline">Call +91 70126 39646</a>
        </div>
        <div class="contact-card">
          <div class="feature-icon" style="margin: 0 auto 20px;"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"
              stroke="currentColor">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
              <polyline points="22,6 12,13 2,6"></polyline>
            </svg></div>
          <h3>Email Support</h3>
          <p style="margin-bottom:24px;">Current customer needing technical help? Drop us a line anytime.</p>
          <a href="mailto:support@slotstore.com" class="btn btn-outline">support@task19.com</a>
        </div>
      </div>
    </div>
  </section>


  <section style="padding-top:0;">
    <div class="wrap">
      <div class="cta-band">
        <div>
          <h2>Your shelves are ready. Is your store?</h2>
          <p>Set up your first storefront in the time it takes to restock a display.</p>
        </div>
        <a href="javascript:void(0)" class="btn btn-primary pricing-btn-trigger" data-plan="sprout">Start your store —
          free</a>
      </div>
    </div>
  </section>

  <footer>
    <div class="wrap">
      <div class="footer-grid">
        <div class="footer-col">
          <div class="logo" style="margin-bottom:14px;"><span class="logo-mark"></span>Slot Store</div>
          <p style="font-size:13.5px;color:var(--ink-faint);max-width:240px;">The storefront builder for independent
            grocers, made for real inventory and real delivery routes.</p>
        </div>
        <div class="footer-col">
          <h5>Product</h5>
          <a href="#how">How it works</a>
          <a href="#templates">Templates</a>
          <a href="#pricing">Pricing</a>
        </div>
        <div class="footer-col">
          <h5>Company</h5>
          <a href="#">About</a>
          <a href="#">Contact</a>
          <a href="#">Careers</a>
        </div>
        <div class="footer-col">
          <h5>Legal</h5>
          <a href="#">Terms</a>
          <a href="#">Privacy</a>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2026 Slot Store. Made for grocers.</span>
        <span>Kayamkulam · Remote</span>
      </div>
    </div>
  </footer>

  <script>
    document.querySelectorAll('.faq-item').forEach(item => {
      item.addEventListener('click', () => {
        const wasOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
        if (!wasOpen) item.classList.add('open');
      });
    });
  </script>



  <!-- SAAS SIGN-UP MODAL -->
  <div class="saas-modal-overlay" id="saasModal">
    <div class="saas-modal-card">
      <button class="saas-modal-close" id="closeSaasBtn" aria-label="Close Registration">&times;</button>

      <!-- STAGE 2: Sign-Up Form (Multi-Step Onboarding) -->
      <div id="saasStageSignUp" class="saas-stage active">
        <form id="saasRegisterForm" onsubmit="handleSaasRegister(event)">

          <!-- STEP 1: Tell us about yourself -->
          <div id="saasFormStep1" class="saas-form-step active">
            <div class="saas-modal-header">
              <span class="saas-badge class-plan-badge">Starter</span>
              <h2>Tell us about yourself</h2>
              <p class="saas-lead">Provide your details to initiate your premium grocery store</p>
            </div>

            <div class="saas-form-group">
              <label for="saas_name">Full Name *</label>
              <input type="text" id="saas_name" placeholder="e.g. Priya Sharma" required />
              <span class="saas-error" id="err_name"></span>
            </div>

            <div class="saas-form-group">
              <label for="saas_email">Email Address *</label>
              <input type="email" id="saas_email" placeholder="you@yourbrand.com" required />
              <span class="saas-error" id="err_email"></span>
            </div>
          </div>

          <!-- STEP 2: Tell us about your brand -->
          <div id="saasFormStep2" class="saas-form-step">
            <div class="saas-modal-header">
              <span class="saas-badge class-plan-badge">Starter</span>
              <h2>Tell us about your brand</h2>
              <p class="saas-lead">We will optimize your dashboard tailored to your company goals</p>
            </div>

            <div class="saas-form-group">
              <label for="saas_business">Business / Brand Name *</label>
              <input type="text" id="saas_business" placeholder="e.g. Noir Atelier" required />
              <span class="saas-error" id="err_business"></span>
            </div>

            <div class="saas-form-grid">
              <div class="saas-form-group">
                <label for="saas_country">Country *</label>
                <select id="saas_country" required>
                  <option value="" disabled selected>Select country</option>
                  <option value="India">India</option>
                  <option value="United Arab Emirates">United Arab Emirates</option>
                  <option value="Saudi Arabia">Saudi Arabia</option>
                  <option value="United Kingdom">United Kingdom</option>
                  <option value="United States">United States</option>
                  <option value="France">France</option>
                  <option value="Singapore">Singapore</option>
                  <option value="Australia">Australia</option>
                  <option value="Other">Other</option>
                </select>
                <span class="saas-error" id="err_country"></span>
              </div>

              <div class="saas-form-group">
                <label for="saas_whatsapp">WhatsApp Number *</label>
                <div class="saas-phone-input-wrapper">
                  <select id="saas_phone_code" class="saas-phone-code">
                    <option value="+91" selected>🇮🇳 +91</option>
                    <option value="+971">🇦🇪 +971</option>
                    <option value="+966">🇸🇦 +966</option>
                    <option value="+44">🇬🇧 +44</option>
                    <option value="+1">🇺🇸 +1</option>
                    <option value="+33">🇫🇷 +33</option>
                    <option value="+65">🇸🇬 +65</option>
                    <option value="+61">🇦🇺 +61</option>
                  </select>
                  <input type="tel" id="saas_whatsapp" placeholder="98765 43210" required />
                </div>
                <span class="saas-error" id="err_whatsapp"></span>
              </div>
            </div>
          </div>

          <!-- STEP 3: Configure your credentials & theme -->
          <div id="saasFormStep3" class="saas-form-step">
            <div class="saas-modal-header">
              <span class="saas-badge class-plan-badge">Starter</span>
              <h2>Configure store settings</h2>
              <p class="saas-lead">Secure your brand dashboard and select your pricing plan</p>
            </div>

            <div class="saas-form-grid">
              <div class="saas-form-group">
                <label for="saas_password">Password *</label>
                <input type="password" id="saas_password" name="password" autocomplete="new-password"
                  placeholder="At least 8 characters" required />
                <span class="saas-error" id="err_password"></span>
              </div>

              <div class="saas-form-group">
                <label for="saas_confirm_password">Confirm Password *</label>
                <input type="password" id="saas_confirm_password" name="password_confirmation"
                  autocomplete="new-password" placeholder="Re-enter password" required />
                <span class="saas-error" id="err_confirm_password"></span>
              </div>
            </div>


            <div class="saas-form-grid">
              <div class="saas-form-group" style="grid-column: 1 / -1;">
                <label for="saas_plan">Plan Selection (Optional)</label>
                <select id="saas_plan">
                  <option value="sprout">Sprout — $9/month (20 products)</option>
                  <option value="maison">Maison — $19/month (100 products)</option>
                  <option value="heritage">Heritage — $49/month (Unlimited)</option>
                  <option value="not_sure">Not sure yet</option>
                </select>
              </div>
              <input type="hidden" id="saas_theme" value="aura_luxe" />
            </div>
          </div>

          <!-- PROGRESS STEPS NAVIGATION BAR -->
          <div class="saas-step-nav">
            <div class="saas-dots" id="saasStepDots">
              <span class="saas-dot active" onclick="goToStep(1)"></span>
              <span class="saas-dot" onclick="goToStep(2)"></span>
              <span class="saas-dot" onclick="goToStep(3)"></span>
            </div>

            <div class="saas-nav-actions">
              <button type="button" class="saas-back-btn" id="saasBackBtn" onclick="prevStep()">Back</button>
              <button type="button" class="saas-next-btn" id="saasNextBtn" onclick="handleStepNavNext()">Next</button>
            </div>
          </div>

        </form>
      </div>

      <!-- STAGE 3: Email Verification -->
      <div id="saasStageVerify" class="saas-stage">
        <div class="saas-verify-container">
          <div class="saas-verify-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
              <polyline points="22,6 12,13 2,6" />
            </svg>
          </div>

          <h2>Check your inbox</h2>
          <p class="saas-verify-desc">We've sent a verification link to <strong id="verifyEmailDisplay">your
              email</strong>. Please click the link to verify your email and continue setting up your store.</p>

          <div class="saas-verify-meta">
            <p>Didn't receive the email?</p>
            <button id="resendBtn" class="saas-resend-btn" onclick="handleResendCode()">Resend link</button>
            <p id="resendCountdown" class="saas-countdown-label"></p>
          </div>

          <!-- Simulation Tool to Help User Verify Frontend Flow -->
          <div class="saas-simulation-box">
            <span class="sim-badge">SaaS Simulation Tool</span>
            <p>Click below to simulate clicking the verification link in your email.</p>
            <button onclick="simulateVerificationSuccess()" class="saas-simulate-btn">Simulate Email Verification Link
              Click ✓</button>
          </div>
        </div>
      </div>

      <!-- STAGE 4: Success / Welcome Screen -->
      <div id="saasStageSuccess" class="saas-stage">
        <div class="saas-success-container">
          <div class="saas-success-icon-check">✓</div>
          <h2>Account Verified!</h2>
          <p>Your email has been verified successfully. Welcome to Slot Store! Let's get started setting up your
            customized
            grocery store.</p>
          <button onclick="closeSaaSModal()" class="saas-success-finish-btn">Go to your SaaS dashboard →</button>
        </div>
      </div>

    </div>
  </div>

  <style>
    .saas-modal-overlay,
    .demo-modal-overlay {
      --black: #1C231B;
      --white: #FFFFFF;
      --cream: #FDFCFA;
      --gold: #3F6C4E;
      --gold-light: #E7EFE7;
      --gold-dark: #2A4A35;
      --gray-100: #F7F5F0;
      --gray-200: #DDD8CB;
      --gray-400: #7C8177;
      --gray-600: #4B5245;
      --gray-800: #2A4A35;
      --text: #1C231B;
      --text-muted: #4B5245;
      --border: #DDD8CB;
    }

    /* ── MOBILE PREVIEW MODAL ── */
    .demo-modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(10, 10, 10, 0.75);
      backdrop-filter: blur(16px);
      z-index: 2000;
      display: none;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .demo-modal-overlay.active {
      display: flex;
      opacity: 1;
    }

    .demo-modal-close {
      position: absolute;
      top: 24px;
      right: 24px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      width: 48px;
      height: 48px;
      border-radius: 50%;
      color: var(--white);
      font-size: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.3s, transform 0.3s, border-color 0.3s;
      z-index: 2010;
    }

    .demo-modal-close:hover {
      background: rgba(201, 168, 76, 0.2);
      border-color: rgba(201, 168, 76, 0.4);
      color: var(--gold);
      transform: scale(1.08) rotate(90deg);
    }

    .phone-mockup-wrapper {
      position: relative;
      width: 414px;
      height: 860px;
      background: #09090b;
      border: 12px solid #1c1c1e;
      border-radius: 48px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.55),
        0 0 0 2px rgba(255, 255, 255, 0.05),
        inset 0 0 8px rgba(0, 0, 0, 0.8);
      transform: scale(0.8) translateY(40px);
      transform-origin: center;
      opacity: 0;
      transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease;
    }

    .demo-modal-overlay.active .phone-mockup-wrapper {
      transform: scale(0.85) translateY(0);
      opacity: 1;
    }

    /* Physical Side Buttons */
    .phone-button {
      position: absolute;
      background: #27272a;
      border-radius: 3px;
      z-index: -1;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
    }

    .phone-button.volume-up {
      left: -15px;
      top: 150px;
      width: 3px;
      height: 50px;
    }

    .phone-button.volume-down {
      left: -15px;
      top: 212px;
      width: 3px;
      height: 50px;
    }

    .phone-button.power {
      right: -15px;
      top: 180px;
      width: 3px;
      height: 80px;
    }

    .phone-screen {
      width: 100%;
      height: 100%;
      background: #fff;
      border-radius: 36px;
      overflow: hidden;
      position: relative;
      display: flex;
      flex-direction: column;
    }

    /* iOS Status Bar */
    .phone-status-bar {
      position: relative;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      background: #fff;
      z-index: 15;
      font-size: 11px;
      font-weight: 600;
      color: #000;
      user-select: none;
      transition: background-color 0.4s ease, color 0.4s ease;
    }

    .status-time {
      letter-spacing: -0.1px;
    }

    .phone-notch {
      position: absolute;
      top: 6px;
      left: 50%;
      transform: translateX(-50%);
      width: 110px;
      height: 24px;
      background: #000;
      border-radius: 12px;
      z-index: 20;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .notch-camera {
      width: 8px;
      height: 8px;
      background: #05051a;
      border-radius: 50%;
      box-shadow: inset 0 0 2px rgba(255, 255, 255, 0.4);
    }

    .status-icons {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .status-icon {
      width: 14px;
      height: 14px;
      fill: currentColor;
    }

    .battery-percentage {
      font-size: 10px;
    }

    .battery-icon {
      width: 20px;
      height: 10px;
      border: 1px solid currentColor;
      border-radius: 3px;
      padding: 1px;
      display: flex;
      align-items: center;
      position: relative;
    }

    .battery-level {
      width: 80%;
      height: 100%;
      background: currentColor;
      border-radius: 1px;
    }

    .phone-screen iframe {
      flex: 1;
      width: 100%;
      border: none;
      background: #fff;
    }

    .phone-home-indicator {
      position: absolute;
      bottom: 8px;
      left: 50%;
      transform: translateX(-50%);
      width: 120px;
      height: 4.5px;
      background: rgba(0, 0, 0, 0.5);
      border-radius: 2.25px;
      z-index: 15;
      pointer-events: none;
      transition: background-color 0.4s ease;
    }

    .phone-loader {
      position: absolute;
      inset: 0;
      background: var(--cream);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 5;
      transition: opacity 0.4s ease, visibility 0.4s ease;
    }

    .phone-loader.hidden {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }

    .spinner {
      width: 40px;
      height: 40px;
      border: 3px solid rgba(201, 168, 76, 0.1);
      border-top: 3px solid var(--gold);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 16px;
    }

    /* ── DEVICE TOGGLE ── */
    .device-toggle-wrapper {
      position: absolute;
      top: 24px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      padding: 4px;
      border-radius: 30px;
      display: flex;
      gap: 4px;
      z-index: 2010;
      backdrop-filter: blur(8px);
    }

    .toggle-btn {
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.5);
      padding: 8px 16px;
      border-radius: 20px;
      cursor: pointer;
      font-size: 12px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease;
    }

    .toggle-btn.active {
      background: var(--gold);
      color: var(--black);
    }

    .toggle-btn svg {
      width: 16px;
      height: 16px;
    }

    /* ── DESKTOP PREVIEW MODE ── */
    .demo-modal-overlay.desktop-mode .phone-mockup-wrapper {
      width: 90%;
      height: 85vh;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      background: #fff;
      padding: 0;
      box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6);
      transform: scale(1) translateY(20px);
    }

    .demo-modal-overlay.desktop-mode .phone-button,
    .demo-modal-overlay.desktop-mode .phone-status-bar,
    .demo-modal-overlay.desktop-mode .phone-home-indicator,
    .demo-modal-overlay.desktop-mode .phone-notch {
      display: none;
    }

    .demo-modal-overlay.desktop-mode .phone-screen {
      border-radius: 12px;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .loader-text {
      font-size: 13px;
      color: var(--text-muted);
      font-weight: 500;
      letter-spacing: 0.5px;
    }

    /* Responsive Device scaling for different viewport heights */
    @media (max-height: 950px) {
      .phone-mockup-wrapper {
        transform: scale(0.8) translateY(20px);
      }

      .demo-modal-overlay.active .phone-mockup-wrapper {
        transform: scale(0.85) translateY(0);
      }
    }

    @media (max-height: 850px) {
      .phone-mockup-wrapper {
        transform: scale(0.7) translateY(15px);
      }

      .demo-modal-overlay.active .phone-mockup-wrapper {
        transform: scale(0.75) translateY(0);
      }
    }

    @media (max-height: 750px) {
      .phone-mockup-wrapper {
        transform: scale(0.6) translateY(10px);
      }

      .demo-modal-overlay.active .phone-mockup-wrapper {
        transform: scale(0.65) translateY(0);
      }
    }

    @media (max-height: 650px) {
      .phone-mockup-wrapper {
        transform: scale(0.5) translateY(5px);
      }

      .demo-modal-overlay.active .phone-mockup-wrapper {
        transform: scale(0.55) translateY(0);
      }
    }


    /* SaaS Modal Styling */
    .saas-modal-overlay {
      position: fixed;
      inset: 0;
      z-index: 9999;
      background: rgba(10, 10, 10, 0.45);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      padding: 20px;
    }

    .saas-modal-overlay.active {
      opacity: 1;
      pointer-events: auto;
    }

    .saas-modal-card {
      background: var(--cream);
      border: 1px solid var(--border);
      border-radius: 16px;
      width: 100%;
      max-width: 620px;
      position: relative;
      padding: 36px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
      transform: translateY(20px) scale(0.98);
      transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      max-height: 90vh;
      overflow-y: auto;
    }

    .saas-modal-overlay.active .saas-modal-card {
      transform: translateY(0) scale(1);
    }

    .saas-modal-close {
      position: absolute;
      top: 24px;
      right: 24px;
      background: transparent;
      border: none;
      font-size: 28px;
      color: var(--gray-400);
      cursor: pointer;
      line-height: 1;
      transition: color 0.2s;
    }

    .saas-modal-close:hover {
      color: var(--black);
    }

    .saas-stage {
      display: none;
    }

    .saas-stage.active {
      display: block;
      animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(8px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .saas-modal-header {
      margin-bottom: 24px;
    }

    .saas-badge {
      background: var(--gold-light);
      color: var(--gold-dark);
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      padding: 4px 10px;
      border-radius: 4px;
      display: inline-block;
      margin-bottom: 12px;
    }

    .saas-modal-header h2 {
      font-size: 28px;
      font-weight: 700;
      letter-spacing: -0.5px;
      margin-bottom: 6px;
      color: var(--black);
    }

    .saas-lead {
      font-size: 14px;
      color: var(--text-muted);
    }

    /* Form Elements */
    .saas-form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    @media (max-width: 500px) {
      .saas-form-grid {
        grid-template-columns: 1fr;
        gap: 0;
      }
    }

    .saas-form-group {
      margin-bottom: 18px;
      display: flex;
      flex-direction: column;
    }

    .saas-phone-input-wrapper {
      display: flex;
      gap: 8px;
    }

    .saas-phone-code {
      width: 105px !important;
      flex-shrink: 0;
      padding: 12px 6px !important;
    }

    .saas-form-group label {
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.5px;
      color: var(--gray-800);
      margin-bottom: 6px;
      text-transform: uppercase;
    }

    .saas-form-group input,
    .saas-form-group select {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 12px 14px;
      font-family: inherit;
      font-size: 14px;
      color: var(--black);
      width: 100%;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .saas-form-group input:focus,
    .saas-form-group select:focus {
      border-color: var(--gold);
      outline: none;
      box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.15);
    }

    .saas-error {
      color: #DC2626;
      font-size: 11px;
      margin-top: 4px;
      min-height: 16px;
    }

    /* Onboarding wizard step states */
    .saas-form-step {
      display: none;
    }

    .saas-form-step.active {
      display: block;
      animation: fadeIn 0.4s ease-out;
    }

    /* Wizard bottom navigation */
    .saas-step-nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 32px;
      padding-top: 24px;
      border-top: 1px solid var(--border);
    }

    .saas-dots {
      display: flex;
      gap: 8px;
    }

    .saas-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: var(--border);
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
    }

    .saas-dot.active {
      background: var(--gold-dark);
      transform: scale(1.2);
    }

    .saas-nav-actions {
      display: flex;
      gap: 12px;
    }

    .saas-back-btn {
      background: transparent;
      border: 1px solid var(--border);
      color: var(--text-muted);
      font-family: inherit;
      font-weight: 700;
      font-size: 13px;
      border-radius: 8px;
      padding: 10px 20px;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
    }

    .saas-back-btn:hover {
      background: var(--gray-100);
      color: var(--black);
    }

    .saas-next-btn {
      background: var(--black);
      color: var(--white);
      border: none;
      font-family: inherit;
      font-weight: 700;
      font-size: 13px;
      border-radius: 8px;
      padding: 10px 24px;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .saas-next-btn:hover {
      background: var(--gray-800);
      transform: translateY(-1px);
    }

    /* Verification Layout */
    .saas-verify-container,
    .saas-success-container {
      text-align: center;
      padding: 20px 0;
    }

    .saas-verify-icon {
      width: 64px;
      height: 64px;
      background: var(--gold-light);
      color: var(--gold-dark);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
    }

    .saas-verify-icon svg {
      width: 32px;
      height: 32px;
    }

    .saas-verify-container h2,
    .saas-success-container h2 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 12px;
    }

    .saas-verify-desc {
      color: var(--text-muted);
      font-size: 15px;
      margin-bottom: 32px;
      line-height: 1.6;
    }

    .saas-verify-meta {
      padding-top: 20px;
      border-top: 1px solid var(--border);
      margin-bottom: 24px;
    }

    .saas-verify-meta p {
      font-size: 13px;
      color: var(--text-muted);
    }

    .saas-resend-btn {
      background: transparent;
      border: none;
      color: var(--gold-dark);
      font-weight: 700;
      text-decoration: underline;
      cursor: pointer;
      padding: 4px 8px;
      font-family: inherit;
    }

    .saas-resend-btn:disabled {
      color: var(--gray-400);
      text-decoration: none;
      cursor: not-allowed;
    }

    .saas-countdown-label {
      font-size: 12px;
      color: var(--gray-600);
      margin-top: 6px;
      font-weight: 500;
    }

    /* Simulation Styling */
    .saas-simulation-box {
      background: rgba(201, 168, 76, 0.08);
      border: 1px dashed var(--gold);
      border-radius: 10px;
      padding: 20px;
      margin-top: 30px;
    }

    .sim-badge {
      background: var(--gold);
      color: var(--white);
      font-size: 10px;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 4px;
      text-transform: uppercase;
      letter-spacing: 1px;
      display: inline-block;
      margin-bottom: 8px;
    }

    .saas-simulation-box p {
      font-size: 13px;
      color: var(--gray-800);
      margin-bottom: 12px;
    }

    .saas-simulate-btn {
      background: var(--gold-dark);
      color: var(--white);
      font-family: inherit;
      font-weight: 700;
      font-size: 13px;
      border: none;
      border-radius: 6px;
      padding: 10px 16px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .saas-simulate-btn:hover {
      background: #6B510F;
    }

    /* Success Screen */
    .saas-success-icon-check {
      width: 64px;
      height: 64px;
      background: #DCFCE7;
      color: #15803D;
      border-radius: 50%;
      font-size: 32px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
    }

    .saas-success-finish-btn {
      background: var(--black);
      color: var(--white);
      font-family: inherit;
      font-weight: 500;
      border: none;
      border-radius: 8px;
      padding: 14px 28px;
      cursor: pointer;
      font-size: 15px;
      margin-top: 24px;
      transition: background 0.2s;
    }

    .saas-success-finish-btn:hover {
      background: var(--gray-800);
    }
  </style>

  <script>
    // Resend code countdown state
    let resendTimer = null;
    let countdownSeconds = 0;
    let currentStep = 1;

    document.addEventListener('DOMContentLoaded', () => {
      const triggerButtons = document.querySelectorAll('.pricing-btn-trigger');
      const saasModal = document.getElementById('saasModal');
      const closeSaasBtn = document.getElementById('closeSaasBtn');
      const planSelector = document.getElementById('saas_plan');

      // Auto-trigger registration modal if URL query has get_started=1
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('get_started') && saasModal) {
        if (planSelector) {
          planSelector.value = 'sprout';
          updatePlanBadges('sprout');
        }
        saasModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        showSaasStage('saasStageSignUp');
        goToStep(1);
      }

      triggerButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          const plan = btn.getAttribute('data-plan');
          if (planSelector && plan) {
            planSelector.value = plan;
            updatePlanBadges(plan);
          }

          // Show Modal
          saasModal.classList.add('active');
          document.body.style.overflow = 'hidden';

          // Reset stages and onboarding wizard step
          showSaasStage('saasStageSignUp');
          goToStep(1);
        });
      });

      if (planSelector) {
        planSelector.addEventListener('change', () => {
          updatePlanBadges(planSelector.value);
        });
      }

      const countrySelector = document.getElementById('saas_country');
      const phoneCodeSelector = document.getElementById('saas_phone_code');
      if (countrySelector && phoneCodeSelector) {
        const countryToCode = {
          'India': '+91',
          'United Arab Emirates': '+971',
          'Saudi Arabia': '+966',
          'United Kingdom': '+44',
          'United States': '+1',
          'France': '+33',
          'Singapore': '+65',
          'Australia': '+61'
        };
        countrySelector.addEventListener('change', () => {
          const countryVal = countrySelector.value;
          const matchingCode = countryToCode[countryVal];
          if (matchingCode) {
            phoneCodeSelector.value = matchingCode;
          }
        });
      }

      closeSaasBtn.addEventListener('click', closeSaaSModal);

      saasModal.addEventListener('click', (e) => {
        if (e.target === saasModal) {
          closeSaaSModal();
        }
      });
    });

    function updatePlanBadges(planName) {
      const capitalized = planName.charAt(0).toUpperCase() + planName.slice(1);
      document.querySelectorAll('.class-plan-badge').forEach(badge => {
        badge.textContent = capitalized;
      });
    }

    function validateStep(step) {
      let stepValid = true;

      // Clear error tags for the current step
      if (step === 1) {
        document.getElementById('err_name').textContent = '';
        document.getElementById('err_email').textContent = '';

        const name = document.getElementById('saas_name').value.trim();
        const email = document.getElementById('saas_email').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!name) {
          document.getElementById('err_name').textContent = 'Full name is required';
          stepValid = false;
        }
        if (!email) {
          document.getElementById('err_email').textContent = 'Email address is required';
          stepValid = false;
        } else if (!emailRegex.test(email)) {
          document.getElementById('err_email').textContent = 'Please enter a valid email address';
          stepValid = false;
        }
      } else if (step === 2) {
        document.getElementById('err_business').textContent = '';
        document.getElementById('err_country').textContent = '';
        document.getElementById('err_whatsapp').textContent = '';

        const business = document.getElementById('saas_business').value.trim();
        const country = document.getElementById('saas_country').value;
        const whatsapp = document.getElementById('saas_whatsapp').value.trim();
        const whatsappRegex = /^\+?[0-9\s\-()]{7,18}$/;

        if (!business) {
          document.getElementById('err_business').textContent = 'Business name is required';
          stepValid = false;
        }
        if (!country) {
          document.getElementById('err_country').textContent = 'Please select your country';
          stepValid = false;
        }
        if (!whatsapp) {
          document.getElementById('err_whatsapp').textContent = 'WhatsApp number is required';
          stepValid = false;
        } else if (!whatsappRegex.test(whatsapp)) {
          document.getElementById('err_whatsapp').textContent = 'Please enter a valid phone number';
          stepValid = false;
        }
      } else if (step === 3) {
        document.getElementById('err_password').textContent = '';
        document.getElementById('err_confirm_password').textContent = '';

        const password = document.getElementById('saas_password').value;
        const confirmPassword = document.getElementById('saas_confirm_password').value;

        if (!password) {
          document.getElementById('err_password').textContent = 'Password is required';
          stepValid = false;
        } else if (password.length < 8) {
          document.getElementById('err_password').textContent = 'Password must be at least 8 characters';
          stepValid = false;
        }
        if (password !== confirmPassword) {
          document.getElementById('err_confirm_password').textContent = 'Passwords do not match';
          stepValid = false;
        }
      }
      return stepValid;
    }

    function goToStep(step) {
      // If navigating forward, validate preceding steps
      if (step > currentStep) {
        for (let i = currentStep; i < step; i++) {
          if (!validateStep(i)) return;
        }
      }

      currentStep = step;

      // Toggle form steps visibility
      document.querySelectorAll('.saas-form-step').forEach((el, idx) => {
        if (idx + 1 === step) {
          el.classList.add('active');
        } else {
          el.classList.remove('active');
        }
      });

      // Toggle dots active state
      document.querySelectorAll('#saasStepDots .saas-dot').forEach((el, idx) => {
        if (idx + 1 === step) {
          el.classList.add('active');
        } else {
          el.classList.remove('active');
        }
      });

      // Toggle Back button visibility
      const backBtn = document.getElementById('saasBackBtn');
      if (step === 1) {
        backBtn.style.visibility = 'hidden';
      } else {
        backBtn.style.visibility = 'visible';
      }

      // Update Next button label
      const nextBtn = document.getElementById('saasNextBtn');
      if (step === 3) {
        nextBtn.textContent = 'Create my account →';
      } else {
        nextBtn.textContent = 'Next';
      }
    }

    function prevStep() {
      if (currentStep > 1) {
        goToStep(currentStep - 1);
      }
    }

    function nextStep() {
      if (currentStep < 3) {
        if (validateStep(currentStep)) {
          goToStep(currentStep + 1);
        }
      }
    }

    function handleStepNavNext() {
      if (currentStep === 3) {
        handleSaasRegister(new Event('submit'));
      } else {
        nextStep();
      }
    }

    function closeSaaSModal() {
      const saasModal = document.getElementById('saasModal');
      saasModal.classList.remove('active');
      document.body.style.overflow = '';
      // Clear resend countdown timer if active
      if (resendTimer) {
        clearInterval(resendTimer);
        resendTimer = null;
      }
    }

    function showSaasStage(stageId) {
      document.querySelectorAll('.saas-stage').forEach(stage => {
        stage.classList.remove('active');
      });
      const target = document.getElementById(stageId);
      if (target) target.classList.add('active');
    }

    function handleSaasRegister(e) {
      if (e) e.preventDefault();

      // Verify all steps are fully valid
      if (!validateStep(1) || !validateStep(2) || !validateStep(3)) {
        return;
      }

      const nextBtn = document.getElementById('saasNextBtn');
      const originalText = nextBtn.textContent;
      nextBtn.disabled = true;
      nextBtn.textContent = 'Registering...';

      const name = document.getElementById('saas_name').value.trim();
      const business = document.getElementById('saas_business').value.trim();
      const country = document.getElementById('saas_country').value;
      const email = document.getElementById('saas_email').value.trim();
      const whatsappPrefix = document.getElementById('saas_phone_code').value;
      const whatsappNumber = document.getElementById('saas_whatsapp').value.trim();
      const password = document.getElementById('saas_password').value;
      const plan = document.getElementById('saas_plan').value;
      const theme = document.getElementById('saas_theme').value;

      const fullWhatsapp = whatsappPrefix + ' ' + whatsappNumber;

      fetch('{{ route("saas.register") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          name: name,
          business: business,
          country: country,
          email: email,
          whatsapp: fullWhatsapp,
          password: password,
          plan: plan,
          theme: theme
        })
      })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
          nextBtn.disabled = false;
          nextBtn.textContent = originalText;

          if (status === 200 && body.success) {
            // Success! Show Step 2: Email verification
            document.getElementById('verifyEmailDisplay').textContent = email;

            // Store the dynamic verification URL on the simulator button!
            const simBtn = document.querySelector('.saas-simulate-btn');
            if (simBtn && body.verify_url) {
              simBtn.setAttribute('onclick', `window.location.href="${body.verify_url}"`);
            }

            showSaasStage('saasStageVerify');
            startResendCountdown();
          } else {
            // Handle validation errors from backend
            if (body.errors) {
              Object.keys(body.errors).forEach(key => {
                const errEl = document.getElementById(`err_${key}`);
                if (errEl) {
                  errEl.textContent = body.errors[key][0];
                }
              });

              // Switch to the step with errors
              if (body.errors.name || body.errors.email) {
                goToStep(1);
              } else if (body.errors.business || body.errors.country || body.errors.whatsapp) {
                goToStep(2);
              } else if (body.errors.password || body.errors.theme) {
                goToStep(3);
              }
            } else {
              alert(body.message || 'An unexpected error occurred. Please try again.');
            }
          }
        })
        .catch(error => {
          nextBtn.disabled = false;
          nextBtn.textContent = originalText;
          console.error('Error:', error);
          alert('A connection error occurred. Please check your network and try again.');
        });
    }

    function startResendCountdown() {
      const resendBtn = document.getElementById('resendBtn');
      const resendCountdown = document.getElementById('resendCountdown');

      if (resendTimer) clearInterval(resendTimer);

      countdownSeconds = 60;
      resendBtn.disabled = true;
      resendCountdown.textContent = `You can resend the link in ${countdownSeconds}s`;

      resendTimer = setInterval(() => {
        countdownSeconds--;
        if (countdownSeconds <= 0) {
          clearInterval(resendTimer);
          resendTimer = null;
          resendBtn.disabled = false;
          resendCountdown.textContent = '';
        } else {
          resendCountdown.textContent = `You can resend the link in ${countdownSeconds}s`;
        }
      }, 1000);
    }

    function handleResendCode() {
      alert('A new verification email has been sent successfully!');
      startResendCountdown();
    }

    function simulateVerificationSuccess() {
      showSaasStage('saasStageSuccess');
    }
  </script>

  <!-- TEMPLATE PREVIEW MODAL -->
  <div class="demo-modal-overlay" id="demoModal">
    <button class="demo-modal-close" id="closeDemoBtn" aria-label="Close Preview">&times;</button>

    <div class="device-toggle-wrapper">
      <button class="toggle-btn active" id="mobileToggle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round">
          <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
          <line x1="12" y1="18" x2="12.01" y2="18"></line>
        </svg>
        Mobile
      </button>
      <button class="toggle-btn" id="desktopToggle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round">
          <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
          <line x1="8" y1="21" x2="16" y2="21"></line>
          <line x1="12" y1="17" x2="12" y2="21"></line>
        </svg>
        Desktop
      </button>
    </div>

    <div class="phone-mockup-wrapper">
      <!-- Physical Side Buttons -->
      <div class="phone-button volume-up"></div>
      <div class="phone-button volume-down"></div>
      <div class="phone-button power"></div>

      <div class="phone-screen">
        <!-- iOS Status Bar -->
        <div class="phone-status-bar">
          <span class="status-time" id="statusTime">9:41</span>
          <div class="phone-notch">
            <span class="notch-camera"></span>
          </div>
          <div class="status-icons">
            <!-- Network SVG -->
            <svg class="status-icon" viewBox="0 0 24 24" fill="currentColor">
              <path d="M2 22h20V2z" opacity="0.3" />
              <path d="M2 22h16V6z" />
            </svg>
            <!-- Wifi SVG -->
            <svg class="status-icon" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M12 21a2 2 0 0 1-1.41-.59l-8.5-8.5a1 1 0 0 1 0-1.41A15.91 15.91 0 0 1 12 6a15.91 15.91 0 0 1 9.91 4.5 1 1 0 0 1 0 1.41l-8.5 8.5A2 2 0 0 1 12 21z" />
            </svg>
            <!-- Battery Percentage -->
            <span class="battery-percentage">88%</span>
            <div class="battery-icon">
              <span class="battery-level"></span>
            </div>
          </div>
        </div>

        <!-- Loader Overlay -->
        <div class="phone-loader">
          <div class="spinner"></div>
          <div class="loader-text" id="loaderText">Curating storefront...</div>
        </div>

        <!-- Live IFrame -->
        <iframe src="" id="demoIframe"></iframe>

        <!-- Home Indicator -->
        <div class="phone-home-indicator"></div>
      </div>
    </div>
  </div>

  <script>
    function submitForm() {
      const name = document.getElementById('name').value;
      const business = document.getElementById('business').value;
      const email = document.getElementById('email').value;
      const whatsapp = document.getElementById('whatsapp').value;
      const country = document.getElementById('country').value;

      if (!name || !business || !email || !whatsapp || !country) {
        alert('Please fill in all required fields.');
        return;
      }

      document.getElementById('form-fields').style.display = 'none';
      const success = document.getElementById('form-success');
      success.style.display = 'block';
    }

    // Template Iframe Modal Trigger Logic
    const templateCards = document.querySelectorAll('.template-card');
    const demoModal = document.getElementById('demoModal');
    const demoIframe = document.getElementById('demoIframe');
    const phoneLoader = document.querySelector('.phone-loader');
    const loaderText = document.getElementById('loaderText');
    const closeDemoBtn = document.getElementById('closeDemoBtn');
    const statusBar = document.querySelector('.phone-status-bar');
    const homeIndicator = document.querySelector('.phone-home-indicator');
    const mobileToggle = document.getElementById('mobileToggle');
    const desktopToggle = document.getElementById('desktopToggle');

    // Toggle Logic
    mobileToggle.addEventListener('click', () => {
      mobileToggle.classList.add('active');
      desktopToggle.classList.remove('active');
      demoModal.classList.remove('desktop-mode');
    });

    desktopToggle.addEventListener('click', () => {
      desktopToggle.classList.add('active');
      mobileToggle.classList.remove('active');
      demoModal.classList.add('desktop-mode');
    });

    // Custom status bar themes and personalized loading messages
    const themeStyles = {
      'Aura Luxe': { bg: '#1a101e', text: '#ffffff', indicator: 'rgba(255, 255, 255, 0.45)', msg: 'Curating Aura Luxe experience...' },
      'Velvet Dark': { bg: '#1a1208', text: '#ffffff', indicator: 'rgba(255, 255, 255, 0.45)', msg: 'Polishing Velvet Dark storefront...' },
      'Editorial Cream': { bg: '#fdfaf6', text: '#3d2b0e', indicator: 'rgba(0, 0, 0, 0.45)', msg: 'Styling Editorial Cream showcase...' },
      'Modern Minimal': { bg: '#0f1923', text: '#ffffff', indicator: 'rgba(255, 255, 255, 0.45)', msg: 'Aligning Modern Minimal grid...' }
    };

    // Update digital clock on the mockup status bar
    function updatePhoneClock() {
      const now = new Date();
      let hours = now.getHours();
      let minutes = now.getMinutes();
      hours = hours < 10 ? '0' + hours : hours;
      minutes = minutes < 10 ? '0' + minutes : minutes;
      const timeStr = hours + ':' + minutes;
      const clockEl = document.getElementById('statusTime');
      if (clockEl) clockEl.textContent = timeStr;
    }

    updatePhoneClock();
    setInterval(updatePhoneClock, 60000);

    templateCards.forEach(card => {
      card.addEventListener('click', () => {
        const demoUrl = card.getAttribute('data-demo-url');
        if (demoUrl) {
          // Read template name
          const cardTitleEl = card.querySelector('h3');
          const templateName = cardTitleEl ? cardTitleEl.textContent.trim() : '';
          const activeTheme = themeStyles[templateName] || {
            bg: '#ffffff',
            text: '#000000',
            indicator: 'rgba(0, 0, 0, 0.45)',
            msg: 'Curating premium storefront...'
          };

          // Apply theme-matched styles to mockup status bar & home indicator
          if (statusBar) {
            statusBar.style.backgroundColor = activeTheme.bg;
            statusBar.style.color = activeTheme.text;
          }
          if (homeIndicator) {
            homeIndicator.style.backgroundColor = activeTheme.indicator;
          }
          if (loaderText) {
            loaderText.textContent = activeTheme.msg;
          }

          // Reset loader & update src
          phoneLoader.classList.remove('hidden');
          demoIframe.src = demoUrl;

          // Show modal & disable background scroll
          demoModal.classList.add('active');
          document.body.style.overflow = 'hidden';
        }
      });
    });

    const closeModal = () => {
      demoModal.classList.remove('active');
      // Reset to mobile mode on close
      demoModal.classList.remove('desktop-mode');
      mobileToggle.classList.add('active');
      desktopToggle.classList.remove('active');
      document.body.style.overflow = '';
      setTimeout(() => {
        demoIframe.src = '';
      }, 400);
    };

    closeDemoBtn.addEventListener('click', closeModal);

    demoModal.addEventListener('click', (e) => {
      if (e.target === demoModal) {
        closeModal();
      }
    });

    // Close on Escape key press
    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && demoModal.classList.contains('active')) {
        closeModal();
      }
    });

    demoIframe.onload = () => {
      phoneLoader.classList.add('hidden');
    };

    // Sticky Header Scroll Transition
    const headerNav = document.getElementById('main-nav');
    const heroSection = document.getElementById('hero');
    function handleScroll() {
      const heroHeight = heroSection ? heroSection.offsetHeight : 600;
      const transitionPoint = heroHeight - 64;

      if (window.scrollY >= transitionPoint) {
        headerNav.classList.remove('nav-transparent', 'nav-transparent-blur');
        headerNav.classList.add('nav-scrolled');
      } else {
        headerNav.classList.remove('nav-scrolled');
        headerNav.classList.add('nav-transparent');

        if (window.scrollY > 20) {
          headerNav.classList.add('nav-transparent-blur');
        } else {
          headerNav.classList.remove('nav-transparent-blur');
        }
      }
    }
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Run once initially

    // Product Page Showcase Tab Switcher & Auto-rotation
    const showcaseTabs = document.querySelectorAll('.showcase-tab');
    const showcaseImages = document.querySelectorAll('.showcase-image-wrapper');
    let activeIndex = 0;
    let rotationInterval;

    function switchTab(index) {
      activeIndex = index;
      const tab = showcaseTabs[activeIndex];

      showcaseTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      showcaseImages.forEach(img => img.classList.remove('active'));
      const targetId = tab.getAttribute('data-target');
      const targetImg = document.getElementById(targetId);
      if (targetImg) {
        targetImg.classList.add('active');
      }
    }

    function startAutoRotation() {
      stopAutoRotation();
      rotationInterval = setInterval(() => {
        let nextIndex = (activeIndex + 1) % showcaseTabs.length;
        switchTab(nextIndex);
      }, 5000);
    }

    function stopAutoRotation() {
      if (rotationInterval) {
        clearInterval(rotationInterval);
      }
    }

    showcaseTabs.forEach((tab, index) => {
      tab.addEventListener('click', () => {
        switchTab(index);
        // Reset the timer when a user interacts manually so it doesn't skip immediately
        startAutoRotation();
      });
    });

    // Pause auto-rotation when mouse is hovering the showcase container
    const showcaseContainer = document.querySelector('.showcase-container');
    if (showcaseContainer) {
      showcaseContainer.addEventListener('mouseenter', () => {
        stopAutoRotation();
      });
      showcaseContainer.addEventListener('mouseleave', () => {
        // Only resume if lightbox is not active
        const lightbox = document.getElementById('showcase-lightbox');
        if (lightbox && !lightbox.classList.contains('active')) {
          startAutoRotation();
        }
      });
    }

    // Lightbox modal functionality
    const showcasePreviewPanel = document.querySelector('.showcase-preview-panel');
    const lightbox = document.getElementById('showcase-lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const lightboxClose = document.querySelector('.lightbox-close');

    if (showcasePreviewPanel && lightbox && lightboxImg) {
      showcasePreviewPanel.addEventListener('click', (e) => {
        const clickedMobile = e.target.closest('.showcase-mobile-mockup');
        const activeImgWrapper = showcasePreviewPanel.querySelector('.showcase-image-wrapper.active');
        if (activeImgWrapper) {
          if (clickedMobile) {
            const mobileImg = clickedMobile.querySelector('img');
            lightboxImg.src = mobileImg.src;
            lightboxCaption.textContent = mobileImg.alt + " (Mobile View)";
          } else {
            const desktopImg = activeImgWrapper.querySelector('.showcase-desktop-img');
            lightboxImg.src = desktopImg.src;
            lightboxCaption.textContent = desktopImg.alt + " (Desktop View)";
          }
          lightbox.classList.add('active');
          document.body.style.overflow = 'hidden'; // Stop background scrolling
          stopAutoRotation();
        }
      });

      const closeLightbox = () => {
        lightbox.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
        startAutoRotation();
      };

      lightboxClose.addEventListener('click', closeLightbox);
      lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) {
          closeLightbox();
        }
      });
    }

    // Start auto-rotation initially
    startAutoRotation();
  </script>



</body>

</html>