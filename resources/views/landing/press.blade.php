@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">News & Media</span>
      <h2>Press Room</h2>
      <p>The latest news, announcements, and resources from MetoHub.</p>
    </div>
    
    <div style="max-width: 1100px; margin: 0 auto; padding: 0 5vw; display: flex; flex-wrap: wrap; gap: 60px;">
      
      <!-- Left Column: Press Releases -->
      <div style="flex: 1.5; min-width: 280px;">
        <h3 style="color: var(--dark); margin-bottom: 30px; font-size: 2rem;">Recent Announcements</h3>
        
        <div class="feature-card" style="margin-bottom: 24px; padding: 30px;">
          <span style="display: block; font-size: 0.85rem; color: var(--primary); margin-bottom: 8px; font-weight: 600;">August 15, 2026</span>
          <h4 style="margin: 0 0 15px; font-size: 1.3rem; color: var(--dark);">MetoHub Announces $50M Series B to Accelerate Global Expansion</h4>
          <p style="color: var(--muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
            Following a year of record growth, MetoHub has successfully closed its Series B funding round led by top-tier venture firms to further innovate on its grocery SaaS platform.
          </p>
          <a href="#" style="color: var(--primary); font-weight: 500; font-size: 0.95rem;">Read Full Release &rarr;</a>
        </div>

        <div class="feature-card" style="margin-bottom: 24px; padding: 30px;">
          <span style="display: block; font-size: 0.85rem; color: var(--primary); margin-bottom: 8px; font-weight: 600;">June 02, 2026</span>
          <h4 style="margin: 0 0 15px; font-size: 1.3rem; color: var(--dark);">New AI-Powered Inventory Analytics Tool Launched</h4>
          <p style="color: var(--muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
            MetoHub introduces a groundbreaking AI module designed to help independent grocers predict demand, minimize food waste, and optimize their supply chains automatically.
          </p>
          <a href="#" style="color: var(--primary); font-weight: 500; font-size: 0.95rem;">Read Full Release &rarr;</a>
        </div>

        <div class="feature-card" style="margin-bottom: 24px; padding: 30px;">
          <span style="display: block; font-size: 0.85rem; color: var(--primary); margin-bottom: 8px; font-weight: 600;">March 14, 2026</span>
          <h4 style="margin: 0 0 15px; font-size: 1.3rem; color: var(--dark);">MetoHub Surpasses 10,000 Active Grocery Merchants</h4>
          <p style="color: var(--muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
            A major milestone for the platform as thousands of local and regional grocers have now transitioned their physical stores into high-performing omnichannel digital storefronts.
          </p>
          <a href="#" style="color: var(--primary); font-weight: 500; font-size: 0.95rem;">Read Full Release &rarr;</a>
        </div>
      </div>

      <!-- Right Column: Media Contacts -->
      <div style="flex: 1; min-width: 280px;">
        <div class="feature-card" style="padding: 30px;">
          <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--dark); font-size: 1.2rem;">Media Contacts</h4>
          <p style="font-size: 0.95rem; color: var(--muted); margin-bottom: 20px; line-height: 1.6;">
            For press inquiries, interview requests, or speaking engagements, please contact our PR team.
          </p>
          <div style="font-size: 0.95rem;">
            <strong style="display: block; color: var(--dark); margin-bottom: 4px;">Sarah Jenkins</strong>
            <span style="display: block; color: var(--muted); margin-bottom: 10px;">Director of Communications</span>
            <a href="mailto:press@slotstore.com" style="color: var(--primary); font-weight: 500;">press@slotstore.com</a>
          </div>
        </div>
      </div>

    </div>
  </section>
@endsection
