@extends('template_1.layouts.app')

@section('title', 'Shop All Groceries | ' . ($currentTenant->name ?? 'Fresh Grocery'))

@section('content')
<div class="collection-header" style="margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
    <div style="max-width: 800px;">
        @if(!empty($keyword))
            <h1 class="collection-title" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color); line-height: 1.2;">
                <i class="fa-solid fa-magnifying-glass me-2" style="font-size: 2rem; opacity: 0.6;"></i>"{{ $keyword }}"
            </h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 1.1rem;">{{ $products->total() }} result{{ $products->total() !== 1 ? 's' : '' }} found &mdash; <a href="{{ route('v3.all-products') }}" style="color: var(--accent-color); font-weight: 600;">Clear search</a></p>
        @else
            <h1 class="collection-title" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color); line-height: 1.2;">Grocery Catalog</h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 1.1rem;">Explore our complete range of farm fresh vegetables, organic fruits, daily essentials, and household items.</p>
        @endif
    </div>
    <div class="collection-stats" style="font-weight: 600; color: var(--text-muted); background: #fff; border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: 0.75rem; font-size: 0.9rem;">
        {{ $products->total() }} items available
    </div>
</div>

<style>
    .collection-layout-grid { display: grid; grid-template-columns: 280px 1fr; gap: 3rem; align-items: start; }
    .mobile-filter-btn { display: none; }
    @media(max-width: 900px) {
        .collection-layout-grid { grid-template-columns: 1fr; gap: 1rem; }
        .filters-sidebar { display: none; position: static; margin-bottom: 1.5rem; border-radius: 1rem; padding: 1rem; }
        .filters-sidebar.active { display: block; animation: slideDown 0.3s ease; }
        .mobile-filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #fff;
            border: 1px solid var(--border-color);
            padding: 0.5rem 1.25rem;
            border-radius: 9999px;
            font-weight: 700;
            color: var(--primary-color);
            cursor: pointer;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
        }
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="collection-layout-inner">
    <button class="mobile-filter-btn" onclick="document.querySelector('.filters-sidebar').classList.toggle('active')">
        <i class="fa-solid fa-filter"></i> Filters & Sort
    </button>
    
    <div class="collection-layout-grid">
        <!-- Filters Sidebar -->
    <aside class="filters-sidebar" style="background: #fff; padding: 1.5rem; border-radius: 1.5rem; border: 1px solid var(--border-color); position: sticky; top: 100px;">
        <h3 style="font-weight: 800; color: var(--primary-color); margin-bottom: 1.5rem; font-size: 1.25rem;"><i class="fa-solid fa-filter me-2" style="color: var(--accent-color);"></i>Filters</h3>
        <form action="{{ route('v3.all-products') }}" method="GET" id="filter-form">
            @if(!empty($keyword))
                <input type="hidden" name="q" value="{{ $keyword }}">
            @endif

            <div class="filter-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 700; color: var(--primary-color); display: block; margin-bottom: 0.75rem;">Sort By</label>
                <select name="sort" class="form-select" onchange="document.getElementById('filter-form').submit()" style="width: 100%; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid var(--border-color); color: var(--text-muted);">
                    <option value="latest" {{ $currentSort == 'latest' ? 'selected' : '' }}>Newest Arrivals</option>
                    <option value="price_asc" {{ $currentSort == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ $currentSort == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name_asc" {{ $currentSort == 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                    <option value="name_desc" {{ $currentSort == 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                </select>
            </div>

            <div class="filter-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 700; color: var(--primary-color); display: block; margin-bottom: 0.75rem;">Price Range (₹)</label>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="number" name="min_price" value="{{ $currentMinPrice }}" placeholder="Min" style="width: 100%; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    <span style="color: var(--text-muted);">-</span>
                    <input type="number" name="max_price" value="{{ $currentMaxPrice }}" placeholder="Max" style="width: 100%; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                </div>
            </div>

            @if(!empty($allTags))
            <div class="filter-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 700; color: var(--primary-color); display: block; margin-bottom: 0.75rem;">Popular Tags</label>
                <div style="max-height: 200px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.5rem; padding-right: 0.5rem;">
                    @foreach($allTags as $tag)
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-muted); font-size: 0.95rem;">
                        <input type="checkbox" name="tags[]" value="{{ $tag }}" onchange="document.getElementById('filter-form').submit()" {{ in_array($tag, $currentTags) ? 'checked' : '' }} style="accent-color: var(--accent-color);">
                        {{ $tag }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <button type="submit" class="btn-primary" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; background: var(--accent-color); color: #fff; border: none; font-weight: 700; margin-top: 0.5rem; transition: 0.2s;">Apply Filters</button>
            <a href="{{ route('v3.all-products') }}" style="display: block; text-align: center; margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem; text-decoration: underline;">Clear All</a>
        </form>
    </aside>

    <!-- Main Products Area -->
    <div class="main-products-area">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h2 class="section-title" style="font-weight: 800; font-size: 1.6rem; color: var(--primary-color);">All Groceries</h2>
        </div>

    @if($products->count() > 0)
        <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
            @foreach($products as $product)
                @include('template_1.partials.product_card', ['product' => $product])
            @endforeach
        </div>
        <div style="display: flex; justify-content: center; margin-top: 3rem; margin-bottom: 3rem;">
            {{ $products->links('pagination::bootstrap-5') }}
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
                @include('template_1.partials.bundle_card', ['bundle' => $bundle])
            @endforeach
        </div>
    </div>
        </div>
        @endif
    </div>
    </div>
</div>
@endsection
