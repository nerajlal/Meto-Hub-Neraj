@extends('layouts.landing')
@section('content')
  <!-- Premium Legal Hero -->
  <section class="legal-hero">
    <div class="legal-hero-blob"></div>
    <div class="legal-hero-inner">
      <span class="eyebrow fade-up">Legal Information</span>
      <h1 class="fade-up" style="animation-delay: 0.1s;">Refund & Cancellation Policy</h1>
      <p class="fade-up" style="animation-delay: 0.2s;">Last updated: {{ \Carbon\Carbon::parse(config('marketing.policies.updated_at'))->format('F j, Y') }}</p>
    </div>
  </section>

  <!-- Premium Content Section -->
  <section class="legal-content-section">
    <div class="legal-container fade-up" style="animation-delay: 0.3s;">
       <div class="legal-card">
        
        <h3>1. Your free trial</h3>
        <p>Your MetoHub store starts with a {{ config('saas.trial_days') }}-day free trial. No credit card is required to create the trial store. Creating a trial does not create a paid subscription or authorize a payment. When the trial ends, choose and pay for a subscription from Billing to continue using paid store-management features.</p>

        <h3>2. Seven-day refund window</h3>
        <p>You may request a full refund within {{ config('marketing.policies.refund_days') }} days of your first paid MetoHub subscription purchase. This refund window starts with the paid purchase, separately from the free trial. Email <a href="mailto:{{ config('marketing.contact.email') }}">{{ config('marketing.contact.email') }}</a> from your account email address and include the payment reference and store name. Do not send passwords or card details.</p>
        <p>After this window, payments are non-refundable and partial billing periods are not refunded, except where applicable law requires otherwise. A refund request is not an automatic cancellation or an instant bank credit. We will confirm the outcome of your request; the payment provider handles any approved return of funds.</p>

        <h3>3. Cancelling a subscription</h3>
        <p>Use Cancel subscription in your dashboard’s Billing page and confirm the request. Any remaining verified paid period remains available. Check the cancellation status in Billing. If the provider cannot confirm cancellation, contact us for assistance. Cancellation stops future renewal when confirmed; it does not itself request a refund.</p>

        <h3>4. Account data</h3>
        <p>Cancelling a subscription is separate from deleting your account. Contact us to request access to your records or deletion of account data. We do not promise automatic deletion a fixed number of days after cancellation.</p>

        <h3>5. Store purchases and contact</h3>
        <p>This policy covers MetoHub platform subscriptions. Purchases made from an independently operated store are governed by that seller’s policies. For subscription refund or cancellation assistance, contact <a href="mailto:{{ config('marketing.contact.email') }}">{{ config('marketing.contact.email') }}</a> or <a href="tel:{{ config('marketing.contact.phone_uri') }}">{{ config('marketing.contact.phone') }}</a>.</p>

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
      background: radial-gradient(circle, rgba(36, 74, 59, 0.08) 0%, transparent 70%);
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
      font-family: 'Manrope', sans-serif;
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
