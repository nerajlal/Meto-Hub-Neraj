@extends('template_3.layouts.app')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <!-- Breadcrumbs -->
    <div class="breadcrumb">
        <a href="{{ route('v3.home') }}"><i class="fa-solid fa-house"></i> Home</a> 
        <span>/</span> 
        <a href="{{ route('v3.all-products') }}">Shop</a>
        @if($product->collection)
            <span>/</span> 
            <a href="{{ route('v3.collection', ['slug' => $product->collection->slug]) }}">{{ $product->collection->name }}</a>
        @endif
        <span>/</span> 
        <span style="color: var(--text-main); font-weight: 600;">{{ $product->title }}</span>
    </div>

    <div class="product-detail-layout">
        <!-- Left: Image Gallery -->
        <div class="product-gallery">
            @php 
                $isWishlisted = auth()->check() && \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists();
            @endphp
            
            <div class="main-image-container" style="position: relative;">
                <button class="wishlist-btn {{ $isWishlisted ? 'active' : '' }}" onclick="toggleWishlist(event, {{ $product->id }})" style="top: 1rem; right: 1rem; width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="{{ $isWishlisted ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                </button>
                <img id="mainImage" class="main-image" src="{{ $product->main_image_url ?? asset('Images/placeholder-grocery.webp') }}" alt="{{ $product->title }}">
            </div>
            
            @if($product->images && $product->images->count() > 1)
            <div class="thumbnails">
                <img src="{{ $product->main_image_url }}" class="active" onclick="changeMainImage(this, '{{ $product->main_image_url }}')">
                @foreach($product->images as $img)
                <img src="{{ asset('storage/' . $img->path) }}" onclick="changeMainImage(this, '{{ asset('storage/' . $img->path) }}')">
                @endforeach
            </div>
            @endif
        </div>

        <!-- Right: Product Details -->
        <div class="product-info">
            @if(!empty($product->tags) && count($product->tags) > 0)
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                    @foreach($product->tags as $tag)
                        <span style="background: rgba(16, 185, 129, 0.1); color: var(--accent-color); padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.8rem; font-weight: 700;">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif
            
            <h1>{{ $product->title }}</h1>
            
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 0.5rem;">
                {{ $product->type ?? 'Grocery' }}
            </p>
            
            <div class="price-block">
                <span class="current-price">
                    ₹{{ number_format($product->starting_price, 2) }}
                </span>
                @if($product->compare_at_price > $product->starting_price)
                    <span class="compare-price">
                        ₹{{ number_format($product->compare_at_price, 2) }}
                    </span>
                    <span style="background: #EF4444; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem; font-weight: 700;">
                        {{ round((($product->compare_at_price - $product->starting_price) / $product->compare_at_price) * 100) }}% OFF
                    </span>
                @endif
            </div>

            @if($product->variants && $product->variants->count() > 0)
            <div class="variant-selector">
                <h4>Select Size / Weight</h4>
                <div class="variant-options">
                    @foreach($product->variants as $index => $variant)
                        <button class="variant-btn {{ $index === 0 ? 'active' : '' }}" onclick="selectVariant(this, '{{ $variant->id }}', '{{ $variant->size }}')">
                            {{ $variant->size }}
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="product-actions">
                <div style="display: flex; align-items: center; justify-content: space-between; border: 1px solid var(--border-color); border-radius: var(--border-radius-md); padding: 0.5rem; width: 140px; background: #FFFFFF;">
                    <button onclick="decrementQty()" style="width: 36px; height: 36px; border-radius: 50%; background: #F1F5F9; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-main);"><i class="fa-solid fa-minus"></i></button>
                    <span id="product-qty" style="font-weight: 700; font-size: 1.25rem;">1</span>
                    <button onclick="incrementQty()" style="width: 36px; height: 36px; border-radius: 50%; background: #F1F5F9; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-main);"><i class="fa-solid fa-plus"></i></button>
                </div>
                
                <button class="add-to-cart-btn" onclick="addToCartMain()">
                    <i class="fa-solid fa-bag-shopping"></i> Add to Cart
                </button>
            </div>
            
            @if(isset($product->volume_pricing) && (is_array($product->volume_pricing) ? count($product->volume_pricing) > 0 : $product->volume_pricing->count() > 0))
            <div class="volume-deals">
                <h4 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-boxes-stacked" style="color: var(--accent-color);"></i> Pack Deals</h4>
                @foreach($product->volume_pricing as $deal)
                <div class="volume-deal-item">
                    <div>
                        <span style="font-weight: 700; color: var(--primary-color);">Buy {{ $deal->quantity }} or more</span>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">Get special wholesale pricing</div>
                    </div>
                    <div style="font-weight: 800; color: var(--accent-color); font-size: 1.1rem;">
                        ₹{{ number_format($deal->price, 2) }} / ea
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            
            <div style="background: #F8FAFC; border-radius: var(--border-radius-lg); padding: 1.5rem; margin-top: 2rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Product Description</h3>
                <div style="color: var(--text-muted); line-height: 1.8;">
                    {!! $product->description !!}
                </div>
            </div>
        </div>
    </div>
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
    
    function selectVariant(element, id, size) {
        document.querySelectorAll('.variant-btn').forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');
        selectedVariantId = id;
        selectedSize = size;
    }

    function incrementQty() {
        qty++;
        document.getElementById('product-qty').innerText = qty;
    }
    
    function decrementQty() {
        if (qty > 1) {
            qty--;
            document.getElementById('product-qty').innerText = qty;
        }
    }
    
    function addToCartMain() {
        const key = `{{ $product->id }}${selectedSize ? '-' + selectedSize : ''}`;
        
        updateInlineCart(key, qty);
        
        const btn = document.querySelector('.add-to-cart-btn');
        const originalHtml = btn.innerHTML;
        
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Added';
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            toggleCartSidebar();
        }, 1000);
    }
</script>
@endsection
