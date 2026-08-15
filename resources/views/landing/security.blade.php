@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">Trust & Compliance</span>
      <h2>Security at Slot Store</h2>
      <p>How we protect your data, your customers, and your business.</p>
    </div>
    
    <div style="max-width: 1100px; margin: 0 auto; padding: 0 5vw;">
      
      <div style="text-align: center; margin-bottom: 60px;">
        <p style="color: var(--muted); line-height: 1.8; font-size: 1.15rem; max-width: 800px; margin: 0 auto;">
          As a commerce platform processing thousands of transactions daily, security is our top priority. We employ enterprise-grade security measures to ensure your store remains online, secure, and compliant.
        </p>
      </div>

      <div class="promo-grid" style="margin-bottom: 60px;">
        
        <div class="feature-card" style="padding: 40px;">
          <div class="f-icon" style="background: rgba(46, 125, 50, 0.1); width: 60px; height: 60px; font-size: 1.5rem; margin-bottom: 20px;"><i class="fa-solid fa-shield-halved"></i></div>
          <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.4rem;">Data Encryption</h3>
          <p style="color: var(--muted); line-height: 1.6; font-size: 1rem; margin: 0;">
            All data transmitted between your customers and our servers is encrypted using industry-standard TLS 1.3. Sensitive data at rest, such as API keys and customer PII, is encrypted using AES-256 encryption.
          </p>
        </div>

        <div class="feature-card" style="padding: 40px;">
          <div class="f-icon" style="background: rgba(46, 125, 50, 0.1); width: 60px; height: 60px; font-size: 1.5rem; margin-bottom: 20px;"><i class="fa-solid fa-credit-card"></i></div>
          <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.4rem;">PCI-DSS Compliance</h3>
          <p style="color: var(--muted); line-height: 1.6; font-size: 1rem; margin: 0;">
            Slot Store partners with Stripe, Razorpay, and other Tier 1 payment processors. We do not store raw credit card numbers on our servers, ensuring your store is fully PCI-DSS Level 1 compliant out of the box.
          </p>
        </div>

        <div class="feature-card" style="padding: 40px;">
          <div class="f-icon" style="background: rgba(46, 125, 50, 0.1); width: 60px; height: 60px; font-size: 1.5rem; margin-bottom: 20px;"><i class="fa-solid fa-server"></i></div>
          <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.4rem;">Infrastructure & Uptime</h3>
          <p style="color: var(--muted); line-height: 1.6; font-size: 1rem; margin: 0;">
            Hosted on highly available cloud architecture, we guarantee a 99.99% uptime SLA. Our infrastructure automatically scales during high-traffic events (like holiday sales) to prevent downtime.
          </p>
        </div>

        <div class="feature-card" style="padding: 40px;">
          <div class="f-icon" style="background: rgba(46, 125, 50, 0.1); width: 60px; height: 60px; font-size: 1.5rem; margin-bottom: 20px;"><i class="fa-solid fa-user-lock"></i></div>
          <h3 style="color: var(--dark); margin-bottom: 15px; font-size: 1.4rem;">Access Control</h3>
          <p style="color: var(--muted); line-height: 1.6; font-size: 1rem; margin: 0;">
            Protect your admin dashboard with role-based access control (RBAC). Assign specific permissions to cashiers, store managers, and delivery drivers so they only see the data they need.
          </p>
        </div>

      </div>

      <div style="text-align: center; background: var(--white); border-radius: var(--radius-lg); padding: 50px; border: 1px solid var(--line);">
        <h4 style="color: var(--dark); margin-bottom: 15px; font-size: 1.4rem;">Vulnerability Reporting</h4>
        <p style="color: var(--muted); max-width: 600px; margin: 0 auto 20px; font-size: 1.05rem;">
          If you believe you have found a security vulnerability in our platform, please disclose it to us responsibly.
        </p>
        <a href="mailto:security@slotstore.com" style="color: var(--primary); font-weight: 600; font-size: 1.05rem; text-decoration: underline;">security@slotstore.com</a>
      </div>

    </div>
  </section>
@endsection
