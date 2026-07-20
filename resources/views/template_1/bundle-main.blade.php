@extends('template_1.layouts.app')

@section('title', $bundle->title . ' | Grocery Combo | ' . ($currentTenant->name ?? 'Fresh Grocery'))

@section('content')
<div class="product-page-container">
    <div class="breadcrumb" style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem; color: #64748b; margin-bottom: 2rem;">
        <a href="{{ route('v1.home') }}" style="color: inherit; text-decoration: none;">Home</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
        <a href="{{ route('v3.combos') }}" style="color: inherit; text-decoration: none;">Weekly Combos</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
        <span style="color: var(--primary-color); font-weight: 600;">{{ $bundle->title }}</span>
    </div>

    <div class="product-core-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 3rem; align-items: start; margin-bottom: 4rem;">
        <!-- Bundle Image Gallery -->
        <div class="product-gallery">
            <div class="main-image-display" style="background: #fff; border-radius: 1.5rem; overflow: hidden; aspect-ratio: 1; border: 1px solid var(--border-color); margin-bottom: 1rem; position: relative; display: flex; align-items: center; justify-content: center;">
                @php 
                    $mainImg = $bundle->image ? Storage::url($bundle->image) : asset('Images/default.png');
                    if (!$bundle->image && $bundle->type == 'pack') {
                        $firstProd = $bundle->products->first();
                        if ($firstProd) {
                            $mainImg = $firstProd->main_image_url;
                        }
                    }
                @endphp
                <img src="{{ $mainImg }}" id="p-main-img" alt="{{ $bundle->title }}" onerror="this.src='{{ asset('Images/default.png') }}'" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="position: absolute; top: 1.25rem; left: 1.25rem; background: var(--accent-color); color: #fff; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.8rem; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2); z-index: 10;">
                    {{ $bundle->type == 'pack' ? 'BUNDLE' : 'COMBO' }}
                </div>
            </div>
            @if($bundle->products->count() > 0)
            <div class="thumb-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                <div class="thumb-item active" onclick="document.getElementById('p-main-img').src='{{ $mainImg }}'; document.querySelectorAll('.thumb-item').forEach(el => el.style.borderColor='var(--border-color)'); this.style.borderColor='var(--accent-color)';" style="border: 2px solid var(--accent-color); border-radius: 0.75rem; overflow: hidden; aspect-ratio: 1; cursor: pointer; transition: 0.2s;">
                    <img src="{{ $mainImg }}" onerror="this.src='{{ asset('Images/default.png') }}'" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                @foreach($bundle->products->take(3) as $prod)
                    <div class="thumb-item" onclick="document.getElementById('p-main-img').src='{{ $prod->main_image_url }}'; document.querySelectorAll('.thumb-item').forEach(el => el.style.borderColor='var(--border-color)'); this.style.borderColor='var(--accent-color)';" style="border: 2px solid var(--border-color); border-radius: 0.75rem; overflow: hidden; aspect-ratio: 1; cursor: pointer; transition: 0.2s;">
                        <img src="{{ $prod->main_image_url }}" onerror="this.src='{{ asset('Images/default.png') }}'" alt="{{ $prod->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Bundle Info Panel -->
        <div class="product-details-panel" style="background: #fff; padding: 2.5rem; border-radius: 1.5rem; border: 1px solid var(--border-color);">
            <p class="p-vendor-label" style="font-size: 0.75rem; font-weight: 800; color: var(--accent-color); letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 0.25rem;">Value Pack Combo</p>
            <h1 class="p-title" style="font-size: 2.2rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.5rem; line-height: 1.2;">{{ $bundle->title }}</h1>
            
            <div class="p-price-row" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                <span class="p-current-price" style="font-size: 2rem; font-weight: 800; color: var(--accent-color);">₹{{ number_format($bundle->total_price, 2) }}</span>
                @php
                    $originalPrice = $bundle->products->sum(function($p) {
                        return $p->variants->min('price') ?? 0;
                    });
                @endphp
                @if($originalPrice > $bundle->total_price)
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span class="p-compare-at" style="font-size: 1.1rem; text-decoration: line-through; color: var(--text-muted);">₹{{ number_format($originalPrice, 2) }}</span>
                        <span class="p-discount-badge" style="background: #ecfdf5; color: var(--accent-color); padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 800; white-space: nowrap;">Save ₹{{ number_format($originalPrice - $bundle->total_price, 2) }}</span>
                    </div>
                @endif
            </div>

            <div class="delivery-note" style="margin-bottom: 2rem; color: var(--accent-color); font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-truck-fast"></i>
                <span>Delivered by {{ \Carbon\Carbon::now()->addDays($currentTenant->delivery_days ?? 2)->format('D, M d') }}</span>
            </div>

            <div class="p-tabs" style="margin-bottom: 2rem;">
                <h3 style="font-size: 0.9rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">About this Combo</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">{{ $bundle->description }}</p>
            </div>

            <div class="bundle-contents" style="margin-bottom: 2rem;">
                <h3 style="font-size: 0.9rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.5px;">Products Included</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($bundle->products as $product)
                        <div class="bundle-product-item" style="background: #f8fafc; border-radius: 0.75rem; border: 1px solid var(--border-color); overflow: hidden; padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                            <img src="{{ $product->main_image_url }}" alt="{{ $product->title }}" onerror="this.src='{{ asset('Images/default.png') }}'" style="width: 50px; height: 50px; border-radius: 0.5rem; object-fit: cover;">
                            <div style="flex-grow: 1;">
                                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--primary-color); margin: 0;">
                                    {{ $product->pivot->quantity }}x
                                    {{ $product->title }}
                                </h4>
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0.25rem 0 0 0;">
                                    @php
                                        $v = $product->pivot->product_variant_id 
                                            ? $product->variants->firstWhere('id', $product->pivot->product_variant_id) 
                                            : $product->variants->first();
                                    @endphp
                                    @if($v && $v->size)
                                        Size: {{ $v->size }}
                                    @else
                                        Category: {{ $product->type ?? 'Grocery' }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-actions-row" style="display: flex; gap: 1rem; margin-bottom: 2rem; height: 3.5rem;">
                <div class="qty-control" style="background: #fff; border: 2px solid var(--border-color); border-radius: 0.75rem; display: flex; flex-direction: row; align-items: center; justify-content: center; padding: 0.25rem 0.75rem; min-width: 70px; height: 100%;">
                    <span id="page-qty" style="font-size: 1.2rem; font-weight: 800; text-align: center; line-height: 1; margin-right: 0.75rem;">1</span>
                    <div style="display: flex; flex-direction: column; gap: 0.4rem; align-items: center; justify-content: center;">
                        <button onclick="changePageQty(1)" style="border: none; background: none; padding: 0; font-size: 0.75rem; cursor: pointer; color: var(--text-muted); line-height: 1;"><i class="fa-solid fa-chevron-up"></i></button>
                        <button onclick="changePageQty(-1)" style="border: none; background: none; padding: 0; font-size: 0.75rem; cursor: pointer; color: var(--text-muted); line-height: 1;"><i class="fa-solid fa-chevron-down"></i></button>
                    </div>
                </div>
                <button class="btn-add-to-cart add-to-cart-btn" id="add-to-cart-bundle-btn" style="flex-grow: 1; height: 100%; background: var(--accent-color); color: #fff; border: none; border-radius: 0.75rem; font-weight: 800; font-size: 1rem; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap;">
                    <span>ADD TO BAG</span>
                    <span style="width: 1px; height: 16px; background: rgba(255,255,255,0.3); margin: 0 0.5rem;"></span>
                    <span id="btn-price-display">₹{{ number_format($bundle->total_price, 2) }}</span>
                </button>
            </div>

            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 1.25rem; border-radius: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fa-solid fa-shield-halved" style="color: #16a34a; font-size: 1.25rem;"></i>
                <p style="font-size: 0.85rem; color: #166534; font-weight: 600; margin: 0;">Freshness Guarantee: If any item is not up to your standard, get a refund at your door.</p>
            </div>
        </div>
    </div>

    <!-- Related Combos -->
    @if(isset($relatedBundles) && $relatedBundles->count() > 0)
    <div class="department-section" style="margin-top: 4rem;">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h2 class="section-title" style="font-weight: 800; font-size: 1.6rem; color: var(--primary-color);">Other Value Combos</h2>
        </div>
        <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
            @foreach($relatedBundles as $relBundle)
                @include('template_1.partials.bundle_card', ['bundle' => $relBundle])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    let qty = 1;

    function changePageQty(delta) {
        qty = Math.max(1, qty + delta);
        document.getElementById('page-qty').innerText = qty;
        
        // Update price display inside button
        const basePrice = {{ $bundle->total_price }};
        const formattedPrice = new Intl.NumberFormat('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(basePrice * qty);
        document.getElementById('btn-price-display').innerText = '₹' + formattedPrice;
    }

    function addToCart() {
        const btn = document.getElementById('add-to-cart-bundle-btn');
        const originalHtml = btn.innerHTML;

        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';
        btn.disabled = true;

        $.ajax({
            url: "{{ route('cart.add') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: "{{ $bundle->id }}",
                quantity: qty,
                type: 'bundle'
            },
            success: function(response) {
                if(response.success) {
                    $('#cart-count').text(response.cartCount);
                    btn.innerHTML = 'ADDED COMBO!';
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

    document.getElementById('add-to-cart-bundle-btn').addEventListener('click', addToCart);
</script>
@endsection
