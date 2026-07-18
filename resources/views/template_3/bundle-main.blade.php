@extends('template_3.layouts.app')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <!-- Breadcrumbs -->
    <div class="breadcrumb">
        <a href="{{ route('v1.home') }}"><i class="fa-solid fa-house"></i> Home</a> 
        <span>/</span> 
        <a href="{{ route('v1.combos') }}">Weekly Combos</a>
        <span>/</span> 
        <span style="color: var(--text-main); font-weight: 600;">{{ $bundle->title }}</span>
    </div>

    <div class="product-detail-layout">
        <!-- Left: Image Gallery -->
        <div class="product-gallery">
            <div class="main-image-container" style="position: relative;">
                <span style="position: absolute; top: 1rem; left: 1rem; background: var(--accent-color); color: white; padding: 0.25rem 0.75rem; border-radius: 99px; font-weight: 700; font-size: 0.8rem; z-index: 10;">
                    {{ $bundle->type == 'pack' ? 'COMBO' : 'BUNDLE' }}
                </span>
                
                @php
                    $mainImage = $bundle->image ? asset('storage/' . $bundle->image) : asset('Images/default.png');
                    if (!$bundle->image && $bundle->type == 'pack' && $bundle->products->first()) {
                        $mainImage = $bundle->products->first()->main_image_url;
                    }
                @endphp
                <img id="mainImage" class="main-image" src="{{ $mainImage }}" alt="{{ $bundle->title }}">
            </div>
            
            <div class="thumbnails">
                <img src="{{ $mainImage }}" class="active" onclick="changeMainImage(this, '{{ $mainImage }}')">
                @foreach($bundle->products as $product)
                    @if($product->main_image_url)
                    <img src="{{ $product->main_image_url }}" onclick="changeMainImage(this, '{{ $product->main_image_url }}')">
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Right: Bundle Details -->
        <div class="product-info">
            <h1>{{ $bundle->title }}</h1>
            
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 0.5rem;">
                Combo / {{ $bundle->products->count() }} Items Included
            </p>
            
            <div class="price-block">
                <span class="current-price">
                    ₹{{ number_format($bundle->total_price, 2) }}
                </span>
                @if($bundle->base_price > $bundle->total_price)
                    <span class="compare-price">
                        ₹{{ number_format($bundle->base_price, 2) }}
                    </span>
                    <span style="background: #EF4444; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem; font-weight: 700;">
                        Save ₹{{ number_format($bundle->base_price - $bundle->total_price, 2) }}
                    </span>
                @endif
            </div>

            <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: var(--border-radius-md); padding: 1.5rem; margin-bottom: 2rem;">
                <h4 style="margin-bottom: 1rem; font-size: 1.1rem; color: var(--primary-color);">Products Included</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($bundle->products as $product)
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <img src="{{ $product->main_image_url ?? asset('Images/default.png') }}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0;">
                        <div>
                            <div style="font-weight: 600; font-size: 0.95rem;">
                                @if(isset($product->pivot->quantity))
                                    <span style="color: var(--accent-color);">{{ $product->pivot->quantity }}x</span> 
                                @endif
                                {{ $product->title }}
                            </div>
                            @php
                                $variant = $product->pivot->product_variant_id 
                                    ? $product->variants->firstWhere('id', $product->pivot->product_variant_id) 
                                    : $product->variants->first();
                            @endphp
                            @if($variant && $variant->size)
                                <div style="font-size: 0.85rem; color: var(--text-muted);">Size: {{ $variant->size }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="product-actions">
                <div style="display: flex; align-items: center; justify-content: space-between; border: 1px solid var(--border-color); border-radius: var(--border-radius-md); padding: 0.5rem; width: 140px; background: #FFFFFF;">
                    <button onclick="decrementQty()" style="width: 36px; height: 36px; border-radius: 50%; background: #F1F5F9; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-main);"><i class="fa-solid fa-minus"></i></button>
                    <span id="product-qty" style="font-weight: 700; font-size: 1.25rem;">1</span>
                    <button onclick="incrementQty()" style="width: 36px; height: 36px; border-radius: 50%; background: #F1F5F9; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-main);"><i class="fa-solid fa-plus"></i></button>
                </div>
                
                <button class="add-to-cart-btn" onclick="addToCartMain()">
                    <i class="fa-solid fa-bag-shopping"></i> Add Combo to Cart
                </button>
            </div>
            
            @if($bundle->description)
            <div style="background: #F8FAFC; border-radius: var(--border-radius-lg); padding: 1.5rem; margin-top: 2rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Combo Description</h3>
                <div style="color: var(--text-muted); line-height: 1.8;">
                    {!! $bundle->description !!}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Related Combos Section -->
    @if(isset($relatedBundles) && $relatedBundles->count() > 0)
    <div style="margin-top: 4rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; color: var(--text-main);">
            You May Also Like
        </h2>
        <div class="bundle-grid">
            @foreach($relatedBundles as $relatedBundle)
                @include('template_1.partials.bundle_card', ['bundle' => $relatedBundle])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    let qty = 1;
    
    function changeMainImage(element, src) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumbnails img').forEach(img => img.classList.remove('active'));
        element.classList.add('active');
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
        const key = `bundle-{{ $bundle->id }}`;
        
        updateInlineCart(key, qty);
        
        const btn = document.querySelector('.add-to-cart-btn');
        const originalHtml = btn.innerHTML;
        
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Combo Added';
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            toggleCartSidebar();
        }, 1000);
    }
</script>
@endsection
