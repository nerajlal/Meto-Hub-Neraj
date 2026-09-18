@extends('layouts.landing')
@section('content')
  <!-- Premium Legal Hero -->
  <section class="legal-hero">
    <div class="legal-hero-blob"></div>
    <div class="legal-hero-inner">
      <span class="eyebrow fade-up">Legal Information</span>
      <h1 class="fade-up" style="animation-delay: 0.1s;">Terms of Service</h1>
      <p class="fade-up" style="animation-delay: 0.2s;">Last updated: {{ now()->format('F j, Y') }}</p>
    </div>
  </section>

  <!-- Premium Content Section -->
  <section class="legal-content-section">
    <div class="legal-container fade-up" style="animation-delay: 0.3s;">
       <div class="legal-card">
        
        <h3>1. Acceptance of Terms</h3>
        <p>
          By accessing or using the Slot Store platform, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.
        </p>
        
        <h3>2. Use of the Services</h3>
        <p>
          You agree to use our services only for lawful purposes and in accordance with these Terms. You are responsible for all activities that occur under your account. Ensure that you maintain the confidentiality of your login credentials.
        </p>
        
        <h3>3. Subscriptions & Payments</h3>
        <p>
          Some aspects of the Service are billed on a subscription basis. You will be billed in advance on a recurring, periodic basis (e.g., monthly or annually). Upgrades or downgrades in plan level will result in a prorated charge or credit.
        </p>
        
        <h3>4. Limitation of Liability</h3>
        <p>
          In no event shall Slot Store be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses resulting from your use of our platform.
        </p>

        <h3>5. Changes to Terms</h3>
        <p>
          We reserve the right to modify these Terms at any time. We will provide notice of significant changes by updating the date at the top of this page. Continued use of the platform constitutes your consent to such changes.
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
