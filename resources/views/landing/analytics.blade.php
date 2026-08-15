@extends('layouts.landing')
@section('content')
  <section class="features" id="analytics" style="padding-bottom: 40px;">
    <div class="section-head" style="margin-bottom: 40px;">
      <span class="eyebrow">Data & Analytics</span>
      <h2>Data that drives grocery growth</h2>
      <p>Get real-time, actionable insights into your inventory, sales, and customer behavior.</p>
    </div>

    <!-- Expanded Analytics Features -->
    <div class="feature-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
      
      <!-- Feature 1: Sales Tracking -->
      <div class="feature-card" style="background: var(--bg-card, #1c1c1e); border-radius: 16px; padding: 40px; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <div style="margin-bottom: 24px; color: var(--primary);">
           <i class="fa-solid fa-chart-line fa-2x"></i>
        </div>
        <h3 style="color: #fff; margin-bottom: 16px; font-size: 1.4rem;">Real-time Sales Tracking</h3>
        <p style="color: var(--text-muted, #a1a1aa); line-height: 1.6;">Monitor your revenue, average order value, and daily sales volume as it happens. Identify peak shopping hours to optimize staffing and delivery schedules.</p>
        
        <div style="margin-top: 30px; background: rgba(0,0,0,0.3); border-radius: 8px; padding: 20px;">
          <span style="display: block; font-size: 0.8rem; color: var(--text-muted, #a1a1aa); margin-bottom: 8px;">Today's Revenue</span>
          <span style="display: block; font-size: 1.8rem; color: #fff; font-weight: bold;">$4,250.00 <span style="font-size: 0.9rem; color: #4ade80;">▲ 12%</span></span>
        </div>
      </div>

      <!-- Feature 2: Customer Insights -->
      <div class="feature-card" style="background: var(--bg-card, #1c1c1e); border-radius: 16px; padding: 40px; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <div style="margin-bottom: 24px; color: var(--primary);">
           <i class="fa-solid fa-users fa-2x"></i>
        </div>
        <h3 style="color: #fff; margin-bottom: 16px; font-size: 1.4rem;">Customer Behavior</h3>
        <p style="color: var(--text-muted, #a1a1aa); line-height: 1.6;">Understand who your best customers are. Track retention rates, repeat purchase frequency, and identify segments at risk of churning.</p>
        
        <div style="margin-top: 30px; background: rgba(0,0,0,0.3); border-radius: 8px; padding: 20px;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span style="color: #fff;">Repeat Customers</span>
            <span style="color: #4ade80;">68%</span>
          </div>
          <div style="width: 100%; background: rgba(255,255,255,0.1); border-radius: 4px; height: 8px; overflow: hidden;">
            <div style="width: 68%; background: var(--primary); height: 100%;"></div>
          </div>
        </div>
      </div>

      <!-- Feature 3: Inventory Analytics -->
      <div class="feature-card" style="background: var(--bg-card, #1c1c1e); border-radius: 16px; padding: 40px; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <div style="margin-bottom: 24px; color: var(--primary);">
           <i class="fa-solid fa-box-open fa-2x"></i>
        </div>
        <h3 style="color: #fff; margin-bottom: 16px; font-size: 1.4rem;">Inventory Forecasting</h3>
        <p style="color: var(--text-muted, #a1a1aa); line-height: 1.6;">See which products are moving and which are sitting on shelves. Get automated alerts for low stock items and predict future demand based on historical data.</p>
        
        <div style="margin-top: 30px; background: rgba(0,0,0,0.3); border-radius: 8px; padding: 20px;">
          <span style="display: block; font-size: 0.8rem; color: var(--text-muted, #a1a1aa); margin-bottom: 8px;">Top Selling Category</span>
          <span style="display: block; font-size: 1.2rem; color: #fff; font-weight: bold;">Fresh Produce</span>
          <span style="display: block; font-size: 0.85rem; color: #a1a1aa; margin-top: 4px;">42% of total sales</span>
        </div>
      </div>

    </div>
  </section>

  <!-- Visual Dashboard Preview Section -->
  <section style="padding: 40px 20px; background: #ffffff; border-top: 1px solid rgba(0,0,0,0.05); border-bottom: 1px solid rgba(0,0,0,0.05); overflow: hidden;">
    <div style="max-width: 1000px; margin: 0 auto; text-align: center;">
      <h2 style="font-size: 2rem; color: #111827; margin-bottom: 20px;">Everything in one unified view</h2>
      <p style="color: #4b5563; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">Export reports to CSV, integrate with your accounting software, and give your managers the data they need to succeed.</p>
      
      <!-- Mockup of the dashboard included from partial just as a visual element -->
      <div style="pointer-events: none; opacity: 0.9; transform: scale(0.95); background: #1F2937; padding-top: 20px; border-radius: 16px; border: 1px solid rgba(0,0,0,0.1); overflow: hidden;">
        @include('landing.partials.analytics')
      </div>
    </div>
  </section>

  <!-- Final CTA -->
  <section class="final-cta" style="padding: 100px 20px; text-align: center;">
    <h2 style="font-size: 2.5rem; color: #fff; margin-bottom: 20px;">Ready to grow your store?</h2>
    <p style="color: var(--text-muted, #a1a1aa); margin-bottom: 40px;">Get the insights you need to make better decisions today.</p>
    <div class="hero-cta" style="justify-content: center;">
      <a href="javascript:void(0)" class="btn btn-primary btn-lg pricing-btn-trigger" data-plan="sprout">Start Free Trial</a>
    </div>
  </section>
@endsection
