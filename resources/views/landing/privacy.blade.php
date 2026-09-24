@extends('layouts.landing')
@section('content')
  <!-- Premium Legal Hero -->
  <section class="legal-hero">
    <div class="legal-hero-blob"></div>
    <div class="legal-hero-inner">
      <span class="eyebrow fade-up">Legal Information</span>
      <h1 class="fade-up" style="animation-delay: 0.1s;">Privacy Policy</h1>
      <p class="fade-up" style="animation-delay: 0.2s;">Last updated: {{ now()->format('F j, Y') }}</p>
    </div>
  </section>

  <!-- Premium Content Section -->
  <section class="legal-content-section">
    <div class="legal-container fade-up" style="animation-delay: 0.3s;">
       <div class="legal-card">
        
        <h3>1. Information We Collect</h3>
        <p>
          We collect information you provide directly to us, such as when you create or modify your account, request on-demand services, contact customer support, or otherwise communicate with us.
        </p>
        
        <h3>2. How We Use Information</h3>
        <p>
          We may use the information we collect about you to provide, maintain, and improve our services, including to facilitate payments, send receipts, provide products and services you request, and develop new features.
        </p>
        
        <h3>3. Data Security</h3>
        <p>
          We take reasonable measures to help protect information about you from loss, theft, misuse and unauthorized access, disclosure, alteration and destruction. Our security protocols employ the latest encryption standards.
        </p>

        <h3>4. Contact Us</h3>
        <p>
          If you have any questions about this Privacy Policy, please contact us at <a href="mailto:privacy@metohub.com">privacy@metohub.com</a>.
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
