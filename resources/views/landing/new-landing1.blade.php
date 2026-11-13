<!doctype html>
<html lang="en" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('landing.partials.seo')

<!-- Fonts: Fraunces, DM Sans, DM Mono + Manrope & Instrument Serif -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500;600&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Fraunces:ital,opsz,wght,SOFT,WONK@0,9..144,300..900,0..100,0..1;1,9..144,300..900,0..100,0..1&family=Instrument+Serif:ital@0;1&family=Manrope:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

<!-- MetoHub Shared Reference (Tailwind utilities for header/footer and shared components) -->
<link rel="stylesheet" href="{{ asset('css/metohub-reference.css') }}?v={{ filemtime(public_path('css/metohub-reference.css')) }}">

<!-- Metora Luxury Design System & MetoHub Custom Theme -->
<link rel="stylesheet" href="{{ asset('css/metora-style.css') }}?v={{ filemtime(public_path('css/metora-style.css')) }}">
<link rel="stylesheet" href="{{ asset('css/metohub-demo-custom.css') }}?v={{ filemtime(public_path('css/metohub-demo-custom.css')) }}">

<!-- Original registration wizard CSS -->
<link rel="stylesheet" href="{{ asset('css/metohub-home.css') }}?v={{ filemtime(public_path('css/metohub-home.css')) }}">

