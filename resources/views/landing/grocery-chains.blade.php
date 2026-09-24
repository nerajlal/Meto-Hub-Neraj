@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">Enterprise Solutions</span>
      <h2>Platform for Grocery Chains</h2>
      <p>Scale your multi-location supermarket operations with enterprise-grade headless commerce.</p>
    </div>
    
    <div style="max-width: 1000px; margin: 0 auto; padding: 0 5vw;">
      
      <div style="text-align: center; margin-bottom: 60px;">
        <h3 style="color: var(--dark); margin-bottom: 20px; font-size: 2rem;">Unified Commerce Across All Locations</h3>
        <p style="color: var(--muted); line-height: 1.8; max-width: 750px; margin: 0 auto; font-size: 1.1rem;">
          Managing a multi-location grocery chain introduces immense complexity in inventory syncing, regional pricing, and delivery logistics. MetoHub Enterprise provides a centralized command center to orchestrate your entire digital ecosystem seamlessly.
        </p>
      </div>

      <div class="feature-card" style="margin-bottom: 40px; padding: 40px;">
        <div style="display: flex; gap: 30px; align-items: flex-start;">
          <div class="f-icon" style="margin: 0; background: rgba(46, 125, 50, 0.1); width: 60px; height: 60px; font-size: 1.5rem;"><i class="fa-solid fa-map-location-dot"></i></div>
          <div>
            <h3 style="color: var(--dark); margin-top: 5px; margin-bottom: 15px; font-size: 1.5rem;">Multi-Store Architecture</h3>
            <p style="color: var(--muted); line-height: 1.8; font-size: 1rem; margin: 0;">
              Deploy distinct storefronts per location or maintain a single global site with localized inventory. Our system handles location-specific product catalogs, ensuring that customers only see items in stock at their nearest store. Automatically route orders to the correct branch for picking and packing.
            </p>
          </div>
        </div>
      </div>

      <div class="feature-card" style="margin-bottom: 40px; padding: 40px;">
        <div style="display: flex; gap: 30px; align-items: flex-start;">
          <div class="f-icon" style="margin: 0; background: rgba(46, 125, 50, 0.1); width: 60px; height: 60px; font-size: 1.5rem;"><i class="fa-solid fa-server"></i></div>
          <div>
            <h3 style="color: var(--dark); margin-top: 5px; margin-bottom: 15px; font-size: 1.5rem;">Advanced ERP & POS Integration</h3>
            <p style="color: var(--muted); line-height: 1.8; font-size: 1rem; margin: 0;">
              MetoHub isn't an island. We integrate deeply with your existing enterprise resource planning (ERP) software and point-of-sale (POS) systems. Through real-time webhooks and our REST API, sync complex data like weighted items, tiered pricing, and perishable stock constraints without manual intervention.
            </p>
          </div>
        </div>
      </div>

      <div class="feature-card" style="margin-bottom: 60px; padding: 40px;">
        <div style="display: flex; gap: 30px; align-items: flex-start;">
          <div class="f-icon" style="margin: 0; background: rgba(46, 125, 50, 0.1); width: 60px; height: 60px; font-size: 1.5rem;"><i class="fa-solid fa-chart-pie"></i></div>
          <div>
            <h3 style="color: var(--dark); margin-top: 5px; margin-bottom: 15px; font-size: 1.5rem;">Headquarter Analytics</h3>
            <p style="color: var(--muted); line-height: 1.8; font-size: 1rem; margin: 0;">
              Gain birds-eye visibility into your chain's digital performance. Compare metrics across regions, track picking times by store, analyze high-velocity items, and run A/B tests on promotions. Export detailed CSV reports for financial reconciliation.
            </p>
          </div>
        </div>
      </div>

      <div style="background: linear-gradient(135deg, var(--white), #f8faf7); padding: 50px; border-radius: var(--radius-lg); text-align: center; border: 1px solid var(--line);">
        <h4 style="color: var(--dark); margin-bottom: 15px; font-size: 1.5rem;">Ready to upgrade your enterprise architecture?</h4>
        <p style="color: var(--muted); margin-bottom: 25px; font-size: 1.05rem;">Contact our enterprise sales team for a custom deployment plan and SLA details.</p>
        <a href="{{ route('landing.contact') }}" class="btn btn-primary" style="padding: 12px 30px; font-size: 1.05rem;">Contact Sales</a>
      </div>

    </div>
  </section>
@endsection
