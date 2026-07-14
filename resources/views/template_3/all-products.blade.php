@extends('template_3.layouts.app')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <!-- Catalog Header -->
    <div class="catalog-header">
        <div>
            <h1>
                @if(request('keyword'))
                    Search: "{{ request('keyword') }}"
                @else
                    All Products
                @endif
            </h1>
            <span class="catalog-count-badge">{{ $products->total() }} items</span>
        </div>
    </div>

    <!-- Mobile Filter Toggle -->
    <button class="mobile-filter-toggle" onclick="document.getElementById('filter-sidebar').classList.toggle('active')">
        <i class="fa-solid fa-sliders"></i> Filters & Sort
    </button>

    <div class="catalog-layout">
        <!-- Sidebar Filter Form -->
        <aside class="sidebar-filter" id="filter-sidebar">
            <form action="{{ route('v1.all-products') }}" method="GET" id="filter-form">
                @if(request('keyword'))
                    <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                @endif
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;" class="d-lg-none">
                    <h3 style="font-weight: 800; font-size: 1.25rem;">Filters</h3>
                    <button type="button" onclick="document.getElementById('filter-sidebar').classList.remove('active')" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
                </div>
                
                <!-- Sort By -->
                <div class="filter-section">
                    <h4 class="filter-title">Sort By</h4>
                    <select name="sort" class="sort-select" style="width: 100%;" onchange="this.form.submit()">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest Arrivals</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                    </select>
                </div>
                
                <!-- Price Range -->
                <div class="filter-section">
                    <h4 class="filter-title">Price Range</h4>
                    <div class="price-range-inputs">
                        <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}">
                        <span>-</span>
                        <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}">
                    </div>
                    <button type="submit" style="width: 100%; margin-top: 0.5rem; padding: 0.5rem; background: var(--bg-color); border: 1px solid var(--border-color); border-radius: var(--border-radius-md); font-weight: 600; cursor: pointer;">Apply Price</button>
                </div>
                
                <!-- Popular Tags -->
                @if(isset($allTags) && count($allTags) > 0)
                <div class="filter-section">
                    <h4 class="filter-title">Popular Tags</h4>
                    <ul class="filter-list">
                        @foreach($allTags as $tag)
                        <li>
                            <label>
                                <input type="checkbox" name="tags[]" value="{{ $tag }}" onchange="this.form.submit()" {{ in_array($tag, request('tags', [])) ? 'checked' : '' }}>
                                {{ $tag }}
                            </label>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </form>
        </aside>

        <!-- Main Results Content -->
        <main>
            @if($products->count() > 0)
                <div class="product-grid">
                    @foreach($products as $product)
                        @include('template_3.partials.product_card', ['product' => $product])
                    @endforeach
                </div>
                
                <div style="display: flex; justify-content: center; margin-top: 4rem;">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fa-solid fa-basket-shopping"></i>
                    <h3>No products found</h3>
                    <p>We couldn't find anything matching your filters or search criteria.</p>
                    <a href="{{ route('v1.all-products') }}" class="btn-primary">Clear Filters</a>
                </div>
            @endif
            
            <!-- Weekly Combos (Shown second) -->
            @if(isset($bundles) && $bundles->count() > 0)
            <div style="margin-top: 4rem; padding-top: 3rem; border-top: 1px solid var(--border-color);">
                <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; color: var(--primary-color);">
                    <i class="fa-solid fa-boxes-stacked" style="color: var(--accent-color); margin-right: 0.5rem;"></i> Combos & Deals
                </h2>
                <div class="bundle-grid">
                    @foreach($bundles as $bundle)
                        @include('template_1.partials.bundle_card', ['bundle' => $bundle])
                    @endforeach
                </div>
            </div>
            @endif
        </main>
    </div>
</div>
@endsection
