@extends('layouts.admin')

@section('styles')
  <style>
    /* Dashboard styling */
    .dashboard-wrapper {
      padding: 20px 40px 40px 40px;
      background: #f8fafc;
      min-height: 100vh;
    }

    .plan-title {
      font-size: 2.5rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 20px;
    }

    .pricing-toggle {
      display: inline-flex;
      align-items: center;
      background: #f1f5f9;
      border-radius: 999px;
      padding: 4px;
      position: relative;
      margin-bottom: 30px;
    }

    .toggle-btn {
      border: none;
      background: transparent;
      padding: 8px 24px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 999px;
      color: #64748b;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      z-index: 2;
    }

    .toggle-btn.toggle-active {
      background: #ffffff;
      color: #0f172a;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .save-badge-toggle {
      position: absolute;
      top: -12px;
      right: 0;
      background: #dcfce7;
      color: #166534;
      font-size: 0.7rem;
      padding: 2px 8px;
      border-radius: 999px;
      font-weight: 700;
      z-index: 3;
      white-space: nowrap;
    }

    .price-display {
      font-size: 1.15rem;
      font-weight: 600;
      color: #64748b;
      margin-bottom: 30px;
      display: flex;
      align-items: baseline;
      gap: 5px;
    }

    .price-amount {
      color: #111827;
      font-size: 1.8rem;
    }

    .feature-list-large {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .feature-list-large li {
      font-size: 0.95rem;
      color: #475569;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .feature-list-large li i {
      color: #8b5cf6;
      font-size: 1.1rem;
    }

    .status-card {
      background: #ffffff;
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      border-top: 4px solid #f59e0b;
    }

    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #fef3c7;
      color: #d97706;
      padding: 6px 20px;
      border-radius: 999px;
      font-weight: 600;
      font-size: 0.85rem;
      margin-bottom: 25px;
    }

    .status-badge.active {
      background: #dcfce7;
      color: #166534;
      border-top-color: #166534;
    }

    .btn-pay {
      background: #0f172a;
      color: #ffffff;
      border: none;
      padding: 12px 24px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 12px;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: background 0.3s;
      margin-bottom: 15px;
    }

    .btn-pay:hover {
      background: #1e293b;
      color: #ffffff;
    }

    .secure-text {
      font-size: 0.9rem;
      color: #64748b;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    .secure-text i {
      color: #8b5cf6;
    }

    .billing-history-card {
      background: #ffffff;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      border: 1px solid #e2e8f0;
      margin-top: 60px;
    }

    .billing-history-title {
      font-size: 1.4rem;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 60px;
    }

    .empty-state {
      text-align: center;
      padding: 40px 0;
    }

    .empty-icon {
      font-size: 3rem;
      color: #cbd5e1;
      margin-bottom: 20px;
    }

    .empty-title {
      font-size: 1.1rem;
      color: #475569;
      margin-bottom: 10px;
    }

    .empty-desc {
      font-size: 0.95rem;
      color: #94a3b8;
    }
  </style>
@endsection

