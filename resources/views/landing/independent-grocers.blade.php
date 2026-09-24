@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">For Independent Grocers</span>
      <h2>Compete and Win in the Digital Age</h2>
      <p>Everything a local grocery store needs to launch, run, and scale an independent online delivery service.</p>
    </div>
    
    <div style="max-width: 1100px; margin: 0 auto; padding: 0 5vw;">
      
      <div style="text-align: center; margin-bottom: 60px;">
        <h3 style="color: var(--dark); margin-bottom: 20px; font-size: 2rem;">Take Back Your Customers</h3>
        <p style="color: var(--muted); line-height: 1.8; max-width: 700px; margin: 0 auto; font-size: 1.1rem;">
          Stop paying 30% commissions to third-party delivery apps. With MetoHub, independent grocers can build their own branded apps and websites, retain customer data, and keep 100% of their margins.
        </p>
      </div>

      <div class="promo-grid" style="margin-bottom: 60px;">
        <div class="feature-card" style="text-align: center;">
          <div class="f-icon" style="margin: 0 auto 20px;"><i class="fa-solid fa-store"></i></div>
          <h4 style="color: var(--dark); margin-bottom: 12px; font-size: 1.2rem;">Branded Storefronts</h4>
          <p style="color: var(--muted); font-size: 0.95rem; line-height: 1.6;">
            Launch a beautiful, mobile-optimized website and native iOS/Android apps featuring your exact brand colors and logo.
          </p>
        </div>

        <div class="feature-card" style="text-align: center;">
          <div class="f-icon" style="margin: 0 auto 20px;"><i class="fa-solid fa-boxes-stacked"></i></div>
          <h4 style="color: var(--dark); margin-bottom: 12px; font-size: 1.2rem;">Easy Inventory</h4>
          <p style="color: var(--muted); font-size: 0.95rem; line-height: 1.6;">
            Import thousands of products instantly via CSV or our POS integrations. Manage prices, stock levels, and barcodes effortlessly.
          </p>
        </div>

        <div class="feature-card" style="text-align: center;">
          <div class="f-icon" style="margin: 0 auto 20px;"><i class="fa-solid fa-truck-fast"></i></div>
          <h4 style="color: var(--dark); margin-bottom: 12px; font-size: 1.2rem;">Local Delivery Control</h4>
          <p style="color: var(--muted); font-size: 0.95rem; line-height: 1.6;">
            Set up custom delivery zones by zip code or radius. Manage your own driver fleet with our dedicated Delivery Boy app.
          </p>
        </div>
      </div>

      <div style="background: var(--white); border: 1px solid var(--line); padding: 50px; border-radius: var(--radius-lg); text-align: center; max-width: 800px; margin: 0 auto;">
        <h3 style="color: var(--dark); margin-bottom: 20px; font-size: 1.8rem;">Why Local Matters</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 20px;">
          Your community relies on your independent grocery store for fresh produce, local specialty items, and excellent service. MetoHub's platform is designed to amplify these strengths online. Unlike massive corporate chains, you can offer highly localized assortments. 
        </p>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 30px;">
          Don't let third-party marketplaces commoditize your brand. Take ownership of your digital presence today.
        </p>
        <a href="{{ route('landing.pricing') }}" class="btn btn-primary" style="padding: 12px 30px; font-size: 1rem;">View Pricing Plans</a>
      </div>

    </div>
  </section>
@endsection
