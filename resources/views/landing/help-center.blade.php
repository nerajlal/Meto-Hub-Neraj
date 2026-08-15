@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">Support & Resources</span>
      <h2>Help Center</h2>
      <p>Find answers to common questions and learn how to configure your store.</p>
    </div>
    
    <div style="max-width: 1100px; margin: 0 auto; padding: 0 5vw;">
      
      <!-- Search Bar Simulation -->
      <div style="margin-bottom: 50px; position: relative; max-width: 700px; margin-left: auto; margin-right: auto;">
        <input type="text" placeholder="Search for articles, guides, and tutorials..." style="width: 100%; padding: 20px 30px; border-radius: 999px; border: 1px solid var(--line); background: var(--white); color: var(--dark); font-size: 1.1rem; box-shadow: var(--shadow-sm);">
        <button class="btn btn-primary" style="position: absolute; right: 8px; top: 8px; border-radius: 999px; padding: 12px 24px;">Search</button>
      </div>

      <div class="promo-grid" style="margin-bottom: 60px;">
        <!-- Category 1 -->
        <div class="feature-card">
          <h3 style="color: var(--dark); margin-bottom: 20px; font-size: 1.3rem; display: flex; align-items: center; gap: 12px;"><i class="fa-solid fa-rocket" style="color: var(--primary);"></i> Getting Started</h3>
          <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 14px;"><a href="#" style="color: var(--muted); text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">How to launch your first store</a></li>
            <li style="margin-bottom: 14px;"><a href="#" style="color: var(--muted); text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Connecting your custom domain</a></li>
            <li style="margin-bottom: 14px;"><a href="#" style="color: var(--muted); text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Store localization and currency setup</a></li>
            <li style="margin-top: 20px;"><a href="#" style="color: var(--primary); font-size: 0.95rem; font-weight: 600;">View all 12 articles &rarr;</a></li>
          </ul>
        </div>

        <!-- Category 2 -->
        <div class="feature-card">
          <h3 style="color: var(--dark); margin-bottom: 20px; font-size: 1.3rem; display: flex; align-items: center; gap: 12px;"><i class="fa-solid fa-box-open" style="color: var(--primary);"></i> Products & Inventory</h3>
          <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 14px;"><a href="#" style="color: var(--muted); text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Importing products via CSV</a></li>
            <li style="margin-bottom: 14px;"><a href="#" style="color: var(--muted); text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Setting up product variants and attributes</a></li>
            <li style="margin-bottom: 14px;"><a href="#" style="color: var(--muted); text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Managing out-of-stock behavior</a></li>
            <li style="margin-top: 20px;"><a href="#" style="color: var(--primary); font-size: 0.95rem; font-weight: 600;">View all 18 articles &rarr;</a></li>
          </ul>
        </div>

        <!-- Category 3 -->
        <div class="feature-card">
          <h3 style="color: var(--dark); margin-bottom: 20px; font-size: 1.3rem; display: flex; align-items: center; gap: 12px;"><i class="fa-solid fa-truck" style="color: var(--primary);"></i> Delivery & Shipping</h3>
          <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 14px;"><a href="#" style="color: var(--muted); text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Configuring delivery zones by zip code</a></li>
            <li style="margin-bottom: 14px;"><a href="#" style="color: var(--muted); text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Assigning orders to delivery drivers</a></li>
            <li style="margin-bottom: 14px;"><a href="#" style="color: var(--muted); text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Setting up minimum order values</a></li>
            <li style="margin-top: 20px;"><a href="#" style="color: var(--primary); font-size: 0.95rem; font-weight: 600;">View all 9 articles &rarr;</a></li>
          </ul>
        </div>
      </div>

      <div style="text-align: center; padding: 40px; background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--line); max-width: 600px; margin: 0 auto;">
        <h4 style="color: var(--dark); margin-bottom: 10px; font-size: 1.3rem;">Still need help?</h4>
        <p style="color: var(--muted); margin-bottom: 25px;">Our support team is available 24/7 to assist you.</p>
        <a href="{{ route('landing.contact') }}" class="btn btn-outline" style="border: 2px solid var(--line); padding: 10px 24px; color: var(--dark); font-weight: 500; border-radius: 8px;">Contact Support</a>
      </div>

    </div>
  </section>
@endsection
