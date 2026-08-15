@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">Learn & Grow</span>
      <h2>Guides & Playbooks</h2>
      <p>Actionable advice to scale your grocery delivery business.</p>
    </div>
    
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 5vw;">
      
      <div class="promo-grid" style="gap: 40px;">
        
        <!-- Guide 1 -->
        <div class="feature-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
          <div style="height: 200px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center;">
             <i class="fa-solid fa-motorcycle" style="font-size: 4rem; color: var(--white); opacity: 0.9;"></i>
          </div>
          <div style="padding: 30px; flex: 1; display: flex; flex-direction: column;">
            <span style="font-size: 0.85rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; font-weight: 600;">Operations</span>
            <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.4rem;">The Ultimate Guide to In-House Delivery</h3>
            <p style="color: var(--muted); font-size: 1rem; line-height: 1.6; margin-bottom: 25px; flex: 1;">
              Learn how to transition away from expensive 3rd-party delivery apps. We cover routing, hiring drivers, insurance, and maintaining food quality during transit.
            </p>
            <a href="#" style="color: var(--primary); font-weight: 600; text-decoration: none; align-self: flex-start;">Read Guide &rarr;</a>
          </div>
        </div>

        <!-- Guide 2 -->
        <div class="feature-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
          <div style="height: 200px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); display: flex; align-items: center; justify-content: center;">
             <i class="fa-solid fa-boxes-stacked" style="font-size: 4rem; color: var(--white); opacity: 0.9;"></i>
          </div>
          <div style="padding: 30px; flex: 1; display: flex; flex-direction: column;">
            <span style="font-size: 0.85rem; color: #3b82f6; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; font-weight: 600;">Inventory</span>
            <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.4rem;">Mastering Produce & Perishables Online</h3>
            <p style="color: var(--muted); font-size: 1rem; line-height: 1.6; margin-bottom: 25px; flex: 1;">
              Selling variable-weight items online can be tricky. This playbook teaches you how to configure your catalog, manage customer expectations, and handle substitutions gracefully.
            </p>
            <a href="#" style="color: #3b82f6; font-weight: 600; text-decoration: none; align-self: flex-start;">Read Guide &rarr;</a>
          </div>
        </div>

        <!-- Guide 3 -->
        <div class="feature-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
          <div style="height: 200px; background: linear-gradient(135deg, #831843, #ec4899); display: flex; align-items: center; justify-content: center;">
             <i class="fa-solid fa-bullseye" style="font-size: 4rem; color: var(--white); opacity: 0.9;"></i>
          </div>
          <div style="padding: 30px; flex: 1; display: flex; flex-direction: column;">
            <span style="font-size: 0.85rem; color: #ec4899; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; font-weight: 600;">Marketing</span>
            <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.4rem;">Customer Retention Strategies for Local Grocers</h3>
            <p style="color: var(--muted); font-size: 1rem; line-height: 1.6; margin-bottom: 25px; flex: 1;">
              Acquiring new customers is expensive. Learn how to implement loyalty points, subscription boxes, and targeted email marketing to keep your local community coming back.
            </p>
            <a href="#" style="color: #ec4899; font-weight: 600; text-decoration: none; align-self: flex-start;">Read Guide &rarr;</a>
          </div>
        </div>

      </div>

    </div>
  </section>
@endsection
