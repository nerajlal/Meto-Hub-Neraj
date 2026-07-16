@if(count($cart) > 0)
    <div class="n-cart-list">
        @foreach($cart as $key => $item)
            <div class="n-cart-item">
                <div class="n-item-img">
                    <img src="{{ asset($item['image'] ?? 'Images/placeholder-grocery.webp') }}" alt="{{ $item['name'] }}">
                </div>
                <div class="n-item-details">
                    <h4 class="n-item-name">{{ $item['name'] }}</h4>
                    <p class="n-item-meta">{{ $item['size'] ?? '' }}</p>
                    @if(isset($item['coupon']) && $item['coupon'])
                        <div style="font-size: 0.7rem; color: #10b981; font-weight: 700; margin-bottom: 0.5rem; background: #ecfdf5; display: inline-block; padding: 2px 6px; border-radius: 4px;">
                            <i class="fa-solid fa-tag"></i> {{ $item['coupon']['code'] ?? 'DISCOUNT' }} Applied
                        </div>
                    @endif
                    <div class="n-item-controls">
                        <div class="n-qty-wrap">
                            <button onclick="updateNCartQty('{{ $key }}', -1)">-</button>
                            <span>{{ $item['quantity'] }}</span>
                            <button onclick="updateNCartQty('{{ $key }}', 1)">+</button>
                        </div>
                        <span class="n-item-price">
                            @if(isset($item['line_savings']) && $item['line_savings'] > 0)
                                <span style="text-decoration: line-through; color: #94a3b8; font-size: 0.75rem; margin-right: 4px;">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            @endif
                            ₹{{ number_format(($item['price'] * $item['quantity']) - ($item['line_savings'] ?? 0), 2) }}
                        </span>
                    </div>
                    @if((isset($item['min_order_qty']) && $item['min_order_qty']) || (isset($item['max_order_qty']) && $item['max_order_qty']))
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.4rem;">
                        @if(isset($item['min_order_qty']) && $item['min_order_qty'])
                            <span style="margin-right: 8px;"><i class="fa-solid fa-arrow-down-1-9"></i> Min: {{ $item['min_order_qty'] }}</span>
                        @endif
                        @if(isset($item['max_order_qty']) && $item['max_order_qty'])
                            <span><i class="fa-solid fa-arrow-up-9-1"></i> Max: {{ $item['max_order_qty'] }}</span>
                        @endif
                    </div>
                    @endif
                </div>
                <button class="n-item-remove" onclick="removeNCartItem('{{ $key }}')">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        @endforeach
    </div>

    <div class="n-cart-summary">
        <div class="n-summary-line">
            <span>Subtotal</span>
            <span>₹{{ number_format($cartTotalBeforeTax, 2) }}</span>
        </div>
        @if($taxRate)
            <div class="n-summary-line">
                <span>{{ $taxName }} ({{ $taxRate }}%)</span>
                <span>₹{{ number_format($taxAmount, 2) }}</span>
            </div>
        @endif
        @if($savings > 0)
            <div class="n-summary-line savings">
                <span>Total Savings</span>
                <span>-₹{{ number_format($savings, 2) }}</span>
            </div>
        @endif
        <div class="n-summary-line grand-total">
            <span>Grand Total</span>
            <span>₹{{ number_format($total, 2) }}</span>
        </div>
        
        @if(isset($minOrderValue) && $minOrderValue > 0)
        @php
            $percentage = min(100, ($total / $minOrderValue) * 100);
            $diff = $minOrderValue - $total;
        @endphp
        <div class="n-min-order-container" style="margin-top: 1rem;">
            <div class="n-summary-line" style="margin-bottom: 0.2rem; font-size: 0.8rem;">
                @if($diff > 0)
                    <span>Add <strong>₹{{ number_format($diff, 2) }}</strong> more to checkout</span>
                @else
                    <span><strong>Minimum reached! 🎉</strong></span>
                @endif
                <span>₹{{ number_format($minOrderValue, 0) }}</span>
            </div>
            <div style="height: 6px; border-radius: 999px; background: #e2e8f0;">
                <div style="width: {{ $percentage }}%; background: {{ $diff > 0 ? 'var(--accent-color)' : '#10b981' }}; height: 100%; border-radius: 999px; transition: width 0.3s ease;"></div>
            </div>
        </div>
        
            @if($total < $minOrderValue)
                <a href="javascript:void(0)" class="n-checkout-btn" style="opacity: 0.5; cursor: not-allowed; pointer-events: none;">PROCEED TO CHECKOUT</a>
            @else
                <a href="{{ route('v3.checkout') }}" class="n-checkout-btn">PROCEED TO CHECKOUT</a>
            @endif
        @else
            <a href="{{ route('v3.checkout') }}" class="n-checkout-btn">PROCEED TO CHECKOUT</a>
        @endif
    </div>
