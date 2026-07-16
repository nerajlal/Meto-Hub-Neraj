@extends('layouts.storefront')

@section('title', 'Shopping Cart | VESPR Perfumes')

@section('content')
<div class="cart-page-inner">
    <div class="cart-header-lg">
        <h1 class="cart-title-lg">Shopping Bag</h1>
        <p class="cart-subtitle">Review your selection and proceed to checkout.</p>
    </div>

    @if(count($cart) > 0)
    <div class="cart-main-grid">
        <!-- List -->
        <div class="cart-items-list">
            @foreach($cart as $id => $item)
            <div class="cart-item-card" id="item-{{ $id }}">
                <div class="item-visual">
                    @php 
                        $itemImg = $item['image'] ?? 'Images/g-load.webp';
                        if (!$itemImg || $itemImg == '') {
                            $itemImg = 'Images/g-load.webp';
                        }
                    @endphp
                    <img src="{{ asset($itemImg) }}" alt="{{ $item['name'] }}" onerror="this.src='{{ asset('Images/g-load.webp') }}'">
                </div>
                <div class="item-info-lg">
                    <div class="item-top-row">
                        <div>
                            <h3 class="item-name-lg">{{ $item['name'] }}</h3>
                            <span class="item-variant-lg">{{ $item['type'] == 'product' ? 'Size: ' . $item['size'] : 'Bundle' }}</span>
                        </div>
                        <button class="item-remove-btn" onclick="removeCartItem('{{ $id }}')">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    
                    <div class="item-bottom-row">
                        <div class="item-qty-control-lg">
                            <button onclick="updateCartQty('{{ $id }}', -1)">-</button>
                            <span id="qty-{{ $id }}">{{ $item['quantity'] }}</span>
                            <button onclick="updateCartQty('{{ $id }}', 1)">+</button>
                        </div>
                        <span class="item-price-lg" id="price-{{ $id }}">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>

                    @if((isset($item['min_order_qty']) && $item['min_order_qty']) || (isset($item['max_order_qty']) && $item['max_order_qty']))
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem; text-align: left;">
                        @if(isset($item['min_order_qty']) && $item['min_order_qty'])
                            <span class="me-2"><i class="fa-solid fa-arrow-down-1-9"></i> Min: {{ $item['min_order_qty'] }}</span>
                        @endif
                        @if(isset($item['max_order_qty']) && $item['max_order_qty'])
                            <span><i class="fa-solid fa-arrow-up-9-1"></i> Max: {{ $item['max_order_qty'] }}</span>
                        @endif
                    </div>
                    @endif

                    @if(isset($item['coupon']) && $item['coupon'])
                    <div class="item-promo-badge">
                        <i class="fa-solid fa-gift"></i> {{ $item['coupon']['code'] ?? 'DISCOUNT' }} Applied
                    </div>
                    @endif

                    @if(isset($item['pack_offer_applied']) && $item['pack_offer_applied'])
                    <div class="item-promo-badge" style="background: #eff6ff; color: #3b82f6;">
                        <i class="fa-solid fa-tags"></i> {{ $item['pack_offer_text'] }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div class="cart-sidebar-summary">
            <div class="summary-card-lg">
                <h3 class="summary-heading">Order Total</h3>
                <div class="summary-row-lg">
                    <span>Subtotal</span>
                    <span id="subtotal-val">₹{{ number_format($cartTotalBeforeTax, 2) }}</span>
                </div>
                @if($taxRate)
                <div class="summary-row-lg">
                    <span>{{ $taxName }} ({{ $taxRate }}%)</span>
                    <span id="tax-val">₹{{ number_format($taxAmount, 2) }}</span>
                </div>
                @endif
                <div class="summary-row-lg">
                    <span>Shipping</span>
                    <span class="free-badge">FREE</span>
                </div>
                <div class="summary-row-lg" id="savings-row" style="{{ $savings > 0 ? '' : 'display: none;' }}">
                    <span>Volume Discount</span>
                    <span style="color: #10b981; font-weight: 700;">-₹<span id="savings-val">{{ number_format($savings, 2) }}</span></span>
                </div>
                <hr class="summary-hr">
                <div class="summary-row-lg grand-total">
                    <span>Total</span>
                    <span id="total-val" data-val="{{ $total }}">₹{{ number_format($total, 2) }}</span>
                </div>

                @if(isset($minOrderValue) && $minOrderValue > 0)
                <div class="min-order-progress-container mt-3 mb-2">
                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem; color: #E2E8F0;">
                        <span id="min-order-msg">Calculating...</span>
                        <span>₹{{ number_format($minOrderValue, 2) }}</span>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 999px; background: rgba(255,255,255,0.15);">
                        <div id="min-order-bar" class="progress-bar" role="progressbar" style="width: 0%; background: var(--accent-color); border-radius: 999px; transition: width 0.3s ease;"></div>
                    </div>
                </div>
                @endif

                <input type="hidden" id="min-order-value" value="{{ $minOrderValue ?? 0 }}">
                <a href="{{ route('v1.checkout') }}" id="checkout-btn" class="btn-checkout-lg">
                    Checkout Now <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
                <div class="summary-trust">
                    <i class="fa-solid fa-shield-halved"></i> Secure checkout with VESPR
                </div>
            </div>
            
            <div class="help-card">
                <h4>Need help?</h4>
                <p>Contact our support team at support@task19.com or call +91 98765 43210</p>
            </div>
        </div>
    </div>
    @else
    <div class="empty-cart-state">
        <div class="empty-icon-box">
            <i class="fa-solid fa-bag-shopping"></i>
        </div>
        <h2>Your bag is empty</h2>
        <p>Looks like you haven't added any luxury scents to your collection yet.</p>
        <a href="{{ route('v1.all-products') }}" class="btn-primary" style="margin-top: 2rem;">Discover Fragrances</a>
    </div>
    @endif
</div>

<style>
    .cart-page-inner { padding: 1rem 0; }
    .cart-header-lg { margin-bottom: 3.5rem; }
    .cart-title-lg { font-size: 2.75rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.5rem; }
    .cart-subtitle { color: var(--text-muted); font-size: 1.1rem; }

    .cart-main-grid { display: grid; grid-template-columns: 1fr 380px; gap: 4rem; align-items: start; }

    .cart-item-card { display: flex; gap: 2rem; padding: 2rem; background: #fff; border: 1px solid var(--border-color); border-radius: 1.5rem; margin-bottom: 1.5rem; transition: var(--transition); }
    .cart-item-card:hover { border-color: var(--accent-color); box-shadow: var(--shadow-sm); }
    
    .item-visual { width: 140px; height: 140px; border-radius: 1rem; overflow: hidden; background: var(--section-bg); flex-shrink: 0; }
    .item-visual img { width: 100%; height: 100%; object-fit: cover; }

    .item-info-lg { flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
    .item-top-row { display: flex; justify-content: space-between; align-items: flex-start; }
    .item-name-lg { font-size: 1.25rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.25rem; }
    .item-variant-lg { font-size: 0.9rem; color: var(--text-muted); font-weight: 500; }
    .item-remove-btn { background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer; transition: var(--transition); padding: 0.5rem; display: flex; align-items: center; justify-content: center; }
    .item-remove-btn:hover { color: #ef4444; transform: rotate(90deg); }

    .item-bottom-row { display: flex; justify-content: space-between; align-items: center; }
    .item-qty-control-lg { display: flex; align-items: center; background: var(--section-bg); border-radius: 9999px; padding: 0.4rem 1rem; gap: 1.5rem; }
    .item-qty-control-lg button { border: none; background: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer; }
    .item-qty-control-lg span { font-weight: 700; min-width: 20px; text-align: center; }
    .item-price-lg { font-size: 1.35rem; font-weight: 800; color: var(--primary-color); }

    .item-promo-badge { align-self: flex-start; margin-top: 1rem; font-size: 0.8rem; font-weight: 700; color: #10b981; background: #ecfdf5; padding: 0.4rem 0.8rem; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }

    .summary-card-lg { background: var(--primary-color); color: #fff; padding: 2.5rem; border-radius: 2rem; position: sticky; top: 7rem; }
    .summary-heading { font-size: 1.5rem; font-weight: 700; margin-bottom: 2rem; color: var(--accent-color); }
    .summary-row-lg { display: flex; justify-content: space-between; margin-bottom: 1.25rem; font-size: 1.05rem; color: #94A3B8; }
    .free-badge { color: #10b981; font-weight: 800; }
    .summary-hr { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 1.5rem 0; }
    .grand-total { font-size: 1.75rem; font-weight: 800; color: #fff; }
    
    .btn-checkout-lg { display: block; width: 100%; background: var(--accent-color); color: var(--primary-color); text-align: center; padding: 1.25rem; border-radius: 9999px; font-weight: 800; font-size: 1.15rem; margin-top: 2.5rem; transition: var(--transition); }
    .btn-checkout-lg:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); background: #fff; }
    
    .summary-trust { text-align: center; font-size: 0.85rem; color: #64748B; margin-top: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }

    .help-card { margin-top: 1.5rem; padding: 1.5rem; border: 1px solid var(--border-color); border-radius: 1.5rem; }
    .help-card h4 { margin-bottom: 0.5rem; }
    .help-card p { font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; }

    .empty-cart-state { text-align: center; padding: 8rem 2rem; background: var(--section-bg); border-radius: 3rem; }
    .empty-icon-box { width: 100px; height: 100px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; font-size: 2.5rem; color: var(--text-muted); box-shadow: var(--shadow-sm); }
    .empty-cart-state h2 { font-size: 2rem; font-weight: 800; margin-bottom: 1rem; }
    .empty-cart-state p { color: var(--text-muted); font-size: 1.1rem; }

    @media (max-width: 1024px) {
        .cart-title-lg { font-size: 2rem; }
        .cart-main-grid { grid-template-columns: 1fr; gap: 2rem; }
        .cart-sidebar-summary { order: 2; }
        
        .cart-item-card { padding: 1.25rem; gap: 1.25rem; }
        .item-visual { width: 100px; height: 100px; }
        .item-name-lg { font-size: 1.1rem; }
        .item-price-lg { font-size: 1.15rem; }
    }

    @media (max-width: 600px) {
        .cart-item-card { padding: 1rem; gap: 1rem; align-items: center; }
        .item-visual { width: 80px; height: 80px; border-radius: 0.75rem; }
        .item-name-lg { font-size: 0.95rem; }
        .item-price-lg { font-size: 1.1rem; }
        .item-qty-control-lg { gap: 0.75rem; padding: 0.3rem 0.75rem; font-size: 0.9rem; }
        .item-qty-control-lg button { font-size: 1rem; }
        .item-bottom-row { margin-top: 1rem; }
        .item-remove-btn { padding: 0.75rem; } /* Larger hit area for mobile */
    }
</style>

<script>
    function checkMinOrder(totalVal) {
        let minVal = parseFloat(document.getElementById('min-order-value').value);
        let btn = document.getElementById('checkout-btn');
        let bar = document.getElementById('min-order-bar');
        let msg = document.getElementById('min-order-msg');
        
        if (minVal > 0) {
            let percentage = (totalVal / minVal) * 100;
            if (percentage > 100) percentage = 100;
            
            if (bar) bar.style.width = percentage + '%';
            
            if (totalVal < minVal) {
                let diff = minVal - totalVal;
                if (msg) msg.innerHTML = `Add <strong>₹${new Intl.NumberFormat().format(diff)}</strong> more to checkout`;
                btn.style.opacity = '0.5';
                btn.style.pointerEvents = 'none';
                if (bar) bar.style.background = 'var(--accent-color)';
            } else {
                if (msg) msg.innerHTML = `<strong>Minimum reached! 🎉</strong>`;
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
                if (bar) bar.style.background = '#10b981'; // Green when complete
            }
        }
    }

    // Run on initial load
    document.addEventListener("DOMContentLoaded", function() {
        let initialTotal = parseFloat(document.getElementById('total-val').getAttribute('data-val') || 0);
        checkMinOrder(initialTotal);
    });

    function updateCartQty(id, delta) {
        let qtyEl = document.getElementById('qty-' + id);
        let currentQty = parseInt(qtyEl.innerText);
        let newQty = Math.max(1, currentQty + delta);
        
        if (newQty === currentQty) return;

        $.ajax({
            url: "{{ route('cart.update') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                quantity: newQty
            },
            success: function(response) {
                qtyEl.innerText = newQty;
                document.getElementById('price-' + id).innerText = '₹' + new Intl.NumberFormat().format(response.itemTotal);
                document.getElementById('subtotal-val').innerText = '₹' + new Intl.NumberFormat().format(response.cartTotalBeforeTax);
                if (document.getElementById('tax-val')) {
                    document.getElementById('tax-val').innerText = '₹' + new Intl.NumberFormat().format(response.taxAmount);
                }
                document.getElementById('total-val').innerText = '₹' + new Intl.NumberFormat().format(response.cartTotal);
                $('#cart-count').text(response.cartCount);
                
                if (response.savings > 0) {
                    document.getElementById('savings-row').style.display = 'flex';
                    document.getElementById('savings-val').innerText = new Intl.NumberFormat().format(response.savings);
                } else {
                    document.getElementById('savings-row').style.display = 'none';
                }
                
                checkMinOrder(response.cartTotal);
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                } else {
                    alert('Error updating cart');
                }
            }
        });
    }

    function removeCartItem(id) {
        if (!confirm('Remove this item from your bag?')) return;

        $.ajax({
            url: "{{ route('cart.remove') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            success: function(response) {
                if (response.isEmpty) {
                    location.reload();
                } else {
                    document.getElementById('item-' + id).style.opacity = '0';
                    setTimeout(() => {
                        document.getElementById('item-' + id).remove();
                        document.getElementById('subtotal-val').innerText = '₹' + new Intl.NumberFormat().format(response.cartTotalBeforeTax);
                        if (document.getElementById('tax-val')) {
                            document.getElementById('tax-val').innerText = '₹' + new Intl.NumberFormat().format(response.taxAmount);
                        }
                        document.getElementById('total-val').innerText = '₹' + new Intl.NumberFormat().format(response.cartTotal);
                        $('#cart-count').text(response.cartCount);
                        checkMinOrder(response.cartTotal);
                    }, 300);
                }
            }
        });
    }
</script>
@endsection
