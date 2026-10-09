@extends('template_3.layouts.app')

@section('content')
<style>
    .premium-badge {
        background: var(--primary-color); 
        color: #fff; 
        padding: 0.35rem 1rem; 
        border-radius: 4px; 
        font-size: 0.7rem; 
        font-weight: 800; 
        text-transform: uppercase; 
        letter-spacing: 0.15em;
    }
    .t3-premium-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary-color);
        letter-spacing: -0.5px;
        line-height: 1.1;
        margin-bottom: 0.5rem;
    }
    .t3-premium-type {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 2rem;
        display: block;
    }
    .t3-premium-price {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--primary-color);
    }
    .t3-variant-btn {
        background: #fff;
        border: 1px solid var(--border-color);
        color: var(--primary-color);
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .t3-variant-btn.active {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 1px var(--primary-color);
    }
    .t3-add-to-cart {
        background: var(--primary-color);
        color: #fff;
        border: none;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-weight: 800;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        flex-grow: 1;
    }
    .t3-add-to-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .t3-product-detail-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: start;
    }
    @media (max-width: 768px) {
        .t3-product-detail-layout {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        .container {
            padding-top: 1rem !important;
            padding-bottom: 3rem !important;
        }
        .t3-premium-title {
            font-size: 1.8rem;
        }
        .t3-premium-price {
            font-size: 1.8rem;
        }
        .product-actions {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #fff;
            padding: 1rem 1.25rem;
            box-shadow: 0 -4px 25px rgba(0,0,0,0.1);
            z-index: 999;
            margin: 0 !important;
            flex-direction: row !important;
            gap: 0.75rem !important;
            border-top: 1px solid var(--border-color);
            padding-bottom: env(safe-area-inset-bottom, 1rem); /* for iPhone safe area */
        }
        .product-actions > div {
            width: 120px !important;
            flex-shrink: 0;
        }
        .t3-add-to-cart {
            width: 100%;
            padding: 0.85rem !important;
            border-radius: 8px !important;
        }
        .main-image-container {
            padding: 1rem !important;
            margin-bottom: 1rem !important;
        }
    }
</style>
<div class="container" style="padding-top: 2rem; padding-bottom: 8rem;">
    <!-- Breadcrumbs -->
    <div class="breadcrumb" style="margin-bottom: 2rem; font-size: 0.85rem; color: #94a3b8; overflow-x: auto; white-space: nowrap; padding-bottom: 0.5rem;">
        <a href="{{ route('v1.home') }}" style="color: inherit;"><i class="fa-solid fa-house"></i> Home</a> 
        <span style="margin: 0 0.5rem;">/</span> 
        <a href="{{ route('v1.all-products') }}" style="color: inherit;">Shop</a>
        @if($product->collection)
            <span style="margin: 0 0.5rem;">/</span> 
            <a href="{{ route('v1.collection', ['slug' => $product->collection->slug]) }}" style="color: inherit;">{{ $product->collection->name }}</a>
        @endif
        <span style="margin: 0 0.5rem;">/</span> 
        <span style="color: var(--primary-color); font-weight: 700;">{{ $product->title }}</span>
    </div>

    <div class="t3-product-detail-layout">
        <!-- Left: Image Gallery -->
        <div class="product-gallery" style="position: sticky; top: 2rem;">
            @php 
                $isWishlisted = auth()->check() && \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists();
            @endphp
            
            <div class="main-image-container" style="position: relative; background: #f8fafc; border-radius: 1.5rem; overflow: hidden; padding: 2rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <button class="wishlist-btn {{ $isWishlisted ? 'active' : '' }}" onclick="toggleWishlist(event, {{ $product->id }})" style="position: absolute; top: 1.5rem; right: 1.5rem; width: 50px; height: 50px; border-radius: 50%; background: #fff; border: 1px solid var(--border-color); font-size: 1.25rem; color: {{ $isWishlisted ? '#ef4444' : 'var(--text-muted)' }}; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; z-index: 10;">
                    <i class="{{ $isWishlisted ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                </button>
                <img id="mainImage" class="main-image" src="{{ $product->main_image_url ?? asset('Images/default.png') }}" alt="{{ $product->title }}" style="width: 100%; height: auto; max-height: 600px; object-fit: contain; mix-blend-mode: multiply;">
            </div>
            
            @if($product->images && $product->images->count() > 1)
            <div class="thumbnails" style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 0.5rem;">
                <img src="{{ $product->main_image_url }}" class="active" onclick="changeMainImage(this, '{{ $product->main_image_url }}')" style="width: 80px; height: 80px; object-fit: cover; border-radius: 0.75rem; border: 2px solid var(--primary-color); cursor: pointer; padding: 0.25rem; background: #f8fafc;">
                @foreach($product->images as $img)
                <img src="{{ asset('storage/' . $img->path) }}" onclick="changeMainImage(this, '{{ asset('storage/' . $img->path) }}')" style="width: 80px; height: 80px; object-fit: cover; border-radius: 0.75rem; border: 2px solid transparent; cursor: pointer; padding: 0.25rem; background: #f8fafc;">
                @endforeach
            </div>
            <script>
                function changeMainImage(element, src) {
                    document.getElementById('mainImage').src = src;
                    document.querySelectorAll('.thumbnails img').forEach(img => {
                        img.style.borderColor = 'transparent';
                        img.classList.remove('active');
                    });
                    element.style.borderColor = 'var(--primary-color)';
                    element.classList.add('active');
                }
            </script>
            @endif
        </div>

        <!-- Right: Product Details -->
        <div class="product-info" style="padding-top: 1rem;">
            @if(!empty($product->tags) && count($product->tags) > 0)
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem;">
                    @foreach($product->tags as $tag)
                        <span class="premium-badge">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif
            
            <h1 class="t3-premium-title">{{ $product->title }}</h1>
            
            <span class="t3-premium-type">
                {{ $product->type ?? 'Product' }}
            </span>
            
            <div class="price-block" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 2rem;">
                <span class="t3-premium-price" id="t3-price-display">
                    {{ $currentTenant->currency ?? '₹' }}{{ number_format($product->discounted_price, 2) }}
                </span>
                @if($product->compare_at_price > $product->discounted_price)
                    <span class="compare-price" style="font-size: 1.25rem; text-decoration: line-through; color: #94a3b8; font-weight: 600;">
                        {{ $currentTenant->currency ?? '₹' }}{{ number_format($product->compare_at_price, 2) }}
                    </span>
                    <span style="background: #10B981; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                        Save {{ round((($product->compare_at_price - $product->discounted_price) / $product->compare_at_price) * 100) }}%
                    </span>
                @endif
            </div>

            @if($product->active_discount)
                <div style="margin-top: -1rem; margin-bottom: 2rem; font-size: 0.85rem; color: #10b981; font-weight: 700; background: #ecfdf5; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 8px;">
                    <i class="fa-solid fa-tag"></i> {{ $product->active_discount->code ?? 'DISCOUNT' }} Applied
                </div>
            @endif

            @if($product->variants && $product->variants->count() > 0)
            <div class="variant-selector" style="margin-bottom: 2.5rem;">
                <h4 style="font-size: 0.85rem; font-weight: 800; color: var(--primary-color); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1rem;">Select Size</h4>
                <div class="variant-options" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    @foreach($product->variants as $index => $variant)
                        <button class="t3-variant-btn {{ $index === 0 ? 'active' : '' }}" onclick="selectVariant(this, '{{ $variant->id ?? 0 }}', '{{ $variant->size }}', {{ $variant->price ?? 0 }}, {{ $variant->stock ?? 999 }}, {{ $product->continue_selling_when_out_of_stock ? 'true' : 'false' }})">
                            {{ $variant->size }}
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="product-actions" id="main-product-actions" data-product-id="{{ $product->id }}" style="display: flex; gap: 1rem; margin-bottom: 3rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; border: 1px solid var(--border-color); border-radius: 8px; padding: 0.25rem; width: 120px; background: #f8fafc;">
                    <button onclick="updateMainCart(-1)" style="width: 40px; height: 40px; border-radius: 6px; background: #fff; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-muted);"><i class="fa-solid fa-minus" style="font-size: 0.75rem;"></i></button>
                    <span id="main-qty-value" style="font-weight: 800; font-size: 1.2rem; color: var(--primary-color);">0</span>
                    <button onclick="updateMainCart(1)" style="width: 40px; height: 40px; border-radius: 6px; background: #fff; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-muted);"><i class="fa-solid fa-plus" style="font-size: 0.75rem;"></i></button>
                </div>
                
                <button class="t3-add-to-cart" id="add-to-cart-page-btn" onclick="addOrOpenCart()">
                    <i class="fa-solid fa-bag-shopping"></i> <span>Add to Cart</span>
                </button>
            </div>

            @if(isset($product->volume_pricing) && (is_array($product->volume_pricing) ? count($product->volume_pricing) > 0 : $product->volume_pricing->count() > 0))
            <div class="volume-deals" style="margin-bottom: 3rem;">
                <h4 style="font-size: 0.85rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1rem;"><i class="fa-solid fa-layer-group me-2"></i> Pack Deals</h4>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($product->volume_pricing as $deal)
                    <div class="volume-deal-item" style="display: flex; align-items: center; justify-content: space-between; background: #fff; border: 1px solid var(--border-color); padding: 1.25rem; border-radius: 8px;">
                        <div>
                            <span style="font-weight: 800; color: var(--primary-color); display: block; margin-bottom: 0.25rem;">Buy {{ $deal->quantity }} or more</span>
                            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Special wholesale pricing</div>
                        </div>
                        <div style="font-weight: 800; color: var(--primary-color); font-size: 1.2rem;">
                            {{ $currentTenant->currency ?? '₹' }}{{ number_format($deal->price, 2) }} <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">/ ea</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <div class="product-description-premium" style="border-top: 1px solid var(--border-color); padding-top: 2.5rem;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1.25rem; letter-spacing: -0.2px;">Product Details</h3>
                <div style="color: var(--text-main); line-height: 1.8; font-size: 0.95rem;">
                    {!! $product->description !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Recently Viewed Section -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div style="margin-top: 4rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; color: var(--text-main);">
            Recently Viewed
        </h2>
        <div class="product-grid">
            @foreach($relatedProducts as $relatedProduct)
                @include('template_3.partials.product_card', ['product' => $relatedProduct])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    let qty = 1;
    let selectedVariantId = '{{ $product->variants->first()->id ?? "" }}';
    let selectedSize = '{{ $product->variants->first()->size ?? "" }}';
    
    function changeMainImage(element, src) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumbnails img').forEach(img => img.classList.remove('active'));
        element.classList.add('active');
    }
    
    function selectVariant(element, id, size, price, stock, continueSelling) {
        document.querySelectorAll('.variant-btn').forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');
        selectedVariantId = id;
        selectedSize = size;
        
        // Update price display if passed
        if(price !== undefined) {
            const formattedPrice = new Intl.NumberFormat('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(price);
            const priceDisplay = document.getElementById('t3-price-display');
            if (priceDisplay) {
                priceDisplay.innerText = '{{ $currentTenant->currency ?? '₹' }}' + formattedPrice;
            }
        }
        
        // Handle out of stock state
        if(stock !== undefined && continueSelling !== undefined) {
            let isOut = stock <= 0 && !continueSelling;
            let btn = document.getElementById('add-to-cart-page-btn');
            if (isOut) {
                btn.innerHTML = 'OUT OF STOCK';
                btn.style.background = '#cbd5e1';
                btn.style.cursor = 'not-allowed';
                btn.disabled = true;
            } else {
                btn.innerHTML = 'Add to Cart';
                btn.style.background = 'var(--primary-color)';
                btn.style.cursor = 'pointer';
                btn.disabled = false;
            }
        }
        
        syncMainProductUI();
    }

    function getMainCartKey() {
        return "{{ $product->id }}" + (selectedSize ? '-' + selectedSize : '');
    }

    function syncMainProductUI() {
        if (!window.cartItemsMap) return;
        let key = getMainCartKey();
        let currentQty = parseInt(window.cartItemsMap[key] || 0);
        
        let qtyValue = document.getElementById('main-qty-value');
        let btn = document.getElementById('add-to-cart-page-btn');
        
        if (qtyValue) {
            qtyValue.innerText = currentQty;
        }
        
        if (btn && currentQty > 0) {
            btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>View Cart</span>';
            btn.style.background = '#10B981';
        } else if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-bag-shopping"></i> <span>Add to Cart</span>';
            btn.style.background = 'var(--accent-color)';
        }
    }

    const originalSyncCartUI = window.syncCartUI;
    window.syncCartUI = function(showToast) {
        if (typeof originalSyncCartUI === 'function') originalSyncCartUI(showToast);
        syncMainProductUI();
    };

    function updateMainCart(delta) {
        if (typeof window.updateInlineCart === 'function') {
            let key = getMainCartKey();
            window.updateInlineCart(key, delta);
        }
    }

    function addOrOpenCart() {
        let key = getMainCartKey();
        let currentQty = parseInt(window.cartItemsMap ? (window.cartItemsMap[key] || 0) : 0);
        
        if (currentQty > 0) {
            if (typeof toggleCartSidebar === 'function') toggleCartSidebar();
        } else {
            updateMainCart(1);
        }
    }

    setTimeout(syncMainProductUI, 500);
</script>
@endsection
