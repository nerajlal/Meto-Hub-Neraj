<section class="wrap merchant-confidence" aria-labelledby="merchant-confidence-title" style="padding-block: clamp(40px, 6vw, 80px); border-top: var(--rule);">
  <header class="section-head section-head--left">
    <p class="eyebrow">BUILT FOR EVERYDAY SELLING</p>
    <h2 id="merchant-confidence-title">Your orders. Your customers. <em>Your business.</em></h2>
  </header>
  <div class="feature-grid">
    <article class="feature-card">
      <span class="f-icon"><i class="fa-solid fa-bell" aria-hidden="true"></i></span>
      <h3>Know when an order arrives</h3>
      <p>Check new orders from the dashboard bell and receive owner email alerts. Enable device notifications on a supported browser to receive push alerts after granting permission.</p>
    </article>
    <article class="feature-card">
      <span class="f-icon"><i class="fa-solid fa-truck" aria-hidden="true"></i></span>
      <h3>From packing to tracking</h3>
      <p>Save your courier, including DTDC or India Post, add the tracking number, and print invoices and packing labels. Customers can view saved shipment tracking.</p>
      <p>Book pickups and pay postage directly with your courier. Courier-issued labels are managed separately.</p>
    </article>
    <article class="feature-card">
      <span class="f-icon"><i class="fa-solid fa-file-export" aria-hidden="true"></i></span>
      <h3>Keep access to your records</h3>
      <p>Export products and orders from your dashboard. Explore the workflow during your {{ config('saas.trial_days') }}-day free trial and choose monthly or annual billing when you are ready.</p>
      <p><a href="{{ route('landing.contact') }}">Contact our team</a> · <a href="{{ url('/security') }}">Security details</a> · <a href="{{ url('/refund-cancellation') }}">Refund policy</a></p>
    </article>
  </div>
</section>
