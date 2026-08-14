@extends('layouts.landing')
@section('content')
  <section class="hero" id="home">
    <div class="hero-blob blob-a" aria-hidden="true"></div>
    <div class="hero-blob blob-b" aria-hidden="true"></div>
    <div class="hero-inner">
      <div class="hero-copy fade-up">
        <span class="eyebrow">Grocery commerce infrastructure</span>
        <h1>Create &amp; grow your grocery store <span class="highlight">online</span></h1>
        <p class="hero-sub">Launch a powerful grocery eCommerce store with advanced pricing, promotions, analytics,
          custom themes, payment integrations, and a seamless shopping experience â€” all run from one dashboard.</p>
        <div class="hero-cta">
          <a href="javascript:void(0)" class="btn btn-primary btn-lg pricing-btn-trigger" data-plan="sprout">Start
            Free</a>
          <a href="https://wa.me/7012639646?text=Hello!%20I%20would%20like%20to%20schedule%20a%20demo%20to%20learn%20more%20about%20Slot%20Store." target="_blank" class="btn btn-outline btn-lg">Book a Demo</a>
        </div>
        <div class="hero-proof">
          <div class="avatars" aria-hidden="true">
            <span></span><span></span><span></span><span></span>
          </div>
          <p><strong>2,400+</strong> grocery merchants run on Slot Store</p>
        </div>
      </div>

      <div class="hero-visual fade-up" style="animation-delay:.15s">
        <div class="dash-card dash-main">
          <div class="dash-topbar">
            <div class="dash-dots"><span></span><span></span><span></span></div>
            <span class="dash-title">FreshMart Dashboard</span>
          </div>
          <div class="dash-grid">
            <div class="dash-tile">
              <span class="tile-label">Revenue</span>
              <span class="tile-value" data-count="48200" data-prefix="$">$0</span>
              <span class="tile-trend up">â–² 12.4%</span>
            </div>
            <div class="dash-tile">
              <span class="tile-label">Orders</span>
              <span class="tile-value" data-count="1284">0</span>
              <span class="tile-trend up">â–² 8.1%</span>
            </div>
            <div class="dash-tile">
              <span class="tile-label">Customers</span>
              <span class="tile-value" data-count="912">0</span>
              <span class="tile-trend up">â–² 5.6%</span>
            </div>
            <div class="dash-tile">
              <span class="tile-label">Inventory</span>
              <span class="tile-value" data-count="6304">0</span>
              <span class="tile-trend">SKUs</span>
            </div>
          </div>
          <div class="dash-chart" aria-hidden="true">
            <svg viewBox="0 0 320 90" preserveAspectRatio="none">
              <polyline points="0,70 40,60 80,65 120,40 160,48 200,25 240,32 280,12 320,20" fill="none"
                stroke="var(--secondary)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
              <polygon points="0,70 40,60 80,65 120,40 160,48 200,25 240,32 280,12 320,20 320,90 0,90"
                fill="url(#gradFill)" opacity="0.25" />
              <defs>
                <linearGradient id="gradFill" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="var(--secondary)" />
                  <stop offset="100%" stop-color="var(--secondary)" stop-opacity="0" />
                </linearGradient>
              </defs>
            </svg>
          </div>
        </div>

        <div class="float-card float-coupon" aria-hidden="true">
          <span class="float-icon"><i class="fa-solid fa-tag"></i></span>
          <div><strong>SAVE20</strong><small>Coupon applied</small></div>
        </div>
        <div class="float-card float-order" aria-hidden="true">
          <span class="float-icon"><i class="fa-solid fa-box"></i></span>
          <div><strong>Order #10432</strong><small>Ready to ship</small></div>
        </div>
        <div class="float-card float-rule" aria-hidden="true">
          <span class="float-icon"><i class="fa-solid fa-gear"></i></span>
          <div><strong>Wholesale rule</strong><small>Auto-priced Ã—10</small></div>
        </div>
      </div>
    </div>
    <div class="section-divider" aria-hidden="true"></div>
  </section>

  <!-- ================= TRUSTED BY ================= -->
  <section class="trusted">
    <p class="trusted-label">Trusted by grocery businesses building online</p>
    <div class="marquee">
      <div class="marquee-track">
        <span>FreshMart</span><span>Green Basket</span><span>Daily Foods</span><span>Urban Grocery</span><span>Family
          Market</span><span>Farm Fresh</span>
        <span>FreshMart</span><span>Green Basket</span><span>Daily Foods</span><span>Urban Grocery</span><span>Family
          Market</span><span>Farm Fresh</span>
      </div>
    </div>
  </section>

  <!-- ================= FEATURES ================= -->
  @include('landing.partials.features')

  <!-- ================= SOLUTIONS ================= -->
  @include('landing.partials.solutions')

  <!-- ================= THEMES ================= -->
  @include('landing.partials.themes')

  <!-- ================= ANALYTICS ================= -->
  @include('landing.partials.analytics')

  <!-- ================= PRICING ================= -->
  @include('landing.partials.pricing')

  <!-- ================= FINAL CTA ================= -->
  <section class="final-cta">
    <h2>Start your grocery business today</h2>
    <p>Everything you need to launch, manage, and grow a successful grocery store online.</p>
    <div class="hero-cta">
      <a href="javascript:void(0)" class="btn btn-primary btn-lg pricing-btn-trigger" data-plan="sprout">Start Free</a>
      <a href="https://wa.me/7012639646?text=Hello!%20I%20would%20like%20to%20schedule%20a%20demo%20to%20learn%20more%20about%20Slot%20Store." target="_blank" class="btn btn-outline-light btn-lg">Book Demo</a>
    </div>
  </section>

@endsection
