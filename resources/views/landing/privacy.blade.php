@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">Legal</span>
      <h2>Privacy Policy</h2>
      <p>Last updated: {{ now()->format('F j, Y') }}</p>
    </div>
    
    <div style="max-width: 800px; margin: 0 auto; padding: 0 5vw;">
      
      <div class="feature-card" style="padding: 50px; border-radius: var(--radius-lg);">
        
        <h3 style="color: var(--dark); margin-top: 0; margin-bottom: 15px; font-size: 1.5rem;">1. Information We Collect</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 30px; font-size: 1.05rem;">
          We collect information you provide directly to us, such as when you create or modify your account, request on-demand services, contact customer support, or otherwise communicate with us.
        </p>
        
        <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.5rem;">2. How We Use Information</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 30px; font-size: 1.05rem;">
          We may use the information we collect about you to provide, maintain, and improve our services, including to facilitate payments, send receipts, provide products and services you request, and develop new features.
        </p>
        
        <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.5rem;">3. Data Security</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 30px; font-size: 1.05rem;">
          We take reasonable measures to help protect information about you from loss, theft, misuse and unauthorized access, disclosure, alteration and destruction.
        </p>

        <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.5rem;">4. Contact Us</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 0; font-size: 1.05rem;">
          If you have any questions about this Privacy Policy, please contact us at <a href="mailto:privacy@slotstore.com" style="color: var(--primary); text-decoration: underline;">privacy@slotstore.com</a>.
        </p>

      </div>

    </div>
  </section>
@endsection
