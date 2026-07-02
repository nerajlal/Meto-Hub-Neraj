@extends('template_2.layouts.app')

@section('title', 'Shop All Groceries | ' . ($currentTenant->name ?? 'Fresh Grocery'))

@section('content')
<div class="collection-header" style="margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
    <div style="max-width: 800px;">
        @if(!empty($keyword))
            <h1 class="collection-title" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color); line-height: 1.2;">
                <i class="fa-solid fa-magnifying-glass me-2" style="font-size: 2rem; opacity: 0.6;"></i>"{{ $keyword }}"
            </h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 1.1rem;">{{ $products->count() }} result{{ $products->count() !== 1 ? 's' : '' }} found &mdash; <a href="{{ route('v3.all-products') }}" style="color: var(--accent-color); font-weight: 600;">Clear search</a></p>
        @else
            <h1 class="collection-title" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color); line-height: 1.2;">Grocery Catalog</h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 1.1rem;">Explore our complete range of farm fresh vegetables, organic fruits, daily essentials, and household items.</p>
        @endif
    </div>
    <div class="collection-stats" style="font-weight: 600; color: var(--text-muted); background: #fff; border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: 0.75rem; font-size: 0.9rem;">
        {{ $products->count() }} items available
    </div>
</div>

<div class="collection-layout-inner">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2 class="section-title" style="font-weight: 800; font-size: 1.6rem; color: var(--primary-color);">All Groceries</h2>
    </div>

    @if($products->count() > 0)
        <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
            @foreach($products as $product)
                @include('template_1.partials.product_card', ['product' => $product])
            @endforeach
        </div>
    @else
        <div class="empty-state" style="text-align: center; padding: 6rem 2rem; background: #fff; border-radius: 2rem; border: 1px solid var(--border-color); color: var(--text-muted);">
            @if(!empty($keyword))
                <i class="fa-solid fa-magnifying-glass mb-4" style="font-size: 4rem; opacity: 0.2; color: var(--accent-color);"></i>
                <h2 style="color: var(--primary-color); margin-bottom: 1rem; font-weight: 800;">No results for "{{ $keyword }}"</h2>
                <p>Try a different search term or browse the full catalog.</p>
                <a href="{{ route('v3.all-products') }}" class="btn-primary mt-4" style="background: var(--accent-color); color: #fff; padding: 0.75rem 2rem; border-radius: 9999px; text-decoration: none; display: inline-block; font-weight: 700; border: none; margin-top: 1.5rem;">Browse All Products</a>
            @else
                <i class="fa-solid fa-basket-shopping mb-4" style="font-size: 4rem; opacity: 0.2; color: var(--accent-color);"></i>
                <h2 style="color: var(--primary-color); margin-bottom: 1rem; font-weight: 800;">Catalog currently empty</h2>
                <p>We are currently updating our digital catalog. Please check back soon!</p>
                <a href="{{ route('v3.home') }}" class="btn-primary mt-4" style="background: var(--accent-color); color: #fff; padding: 0.75rem 2rem; border-radius: 9999px; text-decoration: none; display: inline-block; font-weight: 700; border: none; margin-top: 1.5rem;">Return Home</a>
            @endif
        </div>
    @endif

    <!-- Weekly Combos Section -->
    @if(isset($bundles) && $bundles->count() > 0)
    <div class="department-section" style="margin-bottom: 4rem; margin-top: 4rem;">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 class="section-title" style="font-weight: 800; font-size: 1.6rem; color: var(--primary-color);">Weekly Grocery Combos</h2>
            <a href="{{ route('v3.combos') }}" class="view-all" style="font-weight: 700; color: var(--accent-color); font-size: 0.95rem; text-decoration: none;">View All <i class="fa-solid fa-chevron-right ms-1"></i></a>
        </div>
        <div class="product-grid bundle-responsive-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
            @foreach($bundles->take(4) as $bundle)
                <div class="product-card" style="border: 1px solid var(--border-color); border-radius: 1rem; overflow: hidden; background: #fff; position: relative;">
                    <a href="{{ route('v3.combo', ['id' => $bundle->id]) }}" class="card-img" style="display: block; position: relative; padding-top: 100%; background: #f8fafc;">
                        @php
                            $bundleImg = $bundle->image ? \Illuminate\Support\Facades\Storage::url($bundle->image) : null;
                            if (!$bundleImg) {
                                $firstProd = $bundle->products->first();
                                $bundleImg = $firstProd ? $firstProd->main_image_url : asset('Images/placeholder-grocery.webp');
                            }
                        @endphp
                        <img src="{{ $bundleImg }}" alt="{{ $bundle->title }}" onerror="this.src='{{ asset('Images/placeholder-grocery.webp') }}'" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; top: 10px; right: 10px; background: #10b981; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                            Save Bundle
                        </div>
                    </a>
                    <div class="card-info" style="padding: 1rem; display: flex; flex-direction: column; gap: 0.25rem;">
                        <span class="p-price" style="font-weight: 800; font-size: 1.15rem; color: var(--accent-color);">₹{{ number_format($bundle->total_price, 2) }}</span>
                        <a href="{{ route('v3.combo', ['id' => $bundle->id]) }}" class="p-name" style="font-weight: 700; font-size: 0.95rem; color: var(--primary-color); text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.6rem;">{{ $bundle->title }}</a>
                        <span class="p-meta" style="font-size: 0.75rem; color: var(--text-muted);">{{ $bundle->products->count() }} Products Included</span>
                    </div>
                    <button class="cart-add-btn" data-product-id="{{ $bundle->id }}" data-type="bundle" style="position: absolute; bottom: 10px; right: 10px; width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; border: none; display: flex; align-items: center; justify-content: center; color: var(--primary-color); cursor: pointer;">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
