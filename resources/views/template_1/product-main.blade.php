@extends('template_1.layouts.app')

@section('title', $product->title . ' | ' . ($currentTenant->name ?? 'Fresh Grocery'))
@section('meta_description', Str::limit(strip_tags($product->description), 160))
@section('meta_keywords', $product->title . ', fresh, organic, grocery, order online')
@section('og_image', $product->main_image_url ?? asset('Images/placeholder-grocery.webp'))

@section('styles')
<script type="application/ld+json">
{
  "@@context": "https://schema.org/",
  "@@type": "Product",
  "name": "{{ $product->title }}",
  "image": [
    "{{ $product->main_image_url ?? asset('Images/placeholder-grocery.webp') }}"
  ],
  "description": "{{ strip_tags($product->description) }}",
  "brand": {
    "@@type": "Brand",
    "name": "{{ $currentTenant->name ?? 'Fresh Grocery' }}"
  },
  "offers": {
    "@@type": "Offer",
    "url": "{{ url()->current() }}",
    "priceCurrency": "INR",
    "price": "{{ $product->starting_price }}",
    "availability": "https://schema.org/InStock"
  }
}
</script>
<style>
    .product-page-container {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .main-image-display {
        transition: transform 0.3s ease;
    }
    .main-image-display:hover {
        transform: scale(1.02);
    }
    .size-rect {
        transition: all 0.2s ease;
    }
    .size-rect:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .deal-card {
        transition: all 0.2s ease;
    }
    .deal-card:hover {
        background: #f0fdf4;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .btn-add-to-cart, #buy-now-btn, .wishlist-toggle-btn {
        transition: all 0.2s ease;
    }
    .btn-add-to-cart:hover, #buy-now-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .wishlist-toggle-btn:hover {
        transform: scale(1.1);
    }

    @media (max-width: 768px) {
        .product-page-container {
            padding: 0 !important;
        }
        .breadcrumb {
            margin-bottom: 1rem !important;
            font-size: 0.75rem !important;
        }
        .product-core-grid {
            gap: 1rem !important;
            margin-bottom: 2rem !important;
        }
        .product-details-panel {
            padding: 1.25rem !important;
            border-radius: 1.5rem 1.5rem 0 0 !important;
            margin: 0 -1rem !important;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.08) !important;
            position: relative !important;
        }
        .p-title {
            font-size: 1.5rem !important;
            line-height: 1.3 !important;
            margin-bottom: 0.75rem !important;
        }
        .p-price-row {
            margin-bottom: 1.25rem !important;
            padding: 0.75rem 1rem !important;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
            border-radius: 1rem !important;
        }
        .p-current-price {
            font-size: 1.75rem !important;
        }
        .p-compare-at {
            font-size: 1rem !important;
        }
        .section-title {
            font-size: 1.25rem !important;
        }
        .size-rect-grid {
            gap: 0.75rem !important;
        }
        .size-rect {
            padding: 0.75rem 1rem !important;
            border-radius: 1rem !important;
            flex: 1 1 calc(50% - 0.5rem) !important;
            justify-content: space-between !important;
        }
        .p-volume-deals {
            margin-bottom: 1.5rem !important;
        }
        .deal-card {
            border-radius: 1rem !important;
        }
        .p-actions-row {
            flex-direction: row !important;
            gap: 0.75rem !important;
        }
        .page-qty {
            font-size: 1rem !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            padding: 0 0.75rem !important;
            min-width: 30px !important;
            text-align: center !important;
        }
        .btn-add-to-cart {
            width: 100% !important;
            border-radius: 1rem !important;
        }
        .p-tabs-minimal {
            gap: 1.5rem !important;
            padding: 0 0.25rem !important;
        }
        .tab-link {
            position: relative !important;
        }
        .tab-link.active::after {
            height: 3px !important;
            border-radius: 999px !important;
        }
    }
    @media (max-width: 480px) {
        .p-title {
            font-size: 1.35rem !important;
            margin-bottom: 0.25rem !important;
        }
        .p-vendor-label {
            margin-bottom: 0.25rem !important;
        }
        .p-current-price {
            font-size: 1.5rem !important;
        }
        .p-compare-at {
            font-size: 0.9rem !important;
        }
        .p-discount-badge {
            font-size: 0.75rem !important;
            padding: 0.3rem 0.7rem !important;
            border-radius: 999px !important;
            white-space: nowrap !important;
        }
        .delivery-note {
            font-size: 0.85rem !important;
            padding: 0.75rem !important;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%) !important;
            border-radius: 0.75rem !important;
            margin-bottom: 1.5rem !important;
        }
        .p-section-label {
            font-size: 0.8rem !important;
            margin-bottom: 0.75rem !important;
            letter-spacing: 0.1em !important;
        }
        .deal-card {
            padding: 1rem !important;
        }
        .deal-title {
            font-size: 0.9rem !important;
        }
        .deal-save {
            font-size: 0.75rem !important;
        }
        .btn-deal-add {
            font-size: 0.75rem !important;
            padding: 0.6rem 0.9rem !important;
            border-radius: 0.75rem !important;
        }
        .p-actions-row {
            margin-bottom: 1rem !important;
            gap: 0.75rem !important;
            flex-direction: row !important;
        }
        .p-actions-row .qty-control {
            width: auto !important;
            padding: 0.25rem !important;
        }
        .p-actions-row button {
            padding: 1rem !important;
        }
        .qty-control button {
            font-size: 1.5rem !important;
            padding: 0.5rem 1.5rem !important;
        }
        .page-qty {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
        }
        #buy-now-btn, .wishlist-toggle-btn {
            padding: 0.85rem !important;
            font-size: 0.95rem !important;
            border-radius: 1rem !important;
        }
        .wishlist-toggle-btn {
            width: 48px !important;
            height: 48px !important;
            font-size: 1.2rem !important;
        }
        .tab-link {
            font-size: 0.8rem !important;
            padding: 0.85rem 0 !important;
            font-weight: 600 !important;
        }
        .tab-content-minimal {
            font-size: 0.9rem !important;
            line-height: 1.6 !important;
            padding: 0.5rem 0 !important;
        }
        .department-section {
            margin-top: 2.5rem !important;
        }
        /* Fix excessive margins on mobile */
        .variant-container, .size-rect-grid {
            margin-bottom: 1rem !important;
        }
        .size-rect-grid {
            gap: 0.5rem !important;
            flex-direction: column !important;
        }
        .size-rect {
            padding: 0.6rem 0.8rem !important;
        }
        .p-actions-row {
            margin-bottom: 0.75rem !important;
            gap: 0.5rem !important;
            flex-direction: column !important;
        }
        .secondary-actions-row {
            margin-bottom: 1rem !important;
            gap: 0.5rem !important;
        }
        .delivery-note {
            margin-bottom: 1rem !important;
        }
        .p-tabs-minimal {
            margin-bottom: 1rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="product-page-container">
    <div class="breadcrumb" style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem; color: #64748b; margin-bottom: 2rem;">
        <a href="{{ route('v3.home') }}" style="color: inherit; text-decoration: none;">Home</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
        <a href="{{ route('v3.all-products') }}" style="color: inherit; text-decoration: none;">Shop</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
        <span style="color: var(--primary-color); font-weight: 600;">{{ $product->title }}</span>
    </div>

    <div class="product-core-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 3rem; align-items: start; margin-bottom: 4rem;">
        <!-- Product Gallery -->
        <div class="product-gallery">
            <div class="main-image-display" style="background: #fff; border-radius: 1.5rem; overflow: hidden; aspect-ratio: 1; border: 1px solid var(--border-color); margin-bottom: 1rem; position: relative; display: flex; align-items: center; justify-content: center;">
                @if(isset($packBundles) && $packBundles->count() > 0)
                    @php 
                        $bestPack = $packBundles->sortBy('total_price')->first(); 
                        $bestQty = $bestPack->products->first()->pivot->quantity;
                    @endphp
                    <div class="p-gallery-badge" style="position: absolute; top: 1.25rem; left: 1.25rem; background: #6366f1; color: #fff; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.8rem; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2); z-index: 10;">
                        <i class="fa-solid fa-tags"></i> Buy {{ $bestQty }} for ₹{{ number_format($bestPack->total_price, 0) }}
                    </div>
                @endif
                @php 
                    $mainImg = $product->main_image_url ?? asset('Images/placeholder-grocery.webp');
                @endphp
                <img src="{{ $mainImg }}" id="p-main-img" alt="{{ $product->title }}" onerror="this.src='{{ asset('Images/placeholder-grocery.webp') }}'" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div class="thumb-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                @foreach($product->images->take(4) as $img)
                    @php 
                        $thumbPath = $img->path;
                        if (Str::startsWith($thumbPath, 'http')) {
                            // External URL
                        } elseif (Str::startsWith($thumbPath, 'Images/')) {
                            $thumbPath = asset($thumbPath);
                        } else {
                            $thumbPath = \Illuminate\Support\Facades\Storage::url($thumbPath);
                        }
                    @endphp
                    <div class="thumb-item {{ $loop->first ? 'active' : '' }}" onclick="updateImg('{{ $thumbPath }}', this)" style="border: 2px solid {{ $loop->first ? 'var(--accent-color)' : 'var(--border-color)' }}; border-radius: 0.75rem; overflow: hidden; aspect-ratio: 1; cursor: pointer; transition: 0.2s;">
                        <img src="{{ $thumbPath }}" alt="Gallery thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Product Info Panel -->
        <div class="product-details-panel" style="background: #fff; padding: 2.5rem; border-radius: 1.5rem; border: 1px solid var(--border-color);">
            <p class="p-vendor-label" style="font-size: 0.75rem; font-weight: 800; color: var(--accent-color); letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 0.5rem;">Organic & Fresh</p>
            <h1 class="p-title" style="font-size: 2.2rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1rem; line-height: 1.2;">{{ $product->title }}</h1>
            
            <div class="p-price-row" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                <span class="p-current-price" id="p-price-display" style="font-size: 2rem; font-weight: 800; color: var(--accent-color);">₹{{ number_format($product->starting_price, 2) }}</span>
                @if($product->compare_at_price > $product->starting_price)
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span class="p-compare-at" style="font-size: 1.1rem; text-decoration: line-through; color: var(--text-muted);">₹{{ number_format($product->compare_at_price, 2) }}</span>
                        @php $discount = round((($product->compare_at_price - $product->starting_price) / $product->compare_at_price) * 100); @endphp
                        <span class="p-discount-badge" style="background: #ecfdf5; color: var(--accent-color); padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 800; white-space: nowrap;">Save {{ $discount }}%</span>
                    </div>
                @endif
            </div>

            <div class="delivery-note" style="margin-bottom: 1.5rem; color: var(--accent-color); font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-truck-fast"></i>
                <span>Delivered by {{ \Carbon\Carbon::now()->addDays($currentTenant->delivery_days ?? 2)->format('D, M d') }}</span>
            </div>

            <!-- Variants -->
            @if($product->variants->count() > 0)
            <div class="variant-container" style="margin-bottom: 1.5rem;">
                <span class="p-section-label" style="display: block; font-size: 0.85rem; font-weight: 800; color: var(--primary-color); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">SELECT OPTION</span>
                <div class="size-rect-grid" style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
                    @foreach($product->variants as $variant)
                        <div class="size-rect {{ $loop->first ? 'active' : '' }}" onclick="selectVariant(this, {{ $variant->price }}, '{{ $variant->size }}', {{ $variant->id }})" style="border: 2px solid {{ $loop->first ? 'var(--accent-color)' : 'var(--border-color)' }}; border-radius: 0.75rem; padding: 0.75rem 1.25rem; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 1rem; background: #fff;">
                            <span class="s-size" style="font-weight: 700; color: var(--primary-color);">{{ $variant->size }}</span>
                            <span class="s-price" style="color: var(--text-muted);">₹{{ number_format($variant->price, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" id="selected-variant-id" name="variant_id" value="{{ $product->variants->first()->id ?? '' }}">
            </div>
            @endif

            <!-- Special Volume / Pack Deals -->
            @if(isset($packBundles) && $packBundles->count() > 0)
            <div class="p-volume-deals" style="margin-bottom: 2rem;">
                <h3 class="p-section-label" style="font-size: 0.9rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Save With Bulk Pack Deals</h3>
                <div class="deals-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($packBundles as $pb)
                        @php 
                            $pb_prod = $pb->products->first();
                            $pb_variant = $pb_prod ? $pb_prod->variants->firstWhere('id', $pb_prod->pivot->product_variant_id) : null;
                        @endphp
                        @if($pb_prod)
                        <div class="deal-card" style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border: 1.5px dashed var(--border-color); padding: 1rem 1.25rem; border-radius: 0.75rem;">
                            <div class="deal-info">
                                <span class="deal-title" style="font-weight: 700; color: var(--primary-color);">
                                    Pack of {{ $pb_prod->pivot->quantity }}
                                    @if($pb_variant)
                                        ({{ $pb_variant->size }})
                                    @endif
                                </span>
                                <span class="deal-save" style="display: block; font-size: 0.75rem; color: #10b981; font-weight: 700;">Save ₹{{ number_format(($pb_variant ? $pb_variant->price : $pb_prod->starting_price) * $pb_prod->pivot->quantity - $pb->total_price, 2) }} instantly</span>
                            </div>
                            <button onclick="addPackToCart(event, {{ $pb->id }})" class="btn-deal-add" style="background: var(--primary-color); color: #fff; border: none; padding: 0.6rem 1rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: 0.2s;">
                                Add @ ₹{{ number_format($pb->total_price, 2) }}
                            </button>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Add to Cart actions row -->
            <div class="p-actions-row" style="display: flex; gap: 1rem; margin-bottom: 1.5rem; height: 3.5rem;">
                <div class="qty-control" style="background: #fff; border: 2px solid var(--border-color); border-radius: 0.75rem; display: flex; flex-direction: row; align-items: center; justify-content: center; padding: 0.25rem 0.75rem; min-width: 70px; height: 100%;">
                    <span class="page-qty" style="font-size: 1.2rem; font-weight: 800; text-align: center; line-height: 1; margin-right: 0.75rem;">1</span>
                    <div style="display: flex; flex-direction: column; gap: 0.4rem; align-items: center; justify-content: center;">
                        <button onclick="changePageQty(1)" style="border: none; background: none; padding: 0; font-size: 0.75rem; cursor: pointer; color: var(--text-muted); line-height: 1;"><i class="fa-solid fa-chevron-up"></i></button>
                        <button onclick="changePageQty(-1)" style="border: none; background: none; padding: 0; font-size: 0.75rem; cursor: pointer; color: var(--text-muted); line-height: 1;"><i class="fa-solid fa-chevron-down"></i></button>
                    </div>
                </div>
                <button class="btn-add-to-cart add-to-cart-btn" id="add-to-cart-page-btn" style="flex-grow: 1; height: 100%; background: var(--accent-color); color: #fff; border: none; border-radius: 0.75rem; font-weight: 800; font-size: 1rem; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap;">
                    ADD TO CART <span class="btn-price-display">₹{{ number_format($product->starting_price, 2) }}</span>
                </button>
            </div>
            <div class="secondary-actions-row" style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <button id="buy-now-btn" style="flex-grow: 1; background: var(--primary-color); color: #fff; border: none; padding: 1rem; border-radius: 0.75rem; font-weight: 800; font-size: 1rem; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fa-solid fa-bolt"></i> BUY NOW
                </button>
                @php
                    $isPageWishlisted = false;
                    if (auth()->check()) {
                        $isPageWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                            ->where('product_id', $product->id)
                            ->exists();
                    }
                @endphp
                <button onclick="toggleWishlist(event, {{ $product->id }})" class="wishlist-toggle-btn" style="width: 50px; height: 50px; border-radius: 0.75rem; background: #f1f5f9; border: none; display: flex; align-items: center; justify-content: center; color: {{ $isPageWishlisted ? '#ef4444' : '#64748b' }}; cursor: pointer; transition: all 0.2s ease; font-size: 1.25rem;">
                    <i class="{{ $isPageWishlisted ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                </button>
            </div>

            <!-- Description Tabs -->
            <div class="p-tabs-minimal" style="display: flex; gap: 2rem; border-bottom: 2px solid var(--border-color); margin-bottom: 1.5rem;">
                @if(!empty($product->description))
                <button class="tab-link active" onclick="switchTab('desc', this)" style="background: none; border: none; padding: 1rem 0; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); cursor: pointer; position: relative;">DESCRIPTION</button>
                <button class="tab-link" onclick="switchTab('shipping', this)" style="background: none; border: none; padding: 1rem 0; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); cursor: pointer; position: relative;">DELIVERY DETAILS</button>
                @else
                <button class="tab-link active" onclick="switchTab('shipping', this)" style="background: none; border: none; padding: 1rem 0; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); cursor: pointer; position: relative;">DELIVERY DETAILS</button>
                @endif
            </div>
            
            @if(!empty($product->description))
            <div class="tab-content-minimal" id="tab-desc" style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6;">
                <p>{!! nl2br(e($product->description)) !!}</p>
            </div>
            <div class="tab-content-minimal d-none" id="tab-shipping" style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6;">
                <p>{!! nl2br(e($currentTenant->delivery_info ?? 'We deliver fresh groceries directly to your home within 2 hours. Free shipping applies to all orders over ₹499. Orders are packed using eco-friendly, hygienically sealed bags.')) !!}</p>
            </div>
            @else
            <div class="tab-content-minimal" id="tab-shipping" style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6;">
                <p>{!! nl2br(e($currentTenant->delivery_info ?? 'We deliver fresh groceries directly to your home within 2 hours. Free shipping applies to all orders over ₹499. Orders are packed using eco-friendly, hygienically sealed bags.')) !!}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Related Products & Bundles -->
    @php 
        // 1. Fetch bundles containing this product
        $relatedBundles = $product->bundles()
            ->where('status', 'active')
            ->where('type', '!=', 'pool')
            ->take(4)
            ->get();
            
        // 2. Fetch related products to fill the remaining slots up to 4
        $remainingSlots = 4 - $relatedBundles->count();
        $relatedProducts = collect();
        if ($remainingSlots > 0) {
            $relatedProducts = \App\Models\Product::where('collection_id', $product->collection_id)
                ->where('id', '!=', $product->id)
                ->take($remainingSlots)
                ->get();
        }
    @endphp
    @if($relatedBundles->count() > 0 || $relatedProducts->count() > 0)
    <div class="department-section" style="margin-top: 4rem;">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h2 class="section-title" style="font-weight: 800; font-size: 1.6rem; color: var(--primary-color);">You Might Also Like</h2>
        </div>
        <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
            @foreach($relatedBundles as $relBundle)
                @include('template_1.partials.bundle_card', ['bundle' => $relBundle])
            @endforeach
            @foreach($relatedProducts as $relProd)
                @include('template_1.partials.product_card', ['product' => $relProd])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    let qty = 1;

    function updateImg(src, el) {
        document.getElementById('p-main-img').src = src;
        // Update active class on thumbnails
        const parent = el.parentElement;
        parent.querySelectorAll('.thumb-item').forEach(t => t.style.borderColor = 'var(--border-color)');
        el.style.borderColor = 'var(--accent-color)';
    }

    function selectVariant(element, price, size, id) {
        // Update active style
        element.parentElement.querySelectorAll('.size-rect').forEach(card => {
            card.style.borderColor = 'var(--border-color)';
        });
        element.style.borderColor = 'var(--accent-color)';

        // Update Price displays
        const formattedPrice = new Intl.NumberFormat('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(price);
        
        document.getElementById('p-price-display').innerText = '₹' + formattedPrice;
        document.querySelector('.btn-price-display').innerText = '₹' + formattedPrice;
        
        // Update hidden input
        document.getElementById('selected-variant-id').value = id;
    }

    function changePageQty(delta) {
        qty = Math.max(1, qty + delta);
        document.querySelector('.page-qty').innerText = qty;
    }

    function switchTab(tab, el) {
        // Update active tab styles
        el.parentElement.querySelectorAll('.tab-link').forEach(b => {
            b.style.color = 'var(--text-muted)';
            b.classList.remove('active');
        });
        el.style.color = 'var(--primary-color)';
        el.classList.add('active');

        // Toggle contents
        document.getElementById('tab-desc').classList.add('d-none');
        document.getElementById('tab-shipping').classList.add('d-none');
        document.getElementById('tab-' + tab).classList.remove('d-none');
    }

    function addToCart(event) {
        const btn = document.getElementById('add-to-cart-page-btn');
        const variantId = document.getElementById('selected-variant-id').value;
        const activeCard = document.querySelector('.size-rect[style*="var(--accent-color)"]') || document.querySelector('.size-rect.active');
        const size = activeCard ? activeCard.querySelector('.s-size').innerText : '';
        const originalHtml = btn.innerHTML;

        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';
        btn.disabled = true;

        $.ajax({
            url: "{{ route('cart.add') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: "{{ $product->id }}",
                quantity: qty,
                size: size,
                variant_id: variantId
            },
            success: function(response) {
                if(response.success) {
                    $('#cart-count').text(response.cartCount);
                    btn.innerHTML = 'ADDED TO BAG!';
                    btn.style.background = '#10B981';
                    
                    // Open Cart Drawer
                    showCartToast();

                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.style.background = '';
                        btn.disabled = false;
                    }, 2000);
                } else {
                    alert('Error: ' + response.message);
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            },
            error: function() {
                alert('Something went wrong. Please try again.');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        });
    }

    document.getElementById('add-to-cart-page-btn').addEventListener('click', addToCart);

    function buyNow() {
        const btn = document.getElementById('buy-now-btn');
        const variantId = document.getElementById('selected-variant-id').value;
        const activeCard = document.querySelector('.size-rect[style*="var(--accent-color)"]') || document.querySelector('.size-rect.active');
        const size = activeCard ? activeCard.querySelector('.s-size').innerText : '';
        const originalHtml = btn.innerHTML;

        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
        btn.disabled = true;

        $.ajax({
            url: "{{ route('cart.add') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: "{{ $product->id }}",
                quantity: qty,
                size: size,
                variant_id: variantId
            },
            success: function(response) {
                if(response.success) {
                    window.location.href = "{{ route('v3.checkout') }}";
                } else {
                    alert('Error: ' + response.message);
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            },
            error: function() {
                alert('Something went wrong. Please try again.');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        });
    }

    document.getElementById('buy-now-btn').addEventListener('click', buyNow);

    function addPackToCart(event, bundleId) {
        event.preventDefault();
        const btn = event.currentTarget;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        btn.disabled = true;

        $.ajax({
            url: "{{ route('cart.add') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: bundleId,
                quantity: 1,
                type: 'bundle'
            },
            success: function(response) {
                if(response.success) {
                    $('#cart-count').text(response.cartCount);
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Added';
                    btn.style.background = '#10B981';
                    
                    // Open Cart Drawer
                    showCartToast();
                    
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.style.background = '';
                        btn.disabled = false;
                    }, 2000);
                }
            }
        });
    }
</script>
@endsection