<style>
  /* Seamless container & layout bridge */
  body {
    background-color: var(--paper, #FDFBF7);
    color: var(--ink, #0F172A);
    font-family: var(--sans);
    margin: 0;
    overflow-x: clip;
  }
  .grain {
    pointer-events: none;
    position: fixed;
    inset: 0;
    z-index: 999;
    opacity: 0.035;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
  }
  .highlight-gold {
    color: var(--red, #D97706);
    font-style: italic;
  }
  /* Preserve Wizard styling compatibility */
  dialog.wizard {
    background: #ffffff;
    color: #0f172a;
    border-radius: 1.25rem;
    box-shadow: 0 25px 60px -15px rgba(0,0,0,0.3);
    border: 1px solid #e2e8f0;
  }
  .hero__actions {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    align-items: center;
    margin-top: 28px;
    margin-bottom: 24px;
  }
  .hero__stats {
    display: flex;
    gap: 28px;
    padding-top: 20px;
    border-top: 1px solid rgba(15, 23, 42, 0.08);
  }
  .hero__stat strong {
    display: block;
    font-family: var(--display);
    font-size: 24px;
    color: var(--ink);
    line-height: 1.1;
  }
  .hero__stat span {
    font: 500 12px/1.3 var(--mono);
    color: var(--ink-3);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .stories-scroll-wrapper {
    position: relative;
    overflow: hidden;
    width: 100%;
    padding: 1rem 0 1.5rem;
    -webkit-mask-image: linear-gradient(to right, transparent 0%, black 2%, black 98%, transparent 100%);
    mask-image: linear-gradient(to right, transparent 0%, black 2%, black 98%, transparent 100%);
  }
  .stories-scroll-track {
    display: flex;
    gap: 1.5rem;
    width: max-content;
    animation: metohubStoriesTicker 45s linear infinite;
  }
  .stories-scroll-wrapper:hover .stories-scroll-track {
    animation-play-state: paused;
  }
  @keyframes metohubStoriesTicker {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
  }
  .demo-scroll-wrapper {
    position: relative;
    overflow-x: auto;
    width: 100%;
    padding: 1rem 0 1.5rem;
    scrollbar-width: thin;
  }
  .demo-scroll-track {
    display: flex;
    gap: 1.5rem;
    width: max-content;
  }
  .pricing-footnote {
    font-size: 13px;
    color: var(--ink-3);
    text-align: center;
    margin-top: 32px;
    font-family: var(--mono);
  }
</style>
</head>
<body>
<div class="grain" aria-hidden="true"></div>

@include('landing.partials.header')

<main id="main-content" tabindex="-1">

  <!-- MetoHub Hero -->
  <section class="metohub-hero wrap" id="hero" aria-labelledby="hero-title">
    <div class="metohub-split">
      <div>
        <div class="hero__badge">
          <span class="hero__badge-dot"></span>
          <b>METOHUB</b> · Storefront Builder for Instagram Sellers · Metora Labs
        </div>
        <h1 class="hero__headline" id="hero-title">
          Out of the DMs. Into a real <span class="highlight-gold">storefront.</span>
        </h1>
        <p class="hero__sub">
          Stop juggling screenshots, bank transfer slips, and endless "Price please?" chats. Give your Instagram brand one
          gorgeous, lightning-fast store link where customers browse collections, choose variants, and checkout with ease.
        </p>

        <div class="hero__actions">
          <a class="btn btn--primary" href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}" data-start="" data-plan="base">
            <span>Start {{ config('saas.trial_days') }}-Day Free Trial</span>
            <span aria-hidden="true">→</span>
          </a>
          <a class="btn btn--outline" href="#demos">
            <span>Explore stores</span>
          </a>
        </div>

        <p style="font-size: 13px; color: var(--ink-3); font-family: var(--mono); margin-bottom: 20px;">
          <span>✓ {{ config('saas.trial_days') }}-day free trial</span> · 
          <span>✓ No coding needed</span> · 
          <span>✓ Cash on delivery checkout</span> ·
          <span>✓ Flexible monthly subscriptions</span>
        </p>

        <div class="hero__stats">
          <div class="hero__stat">
            <strong>₹{{ number_format(config('saas.plans.base.monthly_price')) }}<small style="font-size: 14px;">/mo</small></strong>
            <span>Affordable Entry</span>
          </div>
          <div class="hero__stat">
            <strong>{{ count(app(\App\Services\ThemeAvailability::class)->available()) }}</strong>
            <span>Curated Themes</span>
          </div>
          <div class="hero__stat">
            <strong>0% Fee</strong>
            <span>Zero Commission</span>
          </div>
        </div>
      </div>

      <div>
        <div class="phone-mockup">
          <div class="phone-screen">
            <div class="phone-notch">
              <span>9:41</span>
              <span>METOHUB OS</span>
              <span><i class="fa-solid fa-wifi"></i> 100%</span>
            </div>
            <div class="phone-store-bar">
              <span>metohub.cloud/yourbrand</span>
              <span class="tag">VERIFIED</span>
            </div>
            <div class="phone-body">
              <div class="phone-product-card">
                <img src="{{ asset('images/landing/reference/hero.jpg') }}" alt="Lumière Eau de Parfum Store Preview">
                <div class="phone-product-info">
                  <span class="phone-badge">FEATURED COLLECTION</span>
                  <h4 style="font-size: 16px; margin: 0; color: var(--ink); font-weight: 700;">Lumière Eau de Parfum</h4>
                  <div class="phone-price-row">
                    <span style="font-weight: 800; font-size: 18px; color: var(--red);">₹2,450</span>
                    <span style="font-size: 12px; font-weight: 600; color: #15803d;">In Stock · Fast COD</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="live-order-badge">
            <i class="fa-solid fa-circle-check" style="color: #16a34a; font-size: 20px;"></i>
            <div>
              <strong>New Order #1042 Received</strong>
              <span style="display: block; font-size: 12px; color: var(--ink-3);">Via Instagram Bio Link</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Platform Summary & AEO Context -->
  <section class="wrap" style="padding-block: 28px; border-bottom: 1px solid rgba(15, 23, 42, 0.08);" aria-labelledby="platform-summary-heading">
    <div style="background: var(--paper-2); border: 1px solid rgba(217, 119, 6, 0.2); border-radius: 16px; padding: 24px 30px;">
      <h2 id="platform-summary-heading" style="font-size: 20px; font-family: var(--display); color: var(--ink); margin: 0 0 8px;">What is MetoHub?</h2>
      <p style="font-size: 15px; color: var(--ink-2); line-height: 1.65; margin: 0;">{{ app(\App\Services\MarketingContent::class)->summary() }}</p>
    </div>
  </section>

  <!-- Live Demo Stores Showcase -->
  @if($demos->isNotEmpty())
  <section class="wrap" id="demos" style="padding-block: clamp(70px, 9vw, 130px);">
    <header class="section-head section-head--left">
      <p class="eyebrow eyebrow--pill"><b>LIVE DEMOS</b></p>
      <h2>Find your brand's <em>aesthetic.</em></h2>
      <p class="lede">Every store is optimized for mobile Instagram shoppers with instant load times and one-tap checkout.</p>
    </header>

    <div class="demo-scroll-wrapper">
      <div class="demo-scroll-track">
        @if($demos->has('mens-clothing'))
        <a class="demo-store-card" href="{{ $demos['mens-clothing']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" data-demo="mens-clothing">
          <img src="{{ asset('images/landing/reference/demo-mens.jpg') }}" alt="Thread & Form Men's Clothing" loading="lazy">
          <div class="demo-store-content">
            <div>
              <p class="demo-store-tag">Men’s Clothing</p>
              <h3 class="demo-store-title">{{ $demos['mens-clothing']['tenant']->name }}</h3>
              <p class="demo-store-theme">{{ config('storefront_themes.'.$demos['mens-clothing']['tenant']->theme.'.name', 'Atelier Edit') }} theme</p>
              <p style="font-size: 14px; color: var(--ink-2); line-height: 1.6;">{{ $demos['mens-clothing']['workflow'] ?? 'Apparel drops with sizes, variants and stock tracking.' }}</p>
            </div>
            <span style="font-weight: 700; color: var(--red); font-size: 14px; margin-top: 16px; display: inline-flex; align-items: center; gap: 6px;">
              Explore Store <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </span>
          </div>
        </a>
        @endif

        @if($demos->has('grocery'))
        <a class="demo-store-card" href="{{ $demos['grocery']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" data-demo="grocery">
          <img src="{{ asset('images/landing/reference/demo-grocery.jpg') }}" alt="Fresh Basket Grocery" loading="lazy">
          <div class="demo-store-content">
            <div>
              <p class="demo-store-tag">Grocery</p>
              <h3 class="demo-store-title">{{ $demos['grocery']['tenant']->name }}</h3>
              <p class="demo-store-theme">{{ config('storefront_themes.'.$demos['grocery']['tenant']->theme.'.name', 'Fresh Market') }} theme</p>
              <p style="font-size: 14px; color: var(--ink-2); line-height: 1.6;">{{ $demos['grocery']['workflow'] ?? 'Group everyday essentials into easy-to-browse collections.' }}</p>
            </div>
            <span style="font-weight: 700; color: var(--red); font-size: 14px; margin-top: 16px; display: inline-flex; align-items: center; gap: 6px;">
              Explore Store <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </span>
          </div>
        </a>
        @endif

        @if($demos->has('gadgets'))
        <a class="demo-store-card" href="{{ $demos['gadgets']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" data-demo="gadgets">
          <img src="{{ asset('images/landing/reference/demo-gadgets.jpg') }}" alt="Circuit Cart Gadgets" loading="lazy">
          <div class="demo-store-content">
            <div>
              <p class="demo-store-tag">Gadgets</p>
              <h3 class="demo-store-title">{{ $demos['gadgets']['tenant']->name }}</h3>
              <p class="demo-store-theme">{{ config('storefront_themes.'.$demos['gadgets']['tenant']->theme.'.name', 'Vista Shop') }} theme</p>
              <p style="font-size: 14px; color: var(--ink-2); line-height: 1.6;">{{ $demos['gadgets']['workflow'] ?? 'Keep model options, product details and stock together.' }}</p>
            </div>
            <span style="font-weight: 700; color: var(--red); font-size: 14px; margin-top: 16px; display: inline-flex; align-items: center; gap: 6px;">
              Explore Store <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </span>
          </div>
        </a>
        @endif

        @if($demos->has('jewelry'))
        <a class="demo-store-card" href="{{ $demos['jewelry']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" data-demo="jewelry">
          <img src="{{ asset('images/landing/reference/demo-jewelry.jpg') }}" alt="Aurelia Jewels" loading="lazy">
          <div class="demo-store-content">
            <div>
              <p class="demo-store-tag">Jewelry</p>
              <h3 class="demo-store-title">{{ $demos['jewelry']['tenant']->name }}</h3>
              <p class="demo-store-theme">{{ config('storefront_themes.'.$demos['jewelry']['tenant']->theme.'.name', 'Maison Luxe') }} theme</p>
              <p style="font-size: 14px; color: var(--ink-2); line-height: 1.6;">{{ $demos['jewelry']['workflow'] ?? 'Give each piece its own photos, details and collection.' }}</p>
            </div>
            <span style="font-weight: 700; color: var(--red); font-size: 14px; margin-top: 16px; display: inline-flex; align-items: center; gap: 6px;">
              Explore Store <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </span>
          </div>
        </a>
        @endif

        @if($demos->has('watches'))
        <a class="demo-store-card" href="{{ $demos['watches']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" data-demo="watches">
          <img src="{{ asset('images/landing/reference/demo-watches.jpg') }}" alt="Hour House Watches" loading="lazy">
          <div class="demo-store-content">
            <div>
              <p class="demo-store-tag">Watches</p>
              <h3 class="demo-store-title">{{ $demos['watches']['tenant']->name }}</h3>
              <p class="demo-store-theme">{{ config('storefront_themes.'.$demos['watches']['tenant']->theme.'.name', 'Organic Pantry') }} theme</p>
              <p style="font-size: 14px; color: var(--ink-2); line-height: 1.6;">{{ $demos['watches']['workflow'] ?? 'Showcase each watch with photos and available variants.' }}</p>
            </div>
            <span style="font-weight: 700; color: var(--red); font-size: 14px; margin-top: 16px; display: inline-flex; align-items: center; gap: 6px;">
              Explore Store <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </span>
          </div>
        </a>
        @endif

        @if($demos->has('womens-clothing'))
        <a class="demo-store-card" href="{{ $demos['womens-clothing']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" data-demo="womens-clothing">
          <img src="{{ asset('images/landing/reference/demo-womens.jpg') }}" alt="Loom & Grace Women's Clothing" loading="lazy">
          <div class="demo-store-content">
            <div>
              <p class="demo-store-tag">Women’s Clothing</p>
              <h3 class="demo-store-title">{{ $demos['womens-clothing']['tenant']->name }}</h3>
              <p class="demo-store-theme">{{ config('storefront_themes.'.$demos['womens-clothing']['tenant']->theme.'.name', 'Bloom Avenue') }} theme</p>
              <p style="font-size: 14px; color: var(--ink-2); line-height: 1.6;">{{ $demos['womens-clothing']['workflow'] ?? 'Organize new collections with sizes and stock for each look.' }}</p>
            </div>
            <span style="font-weight: 700; color: var(--red); font-size: 14px; margin-top: 16px; display: inline-flex; align-items: center; gap: 6px;">
              Explore Store <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </span>
          </div>
        </a>
        @endif

        @if($demos->has('perfume'))
        <a class="demo-store-card" href="{{ $demos['perfume']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" data-demo="perfume">
          <img src="{{ asset('images/landing/reference/demo-perfume.jpg') }}" alt="Maison Mist Perfume" loading="lazy">
          <div class="demo-store-content">
            <div>
              <p class="demo-store-tag">Perfume</p>
              <h3 class="demo-store-title">{{ $demos['perfume']['tenant']->name }}</h3>
              <p class="demo-store-theme">{{ config('storefront_themes.'.$demos['perfume']['tenant']->theme.'.name', 'Noir Collection') }} theme</p>
              <p style="font-size: 14px; color: var(--ink-2); line-height: 1.6;">{{ $demos['perfume']['workflow'] ?? 'Present signature scents with size options and gift bundles.' }}</p>
            </div>
            <span style="font-weight: 700; color: var(--red); font-size: 14px; margin-top: 16px; display: inline-flex; align-items: center; gap: 6px;">
              Explore Store <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </span>
          </div>
        </a>
        @endif
    </div>
    <p style="font-size: 13px; color: var(--ink-3); margin-top: 16px; font-family: var(--mono);">These are sample stores with demo products. Previews are illustrative. Each opens in a new tab.</p>
  </section>
  @else
  <section class="wrap" id="demos" style="padding-block: clamp(70px, 9vw, 130px);">
    <div style="background: var(--paper-2); border: 1px solid rgba(217, 119, 6, 0.25); border-radius: 20px; padding: clamp(28px, 4vw, 44px); text-align: center;">
      <h3 style="font-size: 24px; font-family: var(--display); color: var(--ink); margin: 0 0 10px;">Your brand deserves its own space.</h3>
      <p style="font-size: 16px; color: var(--ink-2); margin: 0 0 20px;">Explore our themes and choose your store’s look.</p>
      <a class="btn btn--primary" href="{{ route('landing.themes') }}">
        <span>Explore themes</span>
        <span aria-hidden="true">→</span>
      </a>
    </div>
  </section>
  @endif

  <!-- How It Works Section -->
  <section class="process wrap" id="how-it-works" style="border-top: var(--rule);">
    <header class="section-head section-head--left">
      <p class="eyebrow eyebrow--pill"><b>HOW IT WORKS</b></p>
      <h2>Four simple steps to <em>launch.</em></h2>
      <p class="lede">From signing up to accepting your first order in under 10 minutes.</p>
    </header>
    <div class="cards">
      <div class="card">
        <span class="card__no">1</span>
        <img class="card__img" src="{{ asset('img/ello/ello-sketch-sm.webp') }}" alt="Store creation">
        <h3>Create Your Store</h3>
        <p>Register your brand name and create your account. Start with a ready-to-use theme and personalize it from your dashboard.</p>
      </div>
      <div class="card">
        <span class="card__no">2</span>
        <img class="card__img" src="{{ asset('img/ello/ello-laptop-sm.webp') }}" alt="Product upload">
        <h3>Upload Products</h3>
        <p>Add your photos, prices, sizes, and stock. Organize items into clean, aesthetic collections.</p>
      </div>
      <div class="card">
        <span class="card__no">3</span>
        <img class="card__img" src="{{ asset('img/ello/ello-point-sm.webp') }}" alt="Paste in bio">
        <h3>Paste in Bio</h3>
        <p>Add your custom link to your Instagram bio or WhatsApp catalog. Orders arrive neatly in your dashboard.</p>
      </div>
      <div class="card">
        <span class="card__no">4</span>
        <span class="card__no" style="display:none"></span>
        <img class="card__img" src="{{ asset('img/ello/ello-jump-sm.webp') }}" alt="Fulfill orders">
        <h3>Fulfill &amp; Grow</h3>
        <p>Print invoices and packing labels, save your courier tracking number, and keep customers informed.</p>
      </div>
    </div>

    <!-- High-Conversion Action Strip in How It Works -->
    <div style="margin-top: 48px; background: var(--paper-2); border: 1.5px solid rgba(217, 119, 6, 0.25); border-radius: 20px; padding: clamp(24px, 3.5vw, 36px); display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 24px; box-shadow: 0 16px 36px -10px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(255, 255, 255, 0.8) inset;">
      <div style="max-width: 580px;">
        <div style="display: inline-flex; align-items: center; gap: 8px; font: 700 12px/1 var(--mono); color: var(--red); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px;">
          <i class="fa-solid fa-bolt"></i>
          <span>Takes Under 10 Minutes · No Credit Card Required</span>
        </div>
        <h3 style="font-size: clamp(20px, 2.2vw, 28px); margin: 0 0 6px; color: var(--ink); font-family: var(--display);">Ready to open your storefront today?</h3>
        <p style="font-size: 15px; color: var(--ink-2); margin: 0; line-height: 1.5;">Get your instant link-in-bio store with {{ config('saas.trial_days') }} days free. No setup fees or coding needed.</p>
      </div>

      <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
        <a class="btn btn--primary" href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}" data-start="" data-plan="base" style="padding: 15px 32px; font-size: 15px;">
          <span>Get Started Free</span>
          <span aria-hidden="true">→</span>
        </a>
        <a class="btn btn--outline" href="https://wa.me/918547470675?text=Hi%20MetoHub%21%20I%20want%20to%20set%20up%20my%20store%20today." target="_blank" rel="noopener" style="padding: 15px 22px; font-size: 14px;">
          <i class="fa-brands fa-whatsapp" style="margin-right: 8px; color: #16a34a; font-size: 16px;"></i>
          <span>Ask a Question</span>
        </a>
      </div>
    </div>
  </section>

  <!-- Full Storefront Operating System Features -->
  <section class="wrap" style="padding-bottom: clamp(70px, 9vw, 130px);">
    <div class="included-box">
      <div class="included-box__head">
        <h3>Full Storefront Operating System</h3>
        <span class="included-badge">✦ ALL PLANS INCLUDED</span>
      </div>
      <div class="included-grid">
        <div class="included-item">
          <i class="fa-solid fa-palette"></i>
          <div>
            <strong>{{ count(app(\App\Services\ThemeAvailability::class)->available()) }} Premium Creator Themes</strong>
            <span>Match your brand palette, fonts, and aesthetic style.</span>
          </div>
        </div>
        <div class="included-item">
          <i class="fa-solid fa-tags"></i>
          <div>
            <strong>Variant &amp; Size Tracking</strong>
            <span>Never oversell out-of-stock sizes or colors again.</span>
          </div>
        </div>
        <div class="included-item">
          <i class="fa-solid fa-receipt"></i>
          <div>
            <strong>Automatic Invoices &amp; Slips</strong>
            <span>Generate one-click customer invoices and packing labels.</span>
          </div>
        </div>
        <div class="included-item">
          <i class="fa-brands fa-whatsapp"></i>
          <div>
            <strong>Email, Bell &amp; Device Alerts</strong>
            <span>Receive order alerts in your dashboard and by email. Enable device notifications on a supported browser.</span>
          </div>
        </div>
        <div class="included-item">
          <i class="fa-solid fa-percent"></i>
          <div>
            <strong>Coupons &amp; Bundle Deals</strong>
            <span>Run flash sales, promo codes, and buy-one-get-one offers.</span>
          </div>
        </div>
        <div class="included-item">
          <i class="fa-solid fa-globe"></i>
          <div>
            <strong>Custom Domain Support</strong>
            <span>Connect your own `.in` or `.com` domain on Pro &amp; Enterprise.</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section class="wrap" id="pricing" style="padding-bottom: clamp(90px, 11vw, 150px); border-top: var(--rule);">
    <header class="section-head section-head--left" style="margin-top: clamp(60px, 7vw, 90px);">
      <p class="eyebrow eyebrow--pill"><b>TRANSPARENT PLANS</b></p>
      <h2>Simple pricing. <em>Zero commission.</em></h2>
      <p class="lede">No hidden percentages taken from your hard-earned sales. Try free for {{ config('saas.trial_days') }} days.</p>
    </header>

    <div class="pricing-cards-grid" style="margin-top: 40px;">
      <!-- Base Plan -->
      <div class="pricing-card-luxury">
        <div>
          <span class="demo-store-tag">STARTER</span>
          <h3 style="font-size: 26px; margin: 0; color: var(--ink);">{{ config('saas.plans.base.name', 'Base') }}</h3>
          <p style="font-size: 15px; color: var(--ink-2); margin-top: 6px;">Perfect for new Instagram sellers launching their first storefront.</p>
          <div class="price-value">₹{{ number_format(config('saas.plans.base.monthly_price')) }}<small>/month</small></div>
          <p style="font-size: 13px; color: var(--ink-3); font-family: var(--mono); margin-bottom: 24px;">{{ config('saas.trial_days') }}-Day Free Trial · Billed Monthly</p>

          <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 12px; font-size: 15px; color: var(--ink-2);">
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> Custom MetoHub Store Link</li>
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> {{ count(app(\App\Services\ThemeAvailability::class)->available()) }} Themes, Logo &amp; Brand Colors</li>
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> Product Variants &amp; Stock Tracking</li>
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> Invoices &amp; Packing Labels</li>
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> Discount Codes &amp; Bundles</li>
          </ul>
        </div>

        <a class="btn btn--outline" href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}" data-start="" data-plan="base" style="margin-top: 32px; justify-content: center;">
          <span>Start Free Trial</span>
        </a>
      </div>

      <!-- Pro Plan (Featured) -->
      <div class="pricing-card-luxury featured">
        <span class="pricing-top-tag">RECOMMENDED FOR GROWING BRANDS</span>
        <div>
          <span class="demo-store-tag">GROWING BRANDS</span>
          <h3 style="font-size: 28px; margin: 0; color: var(--ink);">{{ config('saas.plans.pro.name', 'Pro') }}</h3>
          <p style="font-size: 15px; color: var(--ink-2); margin-top: 6px;">Your own domain name. Complete brand authority on every link.</p>
          <div class="price-value" style="color: var(--red);">₹{{ number_format(config('saas.plans.pro.monthly_price')) }}<small>/month</small></div>
          <p style="font-size: 13px; color: var(--ink-3); font-family: var(--mono); margin-bottom: 24px;">{{ config('saas.trial_days') }}-Day Free Trial · Billed Monthly</p>

          <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 12px; font-size: 15px; color: var(--ink-2);">
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> <strong>Everything in Base</strong></li>
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> <strong>Connect Your Custom Domain (yourbrand.in)</strong></li>
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> Remove MetoHub Branding</li>
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> Advanced Sales Analytics</li>
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> Priority WhatsApp Studio Support</li>
          </ul>
        </div>

        <a class="btn btn--primary" href="{{ route('landing', ['get_started' => 1, 'plan' => 'pro']) }}" data-start="" data-plan="pro" style="margin-top: 32px; justify-content: center;">
          <span>Start Free Trial</span>
          <span aria-hidden="true">→</span>
        </a>
      </div>

      <!-- Enterprise Plan -->
      <div class="pricing-card-luxury">
        <div>
          <span class="demo-store-tag">SCALE</span>
          <h3 style="font-size: 26px; margin: 0; color: var(--ink);">{{ config('saas.plans.enterprise.name', 'Enterprise') }}</h3>
          <p style="font-size: 15px; color: var(--ink-2); margin-top: 6px;">For established multi-brand catalog sellers needing dedicated support.</p>
          <div class="price-value">₹{{ number_format(config('saas.plans.enterprise.monthly_price')) }}<small>/month</small></div>
          <p style="font-size: 13px; color: var(--ink-3); font-family: var(--mono); margin-bottom: 24px;">{{ config('saas.trial_days') }}-Day Free Trial · Billed Monthly</p>

          <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 12px; font-size: 15px; color: var(--ink-2);">
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> <strong>Everything in Pro</strong></li>
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> Custom Theme Modifications</li>
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> High Volume Traffic SLA</li>
            <li><i class="fa-solid fa-check" style="color: var(--red); margin-right: 8px;"></i> Dedicated Account Architect</li>
          </ul>
        </div>

        <a class="btn btn--outline" href="{{ route('landing', ['get_started' => 1, 'plan' => 'enterprise']) }}" data-start="" data-plan="enterprise" style="margin-top: 32px; justify-content: center;">
          <span>Start Free Trial</span>
        </a>
      </div>
    </div>
    <p class="pricing-footnote">Prices shown are for monthly subscriptions. Choose your paid plan in your dashboard after the trial.</p>
  </section>

  @include('landing.partials.merchant-confidence')

  <!-- Creator Stories & Testimonials Slider -->
  @if($reviews->isNotEmpty())
  <section class="wrap" id="client-reviews" style="padding-block: clamp(70px, 9vw, 120px); border-top: var(--rule);">
    <header class="section-head section-head--left">
      <p class="eyebrow eyebrow--pill"><b>SELLER STORIES</b></p>
      @if($reviews->every(fn ($review) => ($review['sample'] ?? false) === true))
      <h2>Small brands. <em>Everyday possibilities.</em></h2>
      <p class="lede">Illustrative seller stories showing how different brands could use MetoHub.</p>
      <p style="font-size: 13px; color: var(--ink-3); margin-top: 6px; font-family: var(--mono);">Names and quotes are fictional examples, not MetoHub client reviews. Illustrative example.</p>
      @else
      <h2>Loved by <em>creators</em> across India.</h2>
      <p class="lede">From the people behind the brands running on MetoHub.</p>
      @endif
    </header>

    <div class="stories-scroll-wrapper">
      <div class="stories-scroll-track">
        @foreach([1, 2] as $iteration)
          @foreach($reviews as $review)
          <article class="story-card-luxury" @if($iteration === 2) aria-hidden="true" @endif>
            <div>
              @if($review['sample'] ?? false)
              <span class="story-sample-label">Illustrative example</span>
              @else
              <div class="story-stars" aria-label="5 out of 5 stars">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
              </div>
              @endif
              <p class="story-quote">“{{ $review['quote'] }}”</p>
            </div>
            <div class="story-author">
              <div class="story-avatar">{{ mb_substr(trim($review['name']), 0, 2) }}</div>
              <div class="story-meta">
                <h4>{{ $review['name'] }}</h4>
                <p>{{ $review['store'] ?? 'Verified Creator' }}</p>
              </div>
            </div>
          </article>
          @endforeach
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <!-- FAQ Section (Accessible Dropdowns with schema sync) -->
  <section class="wrap" id="faq" style="padding-block: clamp(80px, 10vw, 140px); border-top: var(--rule);">
    <div class="faq-split-layout">
      <!-- Left Column -->
      <div class="faq-left-col">
        <p class="eyebrow eyebrow--pill" style="margin-bottom: 14px;"><b>QUESTIONS</b></p>
        <h2 style="font-size: clamp(40px, 4.5vw, 64px); font-family: var(--display); line-height: 1.1; margin: 0 0 20px; color: var(--ink);">Good to <em>know.</em></h2>
        <p style="font-size: 16px; color: var(--ink-2); line-height: 1.65; max-width: 360px; margin: 0;">
          The practical details, before your big beginning. Find answers about setting up, choosing a plan, and taking your first orders.
        </p>

        <div class="faq-still-card">
          <div class="faq-chat-icon">
            <i class="fa-regular fa-comment-dots"></i>
          </div>
          <h4 style="font-size: 17px; font-weight: 700; color: var(--ink); margin: 0 0 6px;">Still have a question?</h4>
          <p style="font-size: 14px; color: var(--ink-2); line-height: 1.5; margin: 0 0 16px;">Tell us about your store and what you'd like to build.</p>
          <a href="{{ route('landing.contact') }}" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: 14px; color: var(--red); text-decoration: none;">
            <span>Let's talk</span>
            <span aria-hidden="true">↗</span>
          </a>
        </div>
      </div>

      <!-- Right Column (Clean Accordion matching exact schema questions) -->
      <div class="faq-right-list">
        @foreach(app(\App\Services\MarketingContent::class)->faqs() as $faqIndex => $faq)
        <details class="faq-line-item" @if($faqIndex === 0) open @endif>
          <summary class="faq-line-summary">
            <h3 style="font: inherit; margin: 0">{{ $faq['question'] }}</h3>
            <i class="fa-solid fa-chevron-down faq-chevron"></i>
          </summary>
          <div class="faq-line-body">
            {{ $faq['answer'] }}
          </div>
        </details>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Bottom Conversion Banner -->
  <section class="wrap" style="padding-bottom: clamp(80px, 10vw, 130px);">
    <div style="background: linear-gradient(135deg, var(--ink) 0%, #1a1714 100%); border: 1px solid rgba(217, 119, 6, 0.35); border-radius: 28px; padding: clamp(40px, 6vw, 72px) clamp(24px, 5vw, 64px); text-align: center; color: var(--paper); box-shadow: 0 24px 60px -15px rgba(0,0,0,0.4), 0 0 30px rgba(217, 119, 6, 0.15); position: relative; overflow: hidden;">
      <div style="position: absolute; -webkit-mask-image: radial-gradient(circle, black, transparent 75%); inset: 0; background: radial-gradient(circle at 50% 0%, rgba(217, 119, 6, 0.25), transparent 70%); pointer-events: none;"></div>
      <p class="eyebrow eyebrow--pill" style="background: rgba(255,255,255,0.06); border-color: rgba(217, 119, 6, 0.4); color: var(--wood-1); margin-bottom: 20px;">
        <b>ZERO RISK · {{ config('saas.trial_days') }}-DAY FREE TRIAL</b>
      </p>
      <h2 style="font-size: clamp(32px, 4.5vw, 54px); font-family: var(--display); margin: 0 0 16px; color: #FFFDF8;">
        Launch your store in <em style="color: var(--sea); font-style: italic;">10 minutes.</em>
      </h2>
      <p style="font-size: clamp(16px, 1.8vw, 19px); color: rgba(255, 255, 255, 0.75); max-width: 600px; margin: 0 auto 36px; line-height: 1.6;">
        Join hundreds of independent sellers growing their sales on MetoHub. Plans start at just ₹{{ number_format(config('saas.plans.base.monthly_price')) }}/month.
      </p>
      <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
        <a class="btn btn--primary" href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}" data-start="" data-plan="base" style="padding: 16px 36px; font-size: 16px;">
          <span>Create Your Store Now</span>
          <span aria-hidden="true">→</span>
        </a>
        <a class="btn btn--outline" href="https://wa.me/918547470675?text=Hi%20MetoHub%20Team%2C%20I%20have%20questions%20about%20starting%20my%20store." target="_blank" rel="noopener" style="border-color: rgba(255,255,255,0.25); color: #FFFDF8; padding: 16px 28px;">
          <i class="fa-brands fa-whatsapp" style="margin-right: 8px; color: var(--sea);"></i>
          <span>Chat on WhatsApp</span>
        </a>
      </div>
    </div>
  </section>

