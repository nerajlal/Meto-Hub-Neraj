@extends('template_2.layouts.app')

@section('title', 'Shopping Cart | ' . ($currentTenant->name ?? 'Fresh Grocery'))

@section('content')
<div class="cart-page-inner">
    <div class="cart-header-lg" style="margin-bottom: 2rem;">
        <h1 class="cart-title-lg" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color);">Shopping Bag</h1>
        <p class="cart-subtitle" style="color: var(--text-muted);">Review your selection and proceed to checkout.</p>
    </div>

    @if(count($cart) > 0)
    <div class="cart-main-grid" style="display: grid; grid-template-columns: 1fr 380px; gap: 3rem; align-items: start;">
        <!-- List -->
        <div class="cart-items-list">
            @foreach($cart as $id => $item)
            <div class="cart-item-card" id="item-{{ $id }}" style="display: flex; gap: 1.5rem; padding: 1.5rem; background: #fff; border: 1px solid var(--border-color); border-radius: 1.25rem; margin-bottom: 1rem; transition: 0.2s;">
                <div class="item-visual" style="width: 100px; height: 100px; border-radius: 0.75rem; overflow: hidden; background: #f8fafc; flex-shrink: 0;">
                    @php 
                        $itemImg = $item['image'] ?? 'Images/placeholder-grocery.webp';
                        if (!$itemImg || $itemImg == '') {
                            $itemImg = 'Images/placeholder-grocery.webp';
                        }
                    @endphp
                    <img src="{{ asset($itemImg) }}" alt="{{ $item['name'] }}" onerror="this.src='{{ asset('Images/placeholder-grocery.webp') }}'" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="item-info-lg" style="flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div class="item-top-row" style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h3 class="item-name-lg" style="font-size: 1.15rem; font-weight: 700; color: var(--primary-color); margin: 0 0 0.25rem 0;">{{ $item['name'] }}</h3>
                            <span class="item-variant-lg" style="font-size: 0.85rem; color: var(--text-muted);">{{ $item['type'] == 'product' ? 'Option: ' . $item['size'] : 'Bundle' }}</span>
                        </div>
                        <button class="item-remove-btn" onclick="removeCartItem('{{ $id }}')" style="background: none; border: none; font-size: 1.2rem; color: var(--text-muted); cursor: pointer; transition: 0.2s; padding: 0.25rem;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    
                    <div class="item-bottom-row" style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                        <div class="item-qty-control-lg" style="display: flex; align-items: center; background: #f1f5f9; border-radius: 99px; padding: 0.35rem 0.85rem; gap: 1rem;">
                            <button onclick="updateCartQty('{{ $id }}', -1)" style="border: none; background: none; font-weight: 700; cursor: pointer;">-</button>
                            <span id="qty-{{ $id }}" style="font-weight: 700; min-width: 15px; text-align: center;">{{ $item['quantity'] }}</span>
                            <button onclick="updateCartQty('{{ $id }}', 1)" style="border: none; background: none; font-weight: 700; cursor: pointer;">+</button>
                        </div>
                        <span class="item-price-lg" id="price-{{ $id }}" style="font-size: 1.25rem; font-weight: 800; color: var(--accent-color);">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>

                    @if(isset($item['coupon']))
                    <div class="item-promo-badge" style="align-self: flex-start; margin-top: 0.5rem; font-size: 0.75rem; font-weight: 700; color: #10b981; background: #ecfdf5; padding: 2px 8px; border-radius: 4px;">
                        <i class="fa-solid fa-gift"></i> {{ $item['coupon']->code }} Applied
                    </div>
                    @endif

                    @if(isset($item['pack_offer_applied']) && $item['pack_offer_applied'])
                    <div class="item-promo-badge" style="align-self: flex-start; margin-top: 0.5rem; font-size: 0.75rem; font-weight: 700; color: #3b82f6; background: #eff6ff; padding: 2px 8px; border-radius: 4px;">
                        <i class="fa-solid fa-tags"></i> {{ $item['pack_offer_text'] }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div class="cart-sidebar-summary">
            <div class="summary-card-lg" style="background: var(--primary-color); color: #fff; padding: 2rem; border-radius: 1.5rem;">
                <h3 class="summary-heading" style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.5rem; color: var(--accent-color);">Order Total</h3>
                <div class="summary-row-lg" style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: #94a3b8;">
                    <span>Subtotal</span>
                    <span id="subtotal-val">₹{{ number_format($cartTotalBeforeTax, 2) }}</span>
                </div>
                @if($taxRate)
                <div class="summary-row-lg" style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: #94a3b8;">
                    <span>{{ $taxName }} ({{ $taxRate }}%)</span>
                    <span id="tax-val">₹{{ number_format($taxAmount, 2) }}</span>
                </div>
                @endif
                <div class="summary-row-lg" style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: #94a3b8;">
                    <span>Shipping</span>
                    <span class="free-badge" style="color: #10b981; font-weight: 800;">FREE</span>
                </div>
                <div class="summary-row-lg" id="savings-row" style="{{ $savings > 0 ? 'display: flex;' : 'display: none;' }} justify-content: space-between; margin-bottom: 1rem; color: #94a3b8;">
                    <span>Volume Discount</span>
                    <span style="color: #10b981; font-weight: 700;">-₹<span id="savings-val">{{ number_format($savings, 2) }}</span></span>
                </div>
                <hr class="summary-hr" style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 1rem 0;">
                <div class="summary-row-lg grand-total" style="display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: 800; color: #fff;">
                    <span>Total</span>
                    <span id="total-val">₹{{ number_format($total, 2) }}</span>
                </div>
                <a href="{{ route('v3.checkout') }}" class="btn-checkout-lg" style="display: block; width: 100%; background: var(--accent-color); color: #fff; text-align: center; padding: 1rem; border-radius: 99px; font-weight: 800; font-size: 1.1rem; text-decoration: none; margin-top: 2rem; border: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15); transition: 0.2s;">
                    Checkout Now <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
                <div class="summary-trust" style="text-align: center; font-size: 0.8rem; color: #64748b; margin-top: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fa-solid fa-shield-halved"></i> Secure checkout with {{ $currentTenant->name ?? 'Fresh Grocery' }}
                </div>
            </div>
            
            <div class="help-card" style="margin-top: 1.5rem; padding: 1.5rem; border: 1px solid var(--border-color); border-radius: 1rem; background: #fff;">
                <h4 style="margin: 0 0 0.5rem 0; font-size: 1rem; font-weight: 700;">Need help?</h4>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Contact our support team at {{ $currentTenant->contact_email ?? 'support@task19.com' }} or call {{ $currentTenant->contact_phone ?? '+91 98765 43210' }}</p>
            </div>
        </div>
    </div>
    @else
    <div class="empty-cart-state" style="text-align: center; padding: 6rem 2rem; background: #fff; border-radius: 2rem; border: 1px solid var(--border-color);">
        <div class="empty-icon-box" style="width: 80px; height: 80px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2.2rem; color: var(--text-muted);">
            <i class="fa-solid fa-bag-shopping"></i>
        </div>
        <h2 style="font-weight: 800; font-size: 1.75rem; margin-bottom: 0.5rem;">Your bag is empty</h2>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Looks like you haven't added any fresh groceries to your basket yet.</p>
        <a href="{{ route('v3.all-products') }}" class="btn-primary" style="background: var(--accent-color); color: #fff; padding: 0.75rem 2rem; border-radius: 99px; text-decoration: none; font-weight: 700; display: inline-block;">Discover Groceries</a>
    </div>
    @endif
</div>

<style>
    .btn-checkout-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3) !important;
    }
</style>

<script>
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
                    }, 300);
                }
            }
        });
    }
</script>
@endsection
