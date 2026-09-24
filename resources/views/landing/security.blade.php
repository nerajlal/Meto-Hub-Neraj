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
          As a commerce platform processing thousands of transactions daily, security is our top priority. We employ enterprise-grade security measures to ensure your store remains online, secure, and compliant.
        </p>
      </div>

       <div class="security-grid">
         
         <div class="security-card">
           <div class="sec-icon"><i class="fa-solid fa-shield-halved"></i></div>
           <h3>Data Encryption</h3>
           <p>
             All data transmitted between your customers and our servers is encrypted using industry-standard TLS 1.3. Sensitive data at rest is encrypted using AES-256 encryption.
           </p>
         </div>

         <div class="security-card">
           <div class="sec-icon"><i class="fa-solid fa-credit-card"></i></div>
           <h3>PCI-DSS Compliance</h3>
           <p>
             We partner with top Tier 1 payment processors. We do not store raw credit card numbers on our servers, ensuring your store is fully PCI-DSS Level 1 compliant out of the box.
           </p>
         </div>

         <div class="security-card">
           <div class="sec-icon"><i class="fa-solid fa-server"></i></div>
           <h3>Infrastructure & Uptime</h3>
           <p>
             Hosted on highly available cloud architecture, we guarantee a 99.99% uptime SLA. Our infrastructure automatically scales during high-traffic events to prevent downtime.
           </p>
         </div>

         <div class="security-card">
           <div class="sec-icon"><i class="fa-solid fa-user-lock"></i></div>
           <h3>Access Control</h3>
           <p>
             Protect your admin dashboard with role-based access control (RBAC). Assign specific permissions to staff so they only see the data they need.
           </p>
         </div>

       </div>

       <div class="vulnerability-box">
          <h4>Vulnerability Reporting</h4>
          <p>If you believe you have found a security vulnerability in our platform, please disclose it to us responsibly.</p>
          <a href="mailto:security@metohub.com">security@metohub.com</a>
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
      background: linear-gradient(135deg, rgba(46, 125, 50, 0.1), rgba(67, 160, 71, 0.1));
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

    .vulnerability-box h4 {
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
