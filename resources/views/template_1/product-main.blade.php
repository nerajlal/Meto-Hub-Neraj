@extends('template_1.layouts.app')

@section('title', $product->title . ' | ' . ($currentTenant->name ?? 'SaaS Store'))
@section('meta_description', Str::limit(strip_tags($product->description), 160))
@section('meta_keywords', $product->title . ', fresh, organic, grocery, order online')
@section('og_image', $product->main_image_url ?? asset('Images/default.png'))

@section('styles')
<script type="application/ld+json">
{
  "@@context": "https://schema.org/",
  "@@type": "Product",
  "name": "{{ $product->title }}",
  "image": [
    "{{ $product->main_image_url ?? asset('Images/default.png') }}"
  ],
  "description": "{{ strip_tags($product->description) }}",
  "brand": {
    "@@type": "Brand",
    "name": "{{ $currentTenant->name ?? 'SaaS Store' }}"
  },
  "offers": {
    "@@type": "Offer",
    "url": "{{ url()->current() }}",
    "priceCurrency": "INR",
    "price": "{{ $product->discounted_price }}",
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
<div class="product-page-container" data-product-detail="classic">
    <div class="breadcrumb" style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem; color: #64748b; margin-bottom: 2rem;">
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.home') }}" style="color: inherit; text-decoration: none;">Home</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v3.all-products') }}" style="color: inherit; text-decoration: none;">Shop</a>
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
                        <i class="fa-solid fa-tags"></i> Buy {{ $bestQty }} for {{ $currentTenant->currency ?? '₹' }}{{ number_format($bestPack->total_price, 0) }}
                    </div>
                @endif
                @php 
                    $mainImg = app(\App\Services\StorefrontProduct::class)->data($product)['images']->first();
                @endphp
                <img src="{{ $mainImg }}" id="p-main-img" alt="{{ $product->title }}" onerror="this.src='{{ asset('Images/default.png') }}'" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @if($product->images->count() > 1)
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
            @endif
        </div>

        <!-- Product Info Panel -->
        <div class="product-details-panel" style="background: #fff; padding: 3rem; border-radius: 1.5rem; box-shadow: 0 4px 30px rgba(0,0,0,0.03);">
            <p class="p-vendor-label" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;"><span style="width: 20px; height: 1px; background: var(--text-muted);"></span> {{ $product->vendor ?: $product->collection?->name ?: $currentTenant->name }}</p>
            <h1 class="p-title" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1.5rem; line-height: 1.1; letter-spacing: -0.5px;">{{ $product->title }}</h1>
            
            <div class="p-price-row" style="display: flex; align-items: baseline; gap: 1rem; margin-bottom: 2rem;">
                <span class="p-current-price" id="p-price-display" style="font-size: 2.2rem; font-weight: 800; color: var(--primary-color);">{{ $currentTenant->currency ?? '₹' }}{{ number_format($product->discounted_price, 2) }}</span>
                @if($product->compare_at_price > $product->discounted_price)
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span class="p-compare-at" style="font-size: 1.2rem; text-decoration: line-through; color: #94a3b8; font-weight: 600;">{{ $currentTenant->currency ?? '₹' }}{{ number_format($product->compare_at_price, 2) }}</span>
                        @php $discount = round((($product->compare_at_price - $product->discounted_price) / $product->compare_at_price) * 100); @endphp
                        <span class="p-discount-badge" style="background: var(--primary-color); color: #fff; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">Save {{ $discount }}%</span>
                    </div>
                @endif
            </div>

            <div class="delivery-note" style="margin-bottom: 2rem; color: var(--text-main); font-weight: 600; font-size: 0.95rem; display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid var(--accent-color);">
                <i class="fa-solid fa-truck-fast" style="color: var(--accent-color); font-size: 1.1rem;"></i>
                <span>Estimated delivery by <span style="font-weight: 800;">{{ \Carbon\Carbon::now()->addDays($currentTenant->delivery_days ?? 2)->format('D, M d') }}</span></span>
            </div>

            <!-- Variants -->
            @if($product->variants->count() > 0)
            <div class="variant-container" style="margin-bottom: 2rem;">
                <span class="p-section-label" style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1rem;">Select Option</span>
                <div class="size-rect-grid" style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
                    @foreach($product->variants->sortBy('price') as $variant)
                        <div class="size-rect {{ $loop->first ? 'active' : '' }}" onclick="selectVariant(this, {{ $variant->price ?? 0 }}, {{ \Illuminate\Support\Js::from($variant->size) }}, {{ $variant->id ?? 0 }}, {{ $variant->stock ?? 999 }}, {{ $product->continue_selling_when_out_of_stock ? 'true' : 'false' }})" style="border: 1px solid {{ $loop->first ? 'var(--primary-color)' : 'var(--border-color)' }}; border-radius: 8px; padding: 0.85rem 1.25rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: space-between; gap: 1rem; background: #fff; min-width: 140px; box-shadow: {{ $loop->first ? '0 0 0 1px var(--primary-color)' : 'none' }};">
                            <span class="s-size" style="font-weight: 700; color: var(--primary-color); font-size: 0.95rem;">{{ $variant->size }}</span>
                            <span class="s-price" style="color: var(--text-muted); font-weight: 600; font-size: 0.9rem;">{{ $currentTenant->currency ?? '₹' }}{{ number_format($variant->price, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" id="selected-variant-id" name="variant_id" value="{{ $product->variants->sortBy('price')->first()->id ?? '' }}">
            </div>
            @endif

            <!-- Special Volume / Pack Deals -->
            @if(isset($packBundles) && $packBundles->count() > 0)
            <div class="p-volume-deals" style="margin-bottom: 2.5rem;">
                <h3 class="p-section-label" style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.1em;">Save With Bulk Pack Deals</h3>
                <div class="deals-list" style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($packBundles as $pb)
                        @php 
                            $pb_prod = $pb->products->first();
                            $pb_variant = $pb_prod ? $pb_prod->variants->firstWhere('id', $pb_prod->pivot->product_variant_id) : null;
                        @endphp
                        @if($pb_prod)
                        <div class="deal-card" style="display: flex; align-items: center; justify-content: space-between; background: #fff; border: 1px solid var(--border-color); padding: 1.25rem; border-radius: 12px; transition: all 0.2s ease;">
                            <div class="deal-info">
                                <span class="deal-title" style="font-weight: 800; color: var(--primary-color); display: block; margin-bottom: 0.25rem;">
                                    Pack of {{ $pb_prod->pivot->quantity }}
                                    @if($pb_variant)
                                        <span style="color: var(--text-muted); font-weight: 600;">({{ $pb_variant->size }})</span>
                                    @endif
                                </span>
                                <span class="deal-save" style="display: inline-block; font-size: 0.75rem; color: #10b981; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Save {{ $currentTenant->currency ?? '₹' }}{{ number_format(($pb_variant ? $pb_variant->price : $pb_prod->starting_price) * $pb_prod->pivot->quantity - $pb->total_price, 2) }} Instantly</span>
                            </div>
                            <button onclick="addPackToCart(event, {{ $pb->id }})" class="btn-deal-add" style="background: var(--primary-color); color: #fff; border: none; padding: 0.75rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: 0.2s;">
                                Add @ {{ $currentTenant->currency ?? '₹' }}{{ number_format($pb->total_price, 2) }}
                            </button>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @php
                $initialStock = $product->variants->count() > 0 ? $product->variants->sortBy('price')->first()->stock : $product->variants->sum('stock');
                $isOut = $initialStock <= 0 && !$product->continue_selling_when_out_of_stock;
            @endphp
            <!-- Add to Cart actions row -->
            <div class="p-actions-row" id="main-product-actions" style="display: flex; gap: 1rem; margin-bottom: 1rem; height: 3.75rem;">
                <div class="qty-control" style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; display: flex; flex-direction: row; align-items: center; justify-content: center; padding: 0.25rem 1rem; min-width: 90px; height: 100%;">
                    <span class="page-qty" id="main-qty-value" style="font-size: 1.25rem; font-weight: 800; text-align: center; margin-right: 1rem; color: var(--primary-color);">0</span>
                    <div class="classic-qty-stepper" style="display: flex; flex-direction: column; gap: 0.5rem; align-items: center; justify-content: center;">
                        <button type="button" aria-label="Increase quantity" onclick="updateMainCart(1)" style="border: none; background: none; padding: 0; font-size: 0.8rem; cursor: pointer; color: var(--text-muted);"><i class="fa-solid fa-chevron-up" aria-hidden="true"></i></button>
                        <button type="button" aria-label="Decrease quantity" onclick="updateMainCart(-1)" style="border: none; background: none; padding: 0; font-size: 0.8rem; cursor: pointer; color: var(--text-muted);"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button>
                    </div>
                </div>
                <button class="btn-add-to-cart add-to-cart-btn" id="add-to-cart-page-btn" onclick="addOrOpenCart()" {{ $isOut ? 'disabled' : '' }} style="flex-grow: 1; height: 100%; background: {{ $isOut ? '#e2e8f0' : 'var(--accent-color)' }}; color: {{ $isOut ? '#94a3b8' : '#fff' }}; border: none; border-radius: 8px; font-weight: 800; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px; cursor: {{ $isOut ? 'not-allowed' : 'pointer' }}; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.75rem; white-space: nowrap;">
                    @if($isOut)
                        OUT OF STOCK
                    @else
                        <i class="fa-solid fa-bag-shopping"></i> Add To Cart &bull; <span class="btn-price-display">{{ $currentTenant->currency ?? '₹' }}{{ number_format($product->discounted_price, 2) }}</span>
                    @endif
                </button>
            </div>
            <div class="secondary-actions-row" style="display: flex; gap: 1rem; margin-bottom: 2.5rem;">
                <button id="buy-now-btn" {{ $isOut ? 'disabled' : '' }} style="flex-grow: 1; background: {{ $isOut ? '#e2e8f0' : 'var(--primary-color)' }}; color: {{ $isOut ? '#94a3b8' : '#fff' }}; border: none; padding: 1.15rem; border-radius: 8px; font-weight: 800; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px; cursor: {{ $isOut ? 'not-allowed' : 'pointer' }}; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    Buy It Now
                </button>
                @php
                    $isPageWishlisted = false;
                    if (auth()->check()) {
                        $isPageWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                            ->where('product_id', $product->id)
                            ->exists();
                    }
                @endphp
                <button onclick="toggleWishlist(event, {{ $product->id }})" class="wishlist-toggle-btn" style="width: 58px; height: 58px; border-radius: 8px; background: #fff; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: {{ $isPageWishlisted ? '#ef4444' : 'var(--text-muted)' }}; cursor: pointer; transition: all 0.2s ease; font-size: 1.25rem;">
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
                <p>{!! nl2br(e($currentTenant->delivery_info ?? 'Contact the store for delivery information.')) !!}</p>
            </div>
            @else
            <div class="tab-content-minimal" id="tab-shipping" style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6;">
                <p>{!! nl2br(e($currentTenant->delivery_info ?? 'Contact the store for delivery information.')) !!}</p>
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
    @include('storefront.themes.product.scripts')
@endsection
