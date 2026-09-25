@extends('template_3.layouts.app')

@section('content')

<!-- Quick Categories / Aisle Navigation -->
@if(isset($collections) && $collections->count() > 0)
<div class="container" style="padding-top: 2rem; padding-bottom: 1rem;">
    <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">Shop by Aisle</h2>
    <div style="display: flex; gap: 1.5rem; overflow-x: auto; padding-bottom: 1rem; scrollbar-width: none; -webkit-overflow-scrolling: touch;">
        @foreach($collections as $category)
            <a href="{{ route('v1.collection', ['slug' => $category->slug]) }}" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; text-decoration: none; min-width: 80px;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #F1F5F9; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; overflow: hidden; transition: var(--transition);" onmouseover="this.style.borderColor='var(--accent-color)'; this.style.boxShadow='0 4px 10px rgba(16, 185, 129, 0.2)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="fa-solid fa-basket-shopping" style="color: var(--primary-color); font-size: 1.5rem;"></i>
                    @endif
                </div>
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-main); text-align: center; display: block; line-height: 1.1;">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</div>
@endif

<!-- Bestsellers Section -->
@if($bestsellers && $bestsellers->count() > 0)
<div class="container home-category-section">
    <div class="category-header">
        <h2 class="category-title">
            <i class="fa-solid fa-fire" style="color: #EF4444;"></i> Trending Now
        </h2>
        <a href="{{ route('v1.all-products') }}" class="view-all-btn">
            View All <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
    
    <div class="product-grid">
        @foreach($bestsellers as $bestseller)
            @if($bestseller->product)
                @include('template_3.partials.product_card', ['product' => $bestseller->product])
            @endif
        @endforeach
    </div>
</div>
@endif

<!-- Category & Products Section -->
@if($collections && $collections->count() > 0)
    @foreach($collections as $category)
        @if($category->products && $category->products->count() > 0)
        <div class="container home-category-section" style="background: {{ $loop->even ? '#FFFFFF' : 'transparent' }}; padding: 1.5rem 1rem;">
            <div class="category-header">
                <h2 class="category-title">
                    {{ $category->name }}
                </h2>
                <a href="{{ route('v1.collection', ['slug' => $category->slug]) }}" class="view-all-btn">
                    Explore <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="product-grid">
                @foreach($category->products as $product)
                    @include('template_3.partials.product_card', ['product' => $product])
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
@endif

<!-- Instagram Reels Section -->
@if(isset($reels) && $reels->count() > 0)
<div class="container home-category-section">
    <div class="category-header">
        <h2 class="category-title">
            <i class="fa-brands fa-instagram" style="color: #E1306C;"></i> Shop Our Reels
        </h2>
    </div>
    
    <div style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 1rem; scrollbar-width: none; -webkit-overflow-scrolling: touch; scroll-snap-type: x mandatory;">
        @foreach($reels as $reel)
            <div style="flex: 0 0 300px; scroll-snap-align: start; display: flex; flex-direction: column; gap: 0.75rem;">
                <!-- Instagram Embed -->
                <div style="width: 300px; height: 533px; background: #000; border-radius: 12px; overflow: hidden; position: relative; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    @php
                        // Clean the URL (remove query parameters like ?igsh=...)
                        $baseUrl = strtok($reel->instagram_url, '?');
                        $embedUrl = rtrim($baseUrl, '/') . '/embed';
                    @endphp
                    <!-- Sandbox prevents redirecting the parent page or opening popups -->
                    <iframe src="{{ $embedUrl }}" sandbox="allow-scripts allow-same-origin" width="300" height="533" frameborder="0" scrolling="no" allowtransparency="true" style="border: none; width: 100%; height: 100%;"></iframe>
                </div>
                
                <!-- Linked Action -->
                @if($reel->link_type === 'product' && $reel->product)
                    <div style="background: #fff; border: 1px solid var(--border-color); border-radius: 12px; padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                        <div style="flex-grow: 1; min-width: 0;">
                            <h4 style="font-size: 0.85rem; font-weight: 700; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-main);">{{ $reel->product->title }}</h4>
                            <span style="font-size: 0.8rem; color: var(--accent-color); font-weight: 700;">{{ $currentTenant->currency ?? '₹' }}{{ number_format($reel->product->discounted_price, 2) }}</span>
                        </div>
                        <a href="{{ route('v1.product', ['id' => $reel->product->id]) }}" style="background: var(--accent-color); color: #fff; border-radius: 6px; padding: 0.4rem 0.75rem; font-weight: 600; text-decoration: none; font-size: 0.75rem; white-space: nowrap;">Shop</a>
                    </div>
                @elseif($reel->link_type === 'collection' && $reel->collection)
                    <div style="background: #fff; border: 1px solid var(--border-color); border-radius: 12px; padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                        <div style="flex-grow: 1; min-width: 0;">
                            <h4 style="font-size: 0.85rem; font-weight: 700; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-main);">{{ $reel->collection->name }}</h4>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">View Collection</span>
                        </div>
                        <a href="{{ route('v1.collection', ['slug' => $reel->collection->slug]) }}" style="background: var(--accent-color); color: #fff; border-radius: 6px; padding: 0.4rem 0.75rem; font-weight: 600; text-decoration: none; font-size: 0.75rem; white-space: nowrap;">Explore</a>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Bundles Section -->
@if($bundles && $bundles->count() > 0)
<div class="container home-category-section">
    <div class="category-header">
        <h2 class="category-title">
            <i class="fa-solid fa-boxes-stacked" style="color: var(--primary-color);"></i> Exclusive Combos
        </h2>
    </div>
    
    <div class="bundle-grid">
        @foreach($bundles as $bundle)
            @include('template_3.partials.bundle_card', ['bundle' => $bundle])
        @endforeach
    </div>
</div>
@endif

@endsection