@else
    <div class="n-empty-cart">
        <i class="fa-solid fa-cart-shopping"></i>
        <h3>Your cart is empty</h3>
        <p>Looks like you haven't added anything to your cart yet.</p>
        <a href="{{ route('v3.all-products') }}" class="n-shop-btn">Continue Shopping</a>
    </div>
@endif

<style>
    .n-cart-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .n-cart-item {
        display: flex;
        gap: 1rem;
        position: relative;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .n-item-img {
        width: 70px;
        height: 70px;
        background: #f8f8f8;
        border-radius: 0.75rem;
        overflow: hidden;
        flex-shrink: 0;
    }

    .n-item-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .n-item-details {
        flex-grow: 1;
    }

    .n-item-name {
        font-size: 0.9rem;
        font-weight: 700;
        margin: 0 0 0.2rem 0;
        color: var(--primary-color);
        max-width: 180px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .n-item-meta {
        font-size: 0.75rem;
        color: #888;
        margin-bottom: 0.5rem;
    }

    .n-item-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .n-qty-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: #f5f5f5;
        padding: 0.2rem 0.5rem;
        border-radius: 0.5rem;
    }

    .n-qty-wrap button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        color: var(--primary-color);
        font-weight: 800;
    }

    .n-qty-wrap span {
        font-size: 0.85rem;
        font-weight: 700;
        min-width: 15px;
        text-align: center;
    }

    .n-item-price {
        font-weight: 800;
        color: var(--accent-color);
        font-size: 0.95rem;
    }

    .n-item-remove {
        background: none;
        border: none;
        color: #ff4d4d;
        cursor: pointer;
        font-size: 0.9rem;
        padding: 0.5rem;
        opacity: 0.6;
        transition: opacity 0.2s;
    }

    .n-item-remove:hover {
        opacity: 1;
    }

    .n-cart-summary {
        margin-top: 2rem;
        background: #f9f9f9;
        padding: 1.5rem;
        border-radius: 1rem;
    }

    .n-summary-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        color: #666;
    }

    .n-summary-line.savings {
        color: #10B981;
        font-weight: 600;
    }

    .n-summary-line.grand-total {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #ddd;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--primary-color);
    }

    .n-checkout-btn {
        display: block;
        width: 100%;
        background: var(--accent-color);
        color: #fff;
        text-align: center;
        padding: 1rem;
        border-radius: 0.75rem;
        text-decoration: none;
        font-weight: 800;
        margin-top: 1.5rem;
        font-size: 0.9rem;
    }

    .n-empty-cart {
        text-align: center;
        padding: 3rem 1rem;
    }

    .n-empty-cart i {
        font-size: 3rem;
        color: #eee;
        margin-bottom: 1rem;
    }

    .n-shop-btn {
        display: inline-block;
        background: var(--primary-color);
        color: #fff;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        text-decoration: none;
        font-weight: 700;
        margin-top: 1rem;
    }
</style>
