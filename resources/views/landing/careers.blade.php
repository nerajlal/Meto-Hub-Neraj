@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">Join the Team</span>
      <h2>Careers at MetoHub</h2>
      <p>Help us build the most powerful commerce platform for modern grocers.</p>
    </div>
    
    <div style="max-width: 1000px; margin: 0 auto; padding: 0 5vw;">
      
      <div style="margin-bottom: 60px; text-align: center;">
        <h3 style="color: var(--dark); margin-bottom: 20px; font-size: 2rem;">Why MetoHub?</h3>
        <p style="color: var(--muted); line-height: 1.8; font-size: 1.1rem; max-width: 800px; margin: 0 auto;">
          At MetoHub, we're fundamentally changing how grocery stores operate online. We believe that independent grocers, large chains, and local farmers' markets all deserve access to enterprise-grade technology without the enterprise-level overhead. When you join our team, you're not just writing code, designing interfaces, or selling softwareâ€”you're empowering local economies and transforming the food supply chain.
        </p>
      </div>

      <div style="margin-bottom: 60px;">
        <h3 style="color: var(--dark); margin-bottom: 30px; font-size: 1.8rem; text-align: center;">Core Values</h3>
        <div class="promo-grid">
          <div class="feature-card">
            <div class="f-icon"><i class="fa-solid fa-scale-balanced"></i></div>
            <h4 style="color: var(--dark); margin-bottom: 10px;">Merchant First</h4>
            <p style="color: var(--muted);">Every decision we make starts with the impact on our grocery partners and their customers.</p>
          </div>
          <div class="feature-card">
            <div class="f-icon"><i class="fa-solid fa-lightbulb"></i></div>
            <h4 style="color: var(--dark); margin-bottom: 10px;">Relentless Innovation</h4>
            <p style="color: var(--muted);">The digital commerce landscape is always evolving. We stay ahead of the curve.</p>
          </div>
          <div class="feature-card">
            <div class="f-icon"><i class="fa-solid fa-comments"></i></div>
            <h4 style="color: var(--dark); margin-bottom: 10px;">Radical Transparency</h4>
            <p style="color: var(--muted);">Open communication builds trust internally and with our customers.</p>
          </div>
          <div class="feature-card">
            <div class="f-icon"><i class="fa-solid fa-seedling"></i></div>
            <h4 style="color: var(--dark); margin-bottom: 10px;">Sustainable Growth</h4>
            <p style="color: var(--muted);">We build features and businesses meant to last decades, not just quarters.</p>
          </div>
        </div>
      </div>

      <div style="margin-bottom: 60px;">
        <h3 style="color: var(--dark); margin-bottom: 30px; font-size: 1.8rem; text-align: center;">Perks & Benefits</h3>
        <div class="promo-grid">
          <div class="promo-card">
            <div class="p-icon"><i class="fa-solid fa-heart-pulse"></i></div>
            <h4 style="color: var(--dark); margin-bottom: 10px;">Comprehensive Health</h4>
            <p style="color: var(--muted); font-size: 0.9rem;">Top-tier medical, dental, and vision coverage for you and your dependents.</p>
          </div>
          <div class="promo-card">
            <div class="p-icon"><i class="fa-solid fa-house-laptop"></i></div>
            <h4 style="color: var(--dark); margin-bottom: 10px;">Remote-First</h4>
            <p style="color: var(--muted); font-size: 0.9rem;">Work from anywhere in the world with a generous home office stipend.</p>
          </div>
          <div class="promo-card">
            <div class="p-icon"><i class="fa-solid fa-plane-departure"></i></div>
            <h4 style="color: var(--dark); margin-bottom: 10px;">Unlimited PTO</h4>
            <p style="color: var(--muted); font-size: 0.9rem;">Take the time you need to recharge, with a mandatory minimum of 3 weeks off.</p>
          </div>
          <div class="promo-card">
            <div class="p-icon"><i class="fa-solid fa-graduation-cap"></i></div>
            <h4 style="color: var(--dark); margin-bottom: 10px;">Learning Budget</h4>
            <p style="color: var(--muted); font-size: 0.9rem;">Annual allowance for courses, conferences, and books to accelerate your career.</p>
          </div>
        </div>
      </div>

      <div>
        <h3 style="color: var(--dark); margin-bottom: 30px; font-size: 1.8rem; text-align: center;">Open Positions</h3>
        
        <div class="feature-card" style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px 30px;">
          <div>
            <h4 style="margin: 0 0 5px; color: var(--dark); font-size: 1.1rem;">Senior Laravel Developer</h4>
            <span style="font-size: 0.85rem; color: var(--muted);">Engineering &bull; Remote (Global)</span>
          </div>
          <button class="btn btn-primary" style="padding: 8px 20px; font-size: 0.9rem;">Apply Now</button>
        </div>
        
        <div class="feature-card" style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px 30px;">
          <div>
            <h4 style="margin: 0 0 5px; color: var(--dark); font-size: 1.1rem;">Product Marketing Manager</h4>
            <span style="font-size: 0.85rem; color: var(--muted);">Marketing &bull; San Francisco, CA / Remote</span>
          </div>
          <button class="btn btn-primary" style="padding: 8px 20px; font-size: 0.9rem;">Apply Now</button>
        </div>

        <div class="feature-card" style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px 30px;">
          <div>
            <h4 style="margin: 0 0 5px; color: var(--dark); font-size: 1.1rem;">Customer Success Specialist</h4>
            <span style="font-size: 0.85rem; color: var(--muted);">Support &bull; London, UK / Remote</span>
          </div>
          <button class="btn btn-primary" style="padding: 8px 20px; font-size: 0.9rem;">Apply Now</button>
        </div>
        
        <div style="margin-top: 50px; text-align: center; padding: 30px;">
          <p style="color: var(--muted); margin-bottom: 15px;">Don't see a role that fits? We are always looking for exceptional talent.</p>
          <a href="mailto:careers@slotstore.com" style="color: var(--primary); font-weight: 600; text-decoration: none;">Send your resume to careers@slotstore.com â†’</a>
        </div>
      </div>
      
    </div>
  </section>
@endsection
