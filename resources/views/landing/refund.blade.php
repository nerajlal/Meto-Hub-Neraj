@extends('layouts.landing')
@section('content')
  <!-- Premium Legal Hero -->
  <section class="legal-hero">
    <div class="legal-hero-blob"></div>
    <div class="legal-hero-inner">
      <span class="eyebrow fade-up">Legal Information</span>
      <h1 class="fade-up" style="animation-delay: 0.1s;">Refund & Cancellation Policy</h1>
      <p class="fade-up" style="animation-delay: 0.2s;">Last updated: {{ now()->format('F j, Y') }}</p>
    </div>
  </section>

  <!-- Premium Content Section -->
  <section class="legal-content-section">
    <div class="legal-container fade-up" style="animation-delay: 0.3s;">
       <div class="legal-card">
        
        <h3>1. Subscription Cancellations</h3>
        <p>
          You may cancel your subscription to MetoHub at any time through your account settings or by contacting our support team. Upon cancellation, your subscription will remain active until the end of your current billing cycle, after which it will not be renewed.
        </p>
        
        <h3>2. Refunds</h3>
        <p>
          We offer a 14-day money-back guarantee for all new subscriptions. If you are not satisfied with our platform within the first 14 days of your initial purchase, you may request a full refund. After the 14-day period, payments are non-refundable, and we do not provide refunds or credits for any partial-month subscription periods.
        </p>
        
        <h3>3. Data Retention Upon Cancellation</h3>
        <p>
          When you cancel your subscription, your online store and associated data will remain intact for a grace period of 30 days following the end of your billing cycle. After this period, your data may be permanently deleted from our servers. We encourage you to export necessary records before terminating your account.
        </p>

        <h3>4. Contact Us</h3>
        <p>
          If you have any questions or need to request a refund, please contact us at <a href="mailto:support@metohub.com">support@metohub.com</a>.
        </p>

       </div>
    </div>
  </section>

  <style>
    .legal-hero {
      position: relative;
      padding: 200px 8vw 120px;
      background: var(--bg);
      text-align: center;
      overflow: hidden;
    }
    .legal-hero-blob {
      position: absolute;
      top: -100px;
      left: 50%;
      transform: translateX(-50%);
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(46, 125, 50, 0.08) 0%, transparent 70%);
      z-index: 0;
      pointer-events: none;
    }
    .legal-hero-inner {
      position: relative;
      z-index: 1;
      max-width: 800px;
      margin: 0 auto;
    }
    .legal-hero h1 {
      font-size: clamp(2.2rem, 4vw, 3.2rem);
      color: var(--dark);
      margin-bottom: 20px;
      font-weight: 800;
      letter-spacing: -0.02em;
    }
    .legal-hero p {
      font-size: 1.2rem;
      color: var(--muted);
    }
    .legal-content-section {
      padding: 0 8vw 120px;
      background: var(--bg);
    }
    .legal-container {
      max-width: 900px;
      margin: -60px auto 0;
      position: relative;
      z-index: 10;
    }
    .legal-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: var(--radius-xl);
      padding: 70px 80px;
      box-shadow: 0 20px 40px rgba(31, 41, 55, 0.08);
      border: 1px solid rgba(255, 255, 255, 1);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
    }
    .legal-card h2, .legal-card h3, .legal-card h4 {
      color: var(--dark);
      margin-top: 2.5rem;
      margin-bottom: 1rem;
      font-weight: 700;
      font-family: 'Poppins', sans-serif;
    }
    .legal-card h3 {
      font-size: 1.6rem;
      position: relative;
      display: inline-block;
    }
    .legal-card h3::after {
      content: '';
      position: absolute;
      bottom: -4px;
      left: 0;
      width: 30px;
      height: 3px;
      background: var(--primary);
      border-radius: 2px;
    }
    .legal-card h2:first-child, .legal-card h3:first-child {
      margin-top: 0;
    }
    .legal-card p {
      color: var(--muted);
      font-size: 1.1rem;
      line-height: 1.8;
      margin-bottom: 1.5rem;
    }
    .legal-card a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s ease;
      border-bottom: 1px dashed var(--primary);
    }
    .legal-card a:hover {
      color: var(--primary-dark);
      border-bottom-style: solid;
    }
    
    @media (max-width: 768px) {
      .legal-card {
        padding: 40px 30px;
      }
      .legal-hero {
        padding: 160px 6vw 100px;
      }
      .legal-hero h1 {
        font-size: 1.8rem;
      }
      .legal-card p {
        font-size: 1rem;
      }
    }
  </style>
@endsection