@section('content')
  @php
    $currentPlan = $tenant->plan ?? 'base';

    // Dynamic values based on plan
    $planName = ucfirst($currentPlan);

    $monthlyPrice = 99;
    $yearlyPriceDisplay = "1,089";
    $planIdMonthly = env('RAZORPAY_PLAN_BASE_MONTHLY', env('RAZORPAY_PLAN_BASE'));
    $planIdYearly = env('RAZORPAY_PLAN_BASE_YEARLY', env('RAZORPAY_PLAN_BASE'));
    
    if ($currentPlan == 'pro') {
        $monthlyPrice = 249;
        $yearlyPriceDisplay = "2,739";
        $planIdMonthly = env('RAZORPAY_PLAN_PRO_MONTHLY', env('RAZORPAY_PLAN_PRO'));
        $planIdYearly = env('RAZORPAY_PLAN_PRO_YEARLY', env('RAZORPAY_PLAN_PRO'));
    } else if ($currentPlan == 'enterprise') {
        $monthlyPrice = 749;
        $yearlyPriceDisplay = "8,239";
        $planIdMonthly = env('RAZORPAY_PLAN_ENTERPRISE_MONTHLY', env('RAZORPAY_PLAN_ENTERPRISE'));
        $planIdYearly = env('RAZORPAY_PLAN_ENTERPRISE_YEARLY', env('RAZORPAY_PLAN_ENTERPRISE'));
    }
  @endphp

  <div class="dashboard-wrapper">
    <div class="row align-items-start pricing-grid" id="pricingGrid">

      <!-- LEFT COLUMN: Plan Info -->
      <div class="col-md-7 pe-md-5">
        <h1 class="plan-title">Plan - {{ $planName }}</h1>

        <div class="pricing-toggle">
          <button class="toggle-btn toggle-active" id="custom-btn-monthly">Monthly</button>
          <button class="toggle-btn" id="custom-btn-yearly">
            Yearly
            <span class="save-badge-toggle">1 Month Free</span>
          </button>
        </div>

        <div class="price-display" id="custom-price-monthly">
          <span class="price-amount">₹{{ number_format($monthlyPrice) }}</span> / month
        </div>
        <div class="price-display" id="custom-price-yearly" style="display: none;">
          <span class="price-amount">₹{{ $yearlyPriceDisplay }}</span> / year
        </div>

        <ul class="feature-list-large">
          <li><i class="fa-solid fa-circle-check"></i> Complete premium storefront access</li>
          <li><i class="fa-solid fa-circle-check"></i> Advanced analytics and reporting</li>
          <li><i class="fa-solid fa-circle-check"></i> Secure Razorpay integration</li>
          <li><i class="fa-solid fa-circle-check"></i> 24/7 dedicated support</li>
        </ul>
      </div>

      <!-- RIGHT COLUMN: Status Card -->
      <div class="col-md-4 offset-md-1 mt-5 mt-md-0">
        <div class="status-card" @if($tenant->subscription_status === 'active') style="border-top-color: #166534;" @endif>
          @if($tenant->subscription_status === 'active')
            <div class="status-badge active">
              <i class="fa-solid fa-circle-check"></i> ACTIVE
            </div>

            <button class="btn-pay" disabled style="background:#475569;">
              <i class="fa-solid fa-check"></i> Auto-Pay Enabled
            </button>
            <div class="secure-text">
              <i class="fa-solid fa-lock"></i> Secure monthly billing via Razorpay
            </div>
          @else
            <div class="status-badge">
              <i class="fa-solid fa-hourglass-half"></i> GRACE PERIOD
            </div>

            <button class="btn-pay subscribe-btn" data-tier="{{ $currentPlan }}">
              <i class="fa-regular fa-credit-card"></i> Enable Auto-Pay
            </button>
            <div class="secure-text">
              <i class="fa-solid fa-lock"></i> Secure monthly billing via Razorpay
            </div>
          @endif
        </div>

        <button class="btn btn-outline-secondary mt-4 fw-bold w-100" id="explorePlansBtn"
          style="border-radius: 12px; padding: 12px 24px;">Explore Other Plans</button>
      </div>

    </div>

    <!-- BOTTOM ROW: Billing History -->
    <div class="row">
      <div class="col-12">
        <div class="billing-history-card">
          <div class="billing-history-title">Billing History</div>

          <div class="empty-state">
            <i class="fa-solid fa-file-invoice-dollar empty-icon"></i>
            <div class="empty-title">No invoices available yet.</div>
            <div class="empty-desc">Once your auto-pay is processed, your receipts will appear here.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- EXPLORE PLANS GRID (Hidden by default) -->
    <div class="row mt-5" id="explorePlansSection" style="display: none;">
      <div class="col-12 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
          <h3 class="fw-bold mb-1">Available Plans</h3>
          <p class="text-muted mb-0">Choose the plan that fits your business needs.</p>
        </div>

        <div class="pricing-toggle m-0">
          <button class="toggle-btn toggle-active explore-btn-monthly">Monthly</button>
          <button class="toggle-btn explore-btn-yearly">
            Yearly
            <span class="save-badge-toggle">1 Month Free</span>
          </button>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="status-card" style="border-top-color: #64748b; align-items: flex-start; text-align: left;">
          <h4 class="fw-bold">Base</h4>
          <div class="price-display mb-3 explore-price-monthly">
            <span class="price-amount" style="font-size: 1.5rem;">₹99</span> / month
          </div>
          <div class="price-display mb-3 explore-price-yearly" style="display: none;">
            <span class="price-amount" style="font-size: 1.5rem;">₹1,089</span> / year
          </div>
          <ul class="feature-list-large" style="margin-bottom: 20px;">
            <li style="font-size: 0.85rem;"><i class="fa-solid fa-check"></i> Subdomain from us only</li>
            <li style="font-size: 0.85rem;"><i class="fa-solid fa-check"></i> Unlimited visitors</li>
          </ul>
          <button class="btn btn-outline-dark w-100 subscribe-btn" data-tier="base">Select Plan</button>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="status-card" style="border-top-color: #8b5cf6; align-items: flex-start; text-align: left;">
          <h4 class="fw-bold">Pro</h4>
          <div class="price-display mb-3 explore-price-monthly">
            <span class="price-amount" style="font-size: 1.5rem;">₹249</span> / month
          </div>
          <div class="price-display mb-3 explore-price-yearly" style="display: none;">
            <span class="price-amount" style="font-size: 1.5rem;">₹2,739</span> / year
          </div>
          <ul class="feature-list-large" style="margin-bottom: 20px;">
            <li style="font-size: 0.85rem;"><i class="fa-solid fa-check"></i> Custom Domain</li>
            <li style="font-size: 0.85rem;"><i class="fa-solid fa-check"></i> No watermark in footer</li>
          </ul>
          <button class="btn btn-outline-dark w-100 subscribe-btn" data-tier="pro">Select Plan</button>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="status-card" style="border-top-color: #3b82f6; align-items: flex-start; text-align: left;">
          <h4 class="fw-bold">Enterprise</h4>
          <div class="price-display mb-3 explore-price-monthly">
            <span class="price-amount" style="font-size: 1.5rem;">₹749</span> / month
          </div>
          <div class="price-display mb-3 explore-price-yearly" style="display: none;">
            <span class="price-amount" style="font-size: 1.5rem;">₹8,239</span> / year
          </div>
          <ul class="feature-list-large" style="margin-bottom: 20px;">
            <li style="font-size: 0.85rem;"><i class="fa-solid fa-check"></i> Sell Unlimited products</li>
            <li style="font-size: 0.85rem;"><i class="fa-solid fa-check"></i> Priority Support</li>
          </ul>
          <button class="btn btn-outline-dark w-100 subscribe-btn" data-tier="enterprise">Select Plan</button>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    const btnMonthlyTop = document.getElementById('custom-btn-monthly');
    const btnYearlyTop = document.getElementById('custom-btn-yearly');

    const btnsMonthlyExplore = document.querySelectorAll('.explore-btn-monthly');
    const btnsYearlyExplore = document.querySelectorAll('.explore-btn-yearly');

    const priceMonthly = document.getElementById('custom-price-monthly');
    const priceYearly = document.getElementById('custom-price-yearly');

    const explorePricesMonthly = document.querySelectorAll('.explore-price-monthly');
    const explorePricesYearly = document.querySelectorAll('.explore-price-yearly');

    function setPricingMode(mode) {
      if (mode === 'monthly') {
        // Update Top Buttons
        if (btnMonthlyTop) btnMonthlyTop.classList.add('toggle-active');
        if (btnYearlyTop) btnYearlyTop.classList.remove('toggle-active');

        // Update Explore Buttons
        btnsMonthlyExplore.forEach(btn => btn.classList.add('toggle-active'));
        btnsYearlyExplore.forEach(btn => btn.classList.remove('toggle-active'));

        // Update Top Prices
        if (priceMonthly) priceMonthly.style.display = 'flex';
        if (priceYearly) priceYearly.style.display = 'none';

        // Update Explore Prices
        explorePricesMonthly.forEach(el => el.style.display = 'flex');
        explorePricesYearly.forEach(el => el.style.display = 'none');
      } else {
        // Update Top Buttons
        if (btnYearlyTop) btnYearlyTop.classList.add('toggle-active');
        if (btnMonthlyTop) btnMonthlyTop.classList.remove('toggle-active');

        // Update Explore Buttons
        btnsYearlyExplore.forEach(btn => btn.classList.add('toggle-active'));
        btnsMonthlyExplore.forEach(btn => btn.classList.remove('toggle-active'));

        // Update Top Prices
        if (priceYearly) priceYearly.style.display = 'flex';
        if (priceMonthly) priceMonthly.style.display = 'none';

        // Update Explore Prices
        explorePricesYearly.forEach(el => el.style.display = 'flex');
        explorePricesMonthly.forEach(el => el.style.display = 'none');
      }
    }

    // Attach listeners to top toggle
    if (btnMonthlyTop) btnMonthlyTop.addEventListener('click', (e) => { e.preventDefault(); setPricingMode('monthly'); });
    if (btnYearlyTop) btnYearlyTop.addEventListener('click', (e) => { e.preventDefault(); setPricingMode('yearly'); });

    // Attach listeners to explore grid toggle
    btnsMonthlyExplore.forEach(btn => btn.addEventListener('click', (e) => { e.preventDefault(); setPricingMode('monthly'); }));
    btnsYearlyExplore.forEach(btn => btn.addEventListener('click', (e) => { e.preventDefault(); setPricingMode('yearly'); }));

    const exploreBtn = document.getElementById('explorePlansBtn');
    const exploreSection = document.getElementById('explorePlansSection');
    if (exploreBtn && exploreSection) {
      exploreBtn.addEventListener('click', (e) => {
        e.preventDefault();
        exploreSection.style.display = 'flex';
        exploreBtn.style.display = 'none';
        exploreSection.scrollIntoView({ behavior: 'smooth' });
      });
    }

    document.querySelectorAll('.subscribe-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
        const isYearly = btnYearlyTop && btnYearlyTop.classList.contains('toggle-active');
        const cycle = isYearly ? 'yearly' : 'monthly';
        const planTier = this.dataset.tier || 'base';
        
        const originalText = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        this.disabled = true;

        fetch('{{ route("admin.billing.subscribe") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ cycle: cycle, plan_tier: planTier })
        })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              var options = {
                "key": data.key,
                "subscription_id": data.subscription_id,
                "name": "MetoHub",
                "description": "Software Subscription",
                "handler": function (response) {
                  // Verify payment
                  fetch('{{ route("admin.billing.verify") }}', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                      razorpay_payment_id: response.razorpay_payment_id,
                      razorpay_subscription_id: response.razorpay_subscription_id,
                      razorpay_signature: response.razorpay_signature
                    })
                  })
                    .then(res => res.json())
                    .then(verifyData => {
                      if (verifyData.success) {
                        alert('Subscription activated successfully!');
                        window.location.reload();
                      } else {
                        alert('Verification failed: ' + verifyData.message);
                      }
                    });
                },
                "theme": {
                  "color": "#8b5cf6"
                }
              };
              var rzp1 = new Razorpay(options);
              rzp1.open();
            } else {
              alert('Error creating subscription: ' + data.message);
            }
          })
          .catch(err => {
            console.error(err);
            alert('An error occurred.');
          })
          .finally(() => {
            this.innerHTML = originalText;
            this.disabled = false;
          });
      });
    });
  </script>
@endpush