@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">Legal</span>
      <h2>Terms of Service</h2>
      <p>Last updated: {{ now()->format('F j, Y') }}</p>
    </div>
    
    <div style="max-width: 800px; margin: 0 auto; padding: 0 5vw;">
      
      <div class="feature-card" style="padding: 50px; border-radius: var(--radius-lg);">
        
        <h3 style="color: var(--dark); margin-top: 0; margin-bottom: 15px; font-size: 1.5rem;">1. Acceptance of Terms</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 30px; font-size: 1.05rem;">
          By accessing or using the Slot Store platform, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.
        </p>
        
        <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.5rem;">2. Use of the Services</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 30px; font-size: 1.05rem;">
          You agree to use our services only for lawful purposes and in accordance with these Terms. You are responsible for all activities that occur under your account.
        </p>
        
        <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.5rem;">3. Subscriptions & Payments</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 30px; font-size: 1.05rem;">
          Some aspects of the Service are billed on a subscription basis. You will be billed in advance on a recurring, periodic basis (e.g., monthly or annually).
        </p>
        
        <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.5rem;">4. Limitation of Liability</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 30px; font-size: 1.05rem;">
          In no event shall Slot Store be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.
        </p>

        <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.5rem;">5. Changes to Terms</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 0; font-size: 1.05rem;">
          We reserve the right to modify these Terms at any time. We will provide notice of significant changes by updating the date at the top of this page.
        </p>

      </div>

    </div>
  </section>
@endsection
