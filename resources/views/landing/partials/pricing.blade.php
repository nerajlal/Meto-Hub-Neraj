<style>
  .pricing-section {
    background: #f8faf7; /* Or var(--bg) */
    padding: 110px 8vw;
    font-family: 'Inter', sans-serif;
  }
  .pricing-header {
    text-align: center;
    margin-bottom: 60px;
  }
  .pricing-header .eyebrow {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: inline-block;
    margin-bottom: 15px;
  }
  .pricing-header h2 {
    font-size: 2.5rem;
    color: #1f2937;
    margin: 0 0 10px;
    font-family: 'Poppins', sans-serif;
  }
  .pricing-header p {
    color: #6b7280;
    font-size: 1.1rem;
    margin: 0;
  }

  .pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
    align-items: stretch;
  }
  @media(max-width: 900px) {
    .pricing-grid {
      grid-template-columns: 1fr;
      gap: 40px;
    }
  }

  .pricing-card {
    background: #fff;
    border-radius: 16px;
    padding: 40px 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    border: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    position: relative;
  }
  
  /* Business Card Highlight */
  .pricing-card.featured {
    border: 2px solid #2e7d32;
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(46, 125, 50, 0.1);
  }
  .featured-badge {
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    background: #2e7d32;
    color: #fff;
    padding: 6px 20px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .best-value-badge {
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    background: #fef08a; /* yellow */
    color: #854d0e;
    padding: 6px 20px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
    border: 1px solid #fde047;
  }

  .card-top {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
  }
  .card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
  }
  .icon-starter { background: #e8f5e9; color: #2e7d32; }
  .icon-business { background: #2e7d32; color: #fff; }
  .icon-pro { background: #f3e8ff; color: #7e22ce; }

  .card-top h3 {
    margin: 0;
    font-size: 1.5rem;
    color: #1f2937;
    font-family: 'Poppins', sans-serif;
  }
  .card-top p {
    margin: 4px 0 0;
    font-size: 0.85rem;
    color: #6b7280;
  }

  .price-monthly {
    font-size: 2.2rem;
    font-weight: 700;
    color: #2e7d32;
    margin-bottom: 15px;
    font-family: 'Poppins', sans-serif;
  }
  .price-monthly span {
    font-size: 1rem;
    font-weight: 500;
    color: #6b7280;
  }
  .icon-pro-price {
    color: #7e22ce;
  }

  .setup-fee {
    background: #f3f4f6;
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 30px;
  }
  .icon-starter-setup { background: #f0fdf4; color: #166534; }
  .icon-business-setup { background: #f0fdf4; color: #166534; }
  .icon-pro-setup { background: #faf5ff; color: #6b21a8; }
  
  .setup-fee p {
    margin: 0 0 5px;
    font-size: 0.85rem;
    color: #6b7280;
  }
  .setup-fee h4 {
    margin: 0;
    font-size: 1.2rem;
  }

  .divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin-bottom: 20px;
  }
  .divider::before, .divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #e5e7eb;
  }
  .divider span {
    padding: 0 10px;
    color: #6b7280;
    font-size: 0.85rem;
    font-weight: 500;
  }

  .feature-list {
    list-style: none;
    padding: 0;
    margin: 0 0 30px;
    flex-grow: 1;
  }
  .feature-list li {
    font-size: 0.9rem;
    color: #374151;
    margin-bottom: 12px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
  }
  .feature-list li i {
    color: #2e7d32;
    margin-top: 3px;
    font-size: 0.9rem;
  }
  .feature-list.pro li i {
    color: #7e22ce;
  }

  .btn-pricing {
    display: block;
    width: 100%;
    text-align: center;
    padding: 14px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid #2e7d32;
  }
  .btn-outline-green {
    background: transparent;
    color: #2e7d32;
  }
  .btn-outline-green:hover {
    background: #f0fdf4;
  }
  .btn-solid-green {
    background: #2e7d32;
    color: #fff;
  }
  .btn-solid-green:hover {
    background: #1b5e20;
  }
  .btn-outline-purple {
    border-color: #7e22ce;
    color: #7e22ce;
  }
  .btn-outline-purple:hover {
    background: #faf5ff;
  }

  /* Bottom Trust Bar */
  .trust-bar {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 40px;
    margin-top: 80px;
    padding-top: 40px;
    border-top: 1px solid #e5e7eb;
  }
  .trust-item {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .trust-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.9rem;
  }
  .trust-text h5 { margin: 0; font-size: 0.9rem; color: #1f2937; }
  .trust-text p { margin: 0; font-size: 0.8rem; color: #6b7280; }

  .billing-info {
    font-size: 0.85rem;
    color: #6b7280;
    margin-top: -10px;
    margin-bottom: 20px;
    font-weight: 500;
  }

  .pricing-toggle-container {
    display: flex;
    justify-content: center;
    margin-bottom: 3rem;
  }
  .pricing-toggle {
    display: inline-flex;
    align-items: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 6px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  }
  .toggle-btn {
    border: none;
    background: transparent;
    padding: 10px 24px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 999px;
    color: #0f763e !important;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .toggle-btn.toggle-active {
    background: #0f763e !important;
    color: white !important;
  }
  .toggle-btn.toggle-active .toggle-text {
    color: white !important;
  }
  .toggle-btn:not(.toggle-active) .toggle-text {
    color: #0f763e !important;
  }
  .save-badge {
    background: #fcd34d;
    color: #000;
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 999px;
    font-weight: 700;
  }
  .pricing-grid.show-yearly .price-show-monthly { display: none; }
  .pricing-grid.show-yearly .price-show-yearly { display: block; }
  .price-show-yearly { display: none; }
</style>

<section class="pricing-section" id="pricing">
  <div class="pricing-header">
    <span class="eyebrow">All-in-one platform for grocery businesses</span>
    <h2>Launch & Grow Your Grocery Business Online</h2>
    <p>B2B + B2C &bull; Payments &bull; Delivery &bull; Apps &bull; Everything You Need</p>
  </div>

  <div class="pricing-toggle-container">
    <div class="pricing-toggle">
      <button class="toggle-btn toggle-active" id="btn-monthly"><span class="toggle-text">Monthly</span></button>
      <button class="toggle-btn" id="btn-yearly">
        <span class="toggle-text">Yearly</span>
        <span class="save-badge">Save 2 Months</span>
      </button>
    </div>
  </div>

  <div class="pricing-grid" id="pricingGrid">
    
    <!-- Web App Only Plan -->
    <div class="pricing-card">
      <div class="card-top">
        <div class="card-icon icon-starter"><i class="fa-solid fa-store"></i></div>
        <div>
          <h3>Web App Only</h3>
          <p>Perfect for small grocery stores</p>
        </div>
      </div>
      <div class="price-show-monthly">
        <div class="price-monthly">₹2,999 <span>/mo</span></div>
        <p class="billing-info">billed monthly</p>
      </div>
      <div class="price-show-yearly">
        <div class="price-monthly">₹29,990 <span>/yr</span></div>
        <p class="billing-info">billed yearly</p>
      </div>

      <div class="divider"><span>What's Included</span></div>
      
      <ul class="feature-list">
        <li><i class="fa-solid fa-circle-check"></i> 1 storefront</li>
        <li><i class="fa-solid fa-circle-check"></i> Standard themes</li>
        <li><i class="fa-solid fa-circle-check"></i> Basic pricing rules</li>
        <li><i class="fa-solid fa-circle-check"></i> Email support</li>
        <li><i class="fa-solid fa-circle-check"></i> Online Payment Gateway & COD</li>
        <li><i class="fa-solid fa-circle-check"></i> Basic Delivery Area Setup</li>
      </ul>

      <a href="javascript:void(0)" class="btn-pricing btn-outline-green pricing-btn-trigger" data-plan="starter">Choose Starter</a>
    </div>

    <!-- Web + Mobile Plan -->
    <div class="pricing-card featured">
      <div class="featured-badge">MOST POPULAR <i class="fa-solid fa-star"></i></div>
      <div class="card-top">
        <div class="card-icon icon-business"><i class="fa-solid fa-mobile-screen-button"></i></div>
        <div>
          <h3>Web App + Mobile App</h3>
          <p>Grow with Web and App</p>
        </div>
      </div>
      <div class="price-show-monthly">
        <div class="price-monthly">₹4,999 <span>/mo</span></div>
        <p class="billing-info">billed monthly</p>
      </div>
      <div class="price-show-yearly">
        <div class="price-monthly">₹49,990 <span>/yr</span></div>
        <p class="billing-info">billed yearly</p>
      </div>

      <div class="divider"><span>Everything in Starter, Plus</span></div>
      
      <ul class="feature-list">
        <li><i class="fa-solid fa-circle-check"></i> Unlimited products</li>
        <li><i class="fa-solid fa-circle-check"></i> Full pricing engine</li>
        <li><i class="fa-solid fa-circle-check"></i> Promotions & bundles</li>
        <li><i class="fa-solid fa-circle-check"></i> Advanced analytics</li>
        <li><i class="fa-solid fa-circle-check"></i> Priority support</li>
        <li><i class="fa-solid fa-circle-check"></i> B2B Registration & Approval</li>
      </ul>

      <a href="javascript:void(0)" class="btn-pricing btn-solid-green pricing-btn-trigger" data-plan="business">Choose Business</a>
      <div class="best-value-badge"><i class="fa-solid fa-star"></i> Best Value for Serious Businesses</div>
    </div>

    <!-- Everything Plan -->
    <div class="pricing-card">
      <div class="card-top">
        <div class="card-icon icon-pro"><i class="fa-solid fa-building"></i></div>
        <div>
          <h3>Everything</h3>
          <p>Web + Mobile + Delivery App</p>
        </div>
      </div>
      <div class="price-show-monthly">
        <div class="price-monthly">₹7,999 <span>/mo</span></div>
        <p class="billing-info">billed monthly</p>
      </div>
      <div class="price-show-yearly">
        <div class="price-monthly">₹79,990 <span>/yr</span></div>
        <p class="billing-info">billed yearly</p>
      </div>

      <div class="divider"><span>Everything in Business, Plus</span></div>
      
      <ul class="feature-list pro">
        <li><i class="fa-solid fa-circle-check"></i> Multi-store management</li>
        <li><i class="fa-solid fa-circle-check"></i> Regional pricing</li>
        <li><i class="fa-solid fa-circle-check"></i> Dedicated account manager</li>
        <li><i class="fa-solid fa-circle-check"></i> Custom integrations</li>
        <li><i class="fa-solid fa-circle-check"></i> Multiple Warehouses</li>
        <li><i class="fa-solid fa-circle-check"></i> Delivery Fleet Management</li>
      </ul>

      <a href="javascript:void(0)" class="btn-pricing btn-outline-purple pricing-btn-trigger" data-plan="pro">Choose Pro</a>
    </div>

  </div>

  <div class="trust-bar">
    <div class="trust-item">
      <div class="trust-icon" style="background: #4ade80;"><i class="fa-solid fa-shield-halved"></i></div>
      <div class="trust-text">
        <h5>Secure & Reliable</h5>
        <p>Enterprise Grade Security</p>
      </div>
    </div>
    <div class="trust-item">
      <div class="trust-icon" style="background: #3b82f6;"><i class="fa-solid fa-mobile-screen-button"></i></div>
      <div class="trust-text">
        <h5>Mobile First</h5>
        <p>Android & iOS Apps</p>
      </div>
    </div>
    <div class="trust-item">
      <div class="trust-icon" style="background: #f97316;"><i class="fa-solid fa-truck-fast"></i></div>
      <div class="trust-text">
        <h5>Smart Delivery</h5>
        <p>Slots, Zones & Tracking</p>
      </div>
    </div>
    <div class="trust-item">
      <div class="trust-icon" style="background: #ec4899;"><i class="fa-solid fa-chart-column"></i></div>
      <div class="trust-text">
        <h5>Business Growth</h5>
        <p>Tools & Insights</p>
      </div>
    </div>
    <div class="trust-item">
      <div class="trust-icon" style="background: #0ea5e9;"><i class="fa-solid fa-headset"></i></div>
      <div class="trust-text">
        <h5>Dedicated Support</h5>
        <p>We're Here to Help</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= FAQ ================= -->
<section class="faq" id="faq">
  <div class="section-head">
    <span class="eyebrow">FAQ</span>
    <h2>Questions merchants ask us</h2>
  </div>
  <div class="faq-list" id="faqList">
    <div class="faq-item">
      <button class="faq-question" aria-expanded="false">Is Slot Store a grocery shopping site or a platform? <span class="faq-toggle">+</span></button>
      <div class="faq-answer">
        <p>Slot Store is a SaaS platform for grocery business owners. It's not a place to buy groceries — it's the software merchants use to build and run their own online grocery stores.</p>
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-question" aria-expanded="false">Do I need a developer to launch my store? <span class="faq-toggle">+</span></button>
      <div class="faq-answer">
        <p>No. Themes, product setup, and pricing rules are all configured from the merchant dashboard — no code required.</p>
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-question" aria-expanded="false">Can I set different prices for different customers? <span class="faq-toggle">+</span></button>
      <div class="faq-answer">
        <p>Yes. The pricing engine supports customer, group, wholesale, bulk, and regional pricing rules that apply automatically at checkout.</p>
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-question" aria-expanded="false">What payment gateways are supported? <span class="faq-toggle">+</span></button>
      <div class="faq-answer">
        <p>Stripe, PayPal, Razorpay, Google Pay, Apple Pay, and major card networks are supported out of the box.</p>
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-question" aria-expanded="false">Can I connect my own domain? <span class="faq-toggle">+</span></button>
      <div class="faq-answer">
        <p>Yes, you can point any custom domain to your store and SSL is provisioned automatically.</p>
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-question" aria-expanded="false">Can I manage more than one store location? <span class="faq-toggle">+</span></button>
      <div class="faq-answer">
        <p>Enterprise plans support multi-store and multi-location management with regional pricing built in.</p>
      </div>
    </div>
  </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const btnMonthly = document.getElementById('btn-monthly');
    const btnYearly = document.getElementById('btn-yearly');
    const pricingGrid = document.getElementById('pricingGrid');
    
    if (btnMonthly && btnYearly && pricingGrid) {
      btnMonthly.addEventListener('click', () => {
        btnMonthly.classList.add('toggle-active');
        btnYearly.classList.remove('toggle-active');
        pricingGrid.classList.remove('show-yearly');
      });
      
      btnYearly.addEventListener('click', () => {
        btnYearly.classList.add('toggle-active');
        btnMonthly.classList.remove('toggle-active');
        pricingGrid.classList.add('show-yearly');
      });
    }
  });
</script>
