@extends('layouts.landing')
@section('content')
  <!-- Premium Legal Hero -->
  <section class="legal-hero">
    <div class="legal-hero-blob"></div>
    <div class="legal-hero-inner">
      <span class="eyebrow fade-up">Trust & Compliance</span>
      <h1 class="fade-up" style="animation-delay: 0.1s;">Security at MetoHub</h1>
      <p class="fade-up" style="animation-delay: 0.2s;">How we protect your data, your customers, and your business.</p>
    </div>
  </section>

  <!-- Premium Content Section -->
  <section class="legal-content-section">
    <div class="legal-container fade-up" style="animation-delay: 0.3s;">
       
       <div style="text-align: center; margin-bottom: 60px;">
        <p style="color: var(--muted); line-height: 1.8; font-size: 1.15rem; max-width: 800px; margin: 0 auto;">
          MetoHub uses account authentication, store access checks and request validation to help protect merchant accounts. The controls below describe application behavior; hosting, HTTPS and operational monitoring also require correct production configuration.
        </p>
      </div>

       <div class="security-grid">
         
         <div class="security-card">
           <div class="sec-icon"><i class="fa-solid fa-shield-halved"></i></div>
           <h3>Account Protection</h3>
           <p>
             Account passwords are hashed. Authentication routes limit repeated login attempts, and merchant dashboard access checks the signed-in account and store.
           </p>
         </div>

         <div class="security-card">
           <div class="sec-icon"><i class="fa-solid fa-credit-card"></i></div>
           <h3>Subscription Payments</h3>
           <p>
             MetoHub subscriptions use Razorpay’s hosted checkout. Customer storefront checkout currently supports cash on delivery. Platform subscription billing is separate from customer order payments.
           </p>
         </div>

         <div class="security-card">
           <div class="sec-icon"><i class="fa-solid fa-server"></i></div>
           <h3>Request Protection</h3>
           <p>
             Browser forms use CSRF protection and server-side validation. Sensitive account actions require authentication. These application controls work alongside the server’s HTTPS and hosting configuration.
           </p>
         </div>

         <div class="security-card">
           <div class="sec-icon"><i class="fa-solid fa-user-lock"></i></div>
           <h3>Access Control</h3>
           <p>
             Merchant and delivery dashboards check account roles and store membership. Keep your account credentials private and contact us if you suspect unauthorized access.
           </p>
         </div>

       </div>

       <div class="vulnerability-box">
          <h2>Vulnerability Reporting</h2>
          <p>If you believe you have found a security vulnerability in our platform, please disclose it to us responsibly.</p>
          <a href="mailto:{{ config('marketing.contact.email') }}">{{ config('marketing.contact.email') }}</a>
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
      max-width: 1000px;
      margin: -40px auto 0;
      position: relative;
      z-index: 10;
    }
    
    .security-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 30px;
      margin-bottom: 50px;
    }

    .security-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: var(--radius-xl);
      padding: 50px;
      box-shadow: 0 20px 40px rgba(31, 41, 55, 0.06);
      border: 1px solid rgba(255, 255, 255, 1);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .security-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 30px 60px rgba(31, 41, 55, 0.1);
    }

    .sec-icon {
      width: 64px;
      height: 64px;
      border-radius: 16px;
      background: linear-gradient(135deg, rgba(36, 74, 59, 0.1), rgba(67, 160, 71, 0.1));
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin-bottom: 24px;
    }

    .security-card h3 {
      color: var(--dark);
      margin-top: 0;
      margin-bottom: 15px;
      font-size: 1.5rem;
      font-weight: 700;
    }

    .security-card p {
      color: var(--muted);
      font-size: 1.05rem;
      line-height: 1.7;
      margin-bottom: 0;
    }

    .vulnerability-box {
      background: var(--white);
      border-radius: var(--radius-lg);
      padding: 50px;
      text-align: center;
      border: 1px solid var(--line);
      box-shadow: 0 10px 30px rgba(31, 41, 55, 0.04);
    }

    .vulnerability-box h2 {
      font-size: 1.4rem;
      color: var(--dark);
      margin-bottom: 15px;
    }

    .vulnerability-box p {
      color: var(--muted);
      font-size: 1.05rem;
      max-width: 600px;
      margin: 0 auto 20px;
    }

    .vulnerability-box a {
      color: var(--primary);
      font-weight: 600;
      font-size: 1.1rem;
      text-decoration: underline;
      display: inline-block;
    }

    .vulnerability-box a:hover {
      color: var(--primary-dark);
    }
    
    @media (max-width: 768px) {
      .security-grid {
        grid-template-columns: 1fr;
      }
      .security-card {
        padding: 40px 30px;
      }
      .legal-hero {
        padding: 160px 6vw 80px;
      }
      .legal-hero h1 {
        font-size: 1.8rem;
      }
    }
  </style>
@endsection
