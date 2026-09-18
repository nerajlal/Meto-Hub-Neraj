@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">Legal</span>
      <h2>Refund & Cancellation Policy</h2>
      <p>Last updated: {{ now()->format('F j, Y') }}</p>
    </div>
    
    <div style="max-width: 800px; margin: 0 auto; padding: 0 5vw;">
      
      <div class="feature-card" style="padding: 50px; border-radius: var(--radius-lg);">
        
        <h3 style="color: var(--dark); margin-top: 0; margin-bottom: 15px; font-size: 1.5rem;">1. Subscription Cancellations</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 30px; font-size: 1.05rem;">
          You may cancel your subscription to Slot Store at any time through your account settings or by contacting our support team. Upon cancellation, your subscription will remain active until the end of your current billing cycle, after which it will not be renewed.
        </p>
        
        <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.5rem;">2. Refunds</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 30px; font-size: 1.05rem;">
          We offer a 14-day money-back guarantee for all new subscriptions. If you are not satisfied with our platform within the first 14 days of your initial purchase, you may request a full refund. After the 14-day period, payments are non-refundable, and we do not provide refunds or credits for any partial-month subscription periods.
        </p>
        
        <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.5rem;">3. Data Retention Upon Cancellation</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 30px; font-size: 1.05rem;">
          When you cancel your subscription, your online store and associated data will remain intact for a grace period of 30 days following the end of your billing cycle. After this period, your data may be permanently deleted from our servers.
        </p>

        <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.5rem;">4. Contact Us</h3>
        <p style="color: var(--muted); line-height: 1.8; margin-bottom: 0; font-size: 1.05rem;">
          If you have any questions or need to request a refund, please contact us at <a href="mailto:support@slotstore.com" style="color: var(--primary); text-decoration: underline;">support@slotstore.com</a>.
        </p>

      </div>

    </div>
  </section>
@endsection
