@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">Local & Fresh</span>
      <h2>Farmers Markets Online</h2>
      <p>Connect local farms and artisanal vendors directly to digital consumers.</p>
    </div>
    
    <div style="max-width: 1000px; margin: 0 auto; padding: 0 5vw;">
      
      <div style="display: flex; flex-direction: column; gap: 40px;">
        
        <div class="feature-card" style="padding: 40px;">
          <h3 style="color: var(--dark); font-size: 1.8rem; margin: 0 0 20px 0; display: flex; align-items: center; gap: 15px;">
            <div class="f-icon" style="margin: 0; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-leaf"></i></div>
            Farm-to-Table Digitization
          </h3>
          <p style="color: var(--muted); line-height: 1.8; font-size: 1.05rem; margin: 0;">
            Farmers markets represent the best of local produce, but relying solely on weekend foot traffic limits growth. MetoHub empowers farmers, collectives, and market organizers to launch dedicated digital storefronts. Sell organic vegetables, grass-fed meats, and artisanal goods online for local delivery or pre-order pickup.
          </p>
        </div>

        <div class="feature-card" style="padding: 40px;">
          <h3 style="color: var(--dark); font-size: 1.8rem; margin: 0 0 20px 0; display: flex; align-items: center; gap: 15px;">
            <div class="f-icon" style="margin: 0; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-calendar-check"></i></div>
            Pre-order & Market Pickup
          </h3>
          <p style="color: var(--muted); line-height: 1.8; font-size: 1.05rem; margin: 0;">
            Streamline your market days. Allow customers to browse seasonal inventory online during the week, place their orders, and pay securely. On market day, simply hand them their prepared boxes. This reduces waste, guarantees sales regardless of weather, and minimizes wait times for customers.
          </p>
        </div>

        <div class="feature-card" style="padding: 40px;">
          <h3 style="color: var(--dark); font-size: 1.8rem; margin: 0 0 20px 0; display: flex; align-items: center; gap: 15px;">
            <div class="f-icon" style="margin: 0; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-carrot"></i></div>
            Seasonal & Variable Weight Inventory
          </h3>
          <p style="color: var(--muted); line-height: 1.8; font-size: 1.05rem; margin: 0;">
            Fresh produce isn't always perfectly uniform. Our platform natively supports pricing by weight (e.g., $4.99/lb). You can adjust final totals based on the exact weight picked before finalizing the charge. Additionally, easily toggle products on and off as seasonal availability shifts throughout the year.
          </p>
        </div>

      </div>

    </div>
  </section>
@endsection
