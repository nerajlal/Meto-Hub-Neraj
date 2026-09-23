import re

with open('resources/views/admin/billing/index.blade.php', 'r') as f:
    content = f.read()

# Find the start of @section('scripts')
scripts_start = content.find("@section('scripts')")

if scripts_start != -1:
    new_top = """@extends('layouts.admin')

@section('styles')
<style>
  /* Dashboard styling */
  .dashboard-wrapper {
      padding: 40px;
      background: #f8fafc;
      min-height: 100vh;
  }
  .plan-title {
      font-size: 3.5rem;
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
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
      font-size: 1.5rem;
      font-weight: 600;
      color: #64748b;
      margin-bottom: 30px;
  }
  .price-amount {
      color: #111827;
  }
  
  .feature-list-large {
      list-style: none;
      padding: 0;
      margin: 0;
  }
  .feature-list-large li {
      font-size: 1.1rem;
      color: #475569;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      gap: 12px;
  }
  .feature-list-large li i {
      color: #10b981;
      font-size: 1.2rem;
  }
  
  .status-card {
      background: #ffffff;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      border-top: 6px solid #d97706;
  }
  .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #fef3c7;
      color: #d97706;
      padding: 8px 24px;
      border-radius: 999px;
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 30px;
  }
  .status-badge.active { background: #dcfce7; color: #166534; border-top-color: #166534; }
  
  .btn-pay {
      background: #0f172a;
      color: #ffffff;
      border: none;
      padding: 16px 32px;
      font-size: 1.1rem;
      font-weight: 600;
      border-radius: 12px;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: background 0.3s;
      margin-bottom: 20px;
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
  .secure-text i { color: #10b981; }
  
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
  
  .pricing-grid.show-yearly .price-show-monthly { display: none !important; }
  .pricing-grid.show-yearly .price-show-yearly { display: block !important; }
  .price-show-yearly { display: none; }
</style>
@endsection

@section('content')
@php
    $currentPlan = $tenant->plan ?? 'sprout';
    
    // Dynamic values based on plan
    $planName = ucfirst($currentPlan);
    
    $monthlyPrice = 2999;
    $yearlyPriceDisplay = "29,990";
    $planId = env('RAZORPAY_PLAN_SPROUT');
    
    if ($currentPlan == 'blossom') {
        $monthlyPrice = 4999;
        $yearlyPriceDisplay = "49,990";
        $planId = env('RAZORPAY_PLAN_BLOSSOM');
    } else if ($currentPlan == 'tree') {
        $monthlyPrice = 7999;
        $yearlyPriceDisplay = "79,990";
        $planId = env('RAZORPAY_PLAN_TREE');
    }
@endphp

<div class="dashboard-wrapper">
    <div class="row align-items-start pricing-grid" id="pricingGrid">
        
        <!-- LEFT COLUMN: Plan Info -->
        <div class="col-md-7 pe-md-5">
            <h1 class="plan-title">VESPR {{ $planName }}</h1>
            
            <div class="pricing-toggle">
                <button class="toggle-btn toggle-active" id="btn-monthly">Monthly</button>
                <div style="position: relative;">
                    <button class="toggle-btn" id="btn-yearly">Yearly</button>
                    <span class="save-badge-toggle">2 Months Free</span>
                </div>
            </div>
            
            <div class="price-show-monthly price-display">
                <span class="price-amount">₹{{ number_format($monthlyPrice) }}</span> / month
            </div>
            <div class="price-show-yearly price-display">
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
        <div class="col-md-5 mt-5 mt-md-0">
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
                    
                    <button class="btn-pay subscribe-btn" data-plan="{{ $planId }}">
                        <i class="fa-regular fa-credit-card"></i> Enable Auto-Pay
                    </button>
                    <div class="secure-text">
                        <i class="fa-solid fa-lock"></i> Secure monthly billing via Razorpay
                    </div>
                @endif
            </div>
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
</div>

"""
    new_content = new_top + content[scripts_start:]
    with open('resources/views/admin/billing/index.blade.php', 'w') as f:
        f.write(new_content)
    print("Successfully updated billing index.")
else:
    print("Could not find scripts section.")
