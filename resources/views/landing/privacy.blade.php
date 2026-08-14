@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px;">
    <div class="section-head">
      <span class="eyebrow">Legal</span>
      <h2>Privacy Policy</h2>
      <p>Last updated: {{ now()->format('F j, Y') }}</p>
    </div>
    <div style="max-width: 800px; margin: 0 auto; line-height: 1.8; color: var(--muted); background: var(--white); padding: 40px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--line);">
      <h3 style="margin-bottom: 16px; font-size: 1.4rem;">1. Information We Collect</h3>
      <p style="margin-bottom: 24px;">We collect information you provide directly to us, such as when you create or modify your account, request on-demand services, contact customer support, or otherwise communicate with us.</p>
      
      <h3 style="margin-bottom: 16px; font-size: 1.4rem;">2. How We Use Information</h3>
      <p style="margin-bottom: 24px;">We may use the information we collect about you to provide, maintain, and improve our services, including to facilitate payments, send receipts, provide products and services you request, and develop new features.</p>
      
      <h3 style="margin-bottom: 16px; font-size: 1.4rem;">3. Data Security</h3>
      <p style="margin-bottom: 24px;">We take reasonable measures to help protect information about you from loss, theft, misuse and unauthorized access, disclosure, alteration and destruction.</p>
    </div>
  </section>
@endsection
