@extends('layouts.storefront')

@section('title', 'Secure Checkout | VESPR Perfumes')

@section('content')
<div class="checkout-page-container">
    <div class="checkout-header-lg">
        <h1 class="checkout-title-lg">Checkout</h1>
        <p class="checkout-subtitle">Complete your order with VESPR's secure checkout.</p>
    </div>

    <div class="checkout-main-grid">
        <!-- Form -->
        <div class="checkout-forms-panel">
            <form action="{{ route('order.place') }}" method="POST" id="main-checkout-form">
                @csrf
                
                <div class="checkout-card">
                    <h2 class="card-heading"><i class="fa-solid fa-user-check"></i> 1. Contact Details</h2>
                    <div class="form-grid-lg">
                        <div class="form-group-lg full">
                            <label>Full Name</label>
                            <input type="text" name="name" value="{{ auth()->user()->name ?? '' }}" required placeholder="John Doe">
                        </div>
                        <div class="form-group-lg">
                            <label>Email Address</label>
                            <input type="email" name="email" value="{{ auth()->user()->email ?? '' }}" required placeholder="john@example.com">
                        </div>
                        <div class="form-group-lg">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" value="{{ auth()->user()->phone ?? '' }}" required placeholder="9876543210">
                        </div>
                    </div>
                </div>

                <div class="checkout-card">
                    <h2 class="card-heading"><i class="fa-solid fa-truck-fast"></i> 2. Delivery Address</h2>
                    <div class="form-grid-lg">
                        <div class="form-group-lg full">
                            <label>Street Address</label>
                            <input type="text" name="address" value="{{ $address->address ?? '' }}" required placeholder="House No, Apartment, Street Name">
                        </div>
                        <div class="form-group-lg">
                            <label>City</label>
                            <input type="text" name="city" value="{{ $address->city ?? '' }}" required placeholder="City">
                        </div>
                        <div class="form-group-lg">
                            <label>State</label>
                            <input type="text" name="state" value="{{ $address->state ?? '' }}" required placeholder="State">
                        </div>
                        <div class="form-group-lg">
                            <label>PIN Code</label>
                            <input type="text" name="pincode" value="{{ $address->pincode ?? '' }}" required placeholder="6-digit PIN">
                        </div>
                        <div class="form-group-lg">
                            <label>Country</label>
                            <input type="text" value="India" disabled style="background: var(--section-bg);">
                        </div>
                    </div>
                </div>

                <div class="checkout-card">
                    <h2 class="card-heading"><i class="fa-solid fa-credit-card"></i> 3. Payment Selection</h2>
                    <div class="payment-selection-grid">
                        <label class="pay-option active">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <div class="pay-content">
                                <span class="pay-title">Cash on Delivery</span>
                                <span class="pay-desc">Pay at your doorstep when you receive the order.</span>
                            </div>
                            <i class="fa-solid fa-money-bill-1-wave"></i>
                        </label>
                        <label class="pay-option">
                            <input type="radio" name="payment_method" value="online">
                            <div class="pay-content">
                                <span class="pay-title">Online Payment</span>
                                <span class="pay-desc">Pay securely via Razorpay (UPI, Card, Wallets).</span>
                            </div>
                            <i class="fa-solid fa-shield-halved"></i>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-complete-order">
                    Complete Order • ₹{{ number_format($total, 2) }}
                </button>
            </form>
        </div>

        <!-- Sticky Summary -->
        <aside class="checkout-sticky-summary">
            <div class="order-summary-card">
                <h3 class="summary-heading-sm">Order Summary</h3>
                <div class="summary-items-scroll">
                    @foreach($cart as $item)
                    <div class="s-item-row">
                        <div class="s-item-img">
                            @php 
                                $checkoutImg = $item['image'] ?? 'Images/default.png';
                                if (!$checkoutImg || $checkoutImg == '') {
                                    $checkoutImg = 'Images/default.png';
                                }
                            @endphp
                            <img src="{{ asset($checkoutImg) }}" alt="{{ $item['name'] }}" onerror="this.src='{{ asset('Images/default.png') }}'">
                            <span class="s-item-qty">{{ $item['quantity'] }}</span>
                        </div>
                        <div class="s-item-info">
                            <span class="s-item-name">{{ $item['name'] }}</span>
                            <span class="s-item-meta">{{ $item['size'] }}</span>
                        </div>
                        <span class="s-item-price">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="summary-totals-lg">
                    <div class="st-row">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    @if($savings > 0)
                    <div class="st-row" style="color: #10b981;">
                        <span>Volume Discount</span>
                        <span>-₹{{ number_format($savings, 2) }}</span>
                    </div>
                    @endif
                    <div class="st-row">
                        <span>Shipping</span>
                        <span style="color: #10b981; font-weight: 700;">FREE</span>
                    </div>
                    <hr class="st-divider">
                    <div class="st-row grand-total-lg">
                        <span>Grand Total</span>
                        <span>₹{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="summary-footer-badges">
                    <div class="badge-item"><i class="fa-solid fa-lock"></i> 128-bit SSL</div>
                    <div class="badge-item"><i class="fa-solid fa-rotate-left"></i> 14-day Returns</div>
                    <div class="badge-item"><i class="fa-solid fa-certificate"></i> Authentic</div>
                </div>
            </div>
        </aside>
    </div>
</div>

<style>
    .checkout-page-container { padding: 1rem 0; }
    .checkout-header-lg { margin-bottom: 3.5rem; }
    .checkout-title-lg { font-size: 2.75rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.5rem; }
    .checkout-subtitle { color: var(--text-muted); font-size: 1.1rem; }

    .checkout-main-grid { display: grid; grid-template-columns: 1fr 400px; gap: 4rem; align-items: start; }

    .checkout-card { background: #fff; border: 1px solid var(--border-color); border-radius: 1.5rem; padding: 2.5rem; margin-bottom: 2rem; }
    .card-heading { font-size: 1.25rem; font-weight: 700; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; }
    
    .form-grid-lg { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .form-group-lg.full { grid-column: span 2; }
    .form-group-lg label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .form-group-lg input { width: 100%; padding: 1rem; border: 1.5px solid var(--border-color); border-radius: 1rem; font-size: 1rem; transition: var(--transition); background: #fff; }
    .form-group-lg input:focus { outline: none; border-color: var(--accent-color); box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1); }

    .payment-selection-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    .pay-option { display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem; border: 1.5px solid var(--border-color); border-radius: 1.25rem; cursor: pointer; transition: var(--transition); position: relative; }
    .pay-option:has(input:checked) { border-color: var(--primary-color); background: var(--section-bg); }
    .pay-option input { accent-color: var(--primary-color); transform: scale(1.2); }
    .pay-content { flex-grow: 1; }
    .pay-title { display: block; font-weight: 700; font-size: 1.1rem; margin-bottom: 0.25rem; }
    .pay-desc { font-size: 0.85rem; color: var(--text-muted); }
    .pay-option i { font-size: 1.5rem; color: var(--text-muted); opacity: 0.5; }

    .btn-complete-order { width: 100%; background: var(--primary-color); color: #fff; border: none; padding: 1.5rem; border-radius: 9999px; font-weight: 800; font-size: 1.25rem; cursor: pointer; transition: var(--transition); margin-top: 1rem; }
    .btn-complete-order:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); background: #1e293b; }

    .checkout-sticky-summary { position: sticky; top: 7rem; align-self: flex-start; }
    .order-summary-card { background: var(--section-bg); padding: 2.5rem; border-radius: 2rem; border: 1px solid var(--border-color); }
    .summary-heading-sm { font-size: 1.25rem; font-weight: 700; margin-bottom: 2rem; }
    
    .summary-items-scroll { max-height: 300px; overflow-y: auto; margin-bottom: 2rem; padding-right: 0.5rem; }
    .s-item-row { display: flex; gap: 1rem; align-items: center; margin-bottom: 1.25rem; }
    .s-item-img { width: 70px; height: 70px; border-radius: 1rem; overflow: hidden; background: #fff; position: relative; border: 1px solid var(--border-color); flex-shrink: 0; }
    .s-item-img img { width: 100%; height: 100%; object-fit: cover; }
    .s-item-qty { position: absolute; top: -5px; right: -5px; background: var(--primary-color); color: #fff; width: 22px; height: 22px; border-radius: 50%; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; font-weight: 700; border: 2px solid #fff; }
    .s-item-info { flex-grow: 1; min-width: 0; }
    .s-item-name { display: block; font-size: 0.95rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .s-item-meta { font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }
    .s-item-price { font-weight: 700; font-size: 1rem; color: var(--primary-color); }

    .summary-totals-lg { border-top: 1px solid var(--border-color); padding-top: 2rem; }
    .st-row { display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 1rem; color: var(--text-muted); }
    .grand-total-lg { font-size: 1.5rem; font-weight: 800; color: var(--primary-color); margin-top: 1rem; }
    .st-divider { border: none; border-top: 1px solid var(--border-color); margin: 1.5rem 0; }

    .summary-footer-badges { display: flex; justify-content: space-between; gap: 0.5rem; margin-top: 2rem; }
    .badge-item { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; }
    .badge-item i { font-size: 1.25rem; color: var(--accent-color); }

    @media (max-width: 1024px) {
        .checkout-title-lg { font-size: 2rem; }
        .checkout-main-grid { grid-template-columns: 1fr; gap: 2rem; }
        .checkout-sticky-summary { order: -1; position: static; }
        
        .checkout-card { padding: 1.5rem; }
        .form-grid-lg { grid-template-columns: 1fr; gap: 1rem; }
        .form-group-lg.full { grid-column: span 1; }
        
        .badge-item { font-size: 0.6rem; }
    }

    @media (max-width: 600px) {
        .checkout-page-container { overflow-x: hidden; max-width: 100%; }
        .checkout-header-lg { margin-bottom: 2rem; }
        .checkout-title-lg { font-size: 1.6rem; }
        .checkout-subtitle { font-size: 0.95rem; }
        .card-heading { font-size: 1rem; flex-wrap: wrap; gap: 0.5rem; }
        .checkout-card { padding: 1.25rem; border-radius: 1rem; margin-bottom: 1.25rem; }
        .pay-option { padding: 1rem; gap: 0.75rem; }
        .pay-title { font-size: 0.95rem; }
        .pay-desc { font-size: 0.8rem; }
        .btn-complete-order { font-size: 1rem; padding: 1.1rem; border-radius: 9999px; }
        .order-summary-card { padding: 1.5rem; border-radius: 1.25rem; }
        .grand-total-lg { font-size: 1.25rem; }
        .summary-footer-badges { gap: 0.25rem; }
        .badge-item { font-size: 0.6rem; }
    }

    @media (max-width: 400px) {
        .checkout-main-grid { gap: 1.25rem; }
        .checkout-card { padding: 1rem; }
        .form-group-lg input { padding: 0.85rem 0.75rem; font-size: 0.95rem; border-radius: 0.75rem; }
        .s-item-row { gap: 0.75rem; }
        .s-item-img { width: 56px; height: 56px; }
        .s-item-name { font-size: 0.85rem; }
    }
</style>
<script>
    $(document).ready(function() {
        $('#main-checkout-form').on('submit', function(e) {
            e.preventDefault();
            
            const $btn = $('.btn-complete-order');
            const originalHtml = $btn.html();
            
            $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Processing Order...').prop('disabled', true);
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        window.location.href = response.redirect_url;
                    } else {
                        alert(response.message || 'Something went wrong.');
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    let msg = 'Something went wrong. Please try again.';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                    $btn.html(originalHtml).prop('disabled', false);
                }
            });
        });

        // Payment option toggle
        $('.pay-option').on('click', function() {
            $('.pay-option').removeClass('active');
            $(this).addClass('active');
            $(this).find('input[type="radio"]').prop('checked', true);
        });
    });
</script>
@endsection