</main>

@include('landing.partials.footer')

<!-- Registration Wizard Modal -->
<dialog class="wizard" id="wizard" aria-labelledby="wt">
  <div class="w-head"><h2 id="wt">Create your store</h2><button class="w-close" type="button" id="wClose" aria-label="Close registration">✕</button></div>
  <div class="prog"><div class="bar" aria-hidden="true"><i id="wBar"></i></div><small id="wLabel" aria-live="polite">Step 1 of 3: About you</small></div>
  <div class="w-body">
    <form id="wForm" method="POST" action="{{ route('saas.register') }}" novalidate>
      @csrf
      <div class="chosen"><span>Plan: <strong id="planName">{{ config('saas.plans.base.name') }}, ₹{{ config('saas.plans.base.monthly_price') }}/month</strong><small class="trial-note">{{ config('saas.trial_days') }}-day trial. Choose your paid subscription in Billing after verification.</small></span>
        <select id="planSel" name="plan" aria-label="Change plan">@foreach(config('saas.plans') as $id=>$plan)<option value="{{ $id }}">{{ $plan['name'] }}</option>@endforeach</select></div>
      <p class="form-status" id="formStatus" role="alert" hidden></p>
      <div class="pane active" data-step="1">
        <div class="signup-field"><label for="f_name">Your name</label><input id="f_name" name="name" autocomplete="name" required maxlength="255" aria-describedby="err_name"><div class="err" id="err_name"></div></div>
        <div class="signup-field"><label for="f_email">Email</label><input id="f_email" name="email" type="email" autocomplete="email" required maxlength="255" aria-describedby="err_email"><div class="err" id="err_email"></div></div>
        <div class="signup-field"><label for="f_phone">Phone or WhatsApp number</label><input id="f_phone" name="whatsapp" type="tel" inputmode="tel" autocomplete="tel" required maxlength="30" placeholder="+91 98765 43210" aria-describedby="err_whatsapp"><div class="err" id="err_whatsapp"></div></div>
      </div>
      <div class="pane" data-step="2">
        <div class="signup-field"><label for="f_biz">Business name</label><input id="f_biz" name="business" autocomplete="organization" required maxlength="255" aria-describedby="err_business"><div class="err" id="err_business"></div></div>
        <div class="signup-field"><label for="f_country">Country</label><select id="f_country" name="country" required autocomplete="country-name" aria-describedby="err_country"><option value="">Choose your country</option>@foreach(['India','United Arab Emirates','Saudi Arabia','United Kingdom','United States','France','Singapore','Australia','Other'] as $country)<option value="{{ $country }}">{{ $country }}</option>@endforeach</select><div class="err" id="err_country"></div></div>
        <p class="fine">You can personalize your storefront and add products after verifying your email.</p>
      </div>
      <div class="pane" data-step="3">
        <div class="two">
          <div class="signup-field"><label for="f_pw">Password</label><input id="f_pw" name="password" type="password" autocomplete="new-password" required minlength="8" aria-describedby="err_password"><div class="err" id="err_password"></div></div>
          <div class="signup-field"><label for="f_pw2">Confirm password</label><input id="f_pw2" name="password_confirmation" type="password" autocomplete="new-password" required aria-describedby="err_password_confirmation"><div class="err" id="err_password_confirmation"></div></div>
        </div>
        <input type="hidden" name="theme" value="template_1">
        <p class="fine" id="signupThemeNote">Your store starts with {{ config('storefront_themes.'.app(\App\Services\ThemeAvailability::class)->defaultTheme().'.name', 'an available theme') }}. After verification, preview and change your theme in Dashboard → Settings → Theme.</p>
        <p id="err_theme" class="theme-errors" role="alert"></p>
        <p class="fine">After your account is created, verify your email to open your dashboard. <a href="{{ route('landing.terms') }}" target="_blank" rel="noopener">Terms</a> · <a href="{{ route('landing.privacy') }}" target="_blank" rel="noopener">Privacy</a></p>
      </div>
      <div class="w-foot"><button class="btn btn-ghost" type="button" id="wBack" hidden>Back</button><button class="btn btn-primary" type="submit" id="wNext" style="margin-left:auto">Continue</button></div>
    </form>
    <div class="done" id="wDone" tabindex="-1" hidden><div class="tick" aria-hidden="true">✓</div><h3 id="doneTitle">Your account is created</h3><p id="doneMessage" role="status"></p><p class="done-email" id="doneEmail"></p><p class="fine">After verification, sign in to choose your products, customize your theme, and share your store.</p><div class="done-actions"><button class="btn btn-ghost" id="resendVerification" type="button">Resend verification link</button><a class="btn btn-primary" href="{{ route('admin.common.login') }}">Go to merchant login</a></div><p class="fine" id="resendStatus" role="status"></p></div>
  </div>
</dialog>

<script id="landing-config" type="application/json">@json($landingConfig)</script>
<script src="{{ asset('js/metohub-home.js') }}?v={{ filemtime(public_path('js/metohub-home.js')) }}" defer></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // FAQ accordion auto-close siblings
    const faqs = document.querySelectorAll('.faq-line-item');
    faqs.forEach(faq => {
      faq.addEventListener('toggle', () => {
        if (faq.open) {
          faqs.forEach(otherFaq => {
            if (otherFaq !== faq && otherFaq.open) {
              otherFaq.open = false;
            }
          });
        }
      });
    });
  });
</script>
</body>
</html>
