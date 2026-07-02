@extends('template_2.layouts.app')

@section('title', 'My Wishlist | ' . ($currentTenant->name ?? 'Fresh Grocery'))

@section('content')
<div class="collection-header" style="margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
    <div style="max-width: 800px;">
        <h1 class="collection-title" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color); line-height: 1.2;">My Wishlist</h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 1.1rem;">Products you've saved to buy later. Click heart to remove.</p>
    </div>
    <div class="collection-stats" style="font-weight: 600; color: var(--text-muted); background: #fff; border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: 0.75rem; font-size: 0.9rem;">
        {{ $wishlists->count() }} items saved
    </div>
</div>

<div class="collection-layout-inner">
    @if($wishlists->count() > 0)
        <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
            @foreach($wishlists as $wishlist)
                @if($wishlist->product)
                    @include('template_1.partials.product_card', ['product' => $wishlist->product])
                @endif
            @endforeach
        </div>
    @else
        <div class="empty-state" style="text-align: center; padding: 6rem 2rem; background: #fff; border-radius: 2rem; border: 1px solid var(--border-color); color: var(--text-muted);">
            <i class="fa-regular fa-heart mb-4" style="font-size: 4rem; opacity: 0.2; color: #ef4444;"></i>
            <h2 style="color: var(--primary-color); margin-bottom: 1rem; font-weight: 800;">Your Wishlist is Empty</h2>
            <p>Explore our catalog and click the heart icon on any product to save it here.</p>
            <a href="{{ route('v3.all-products') }}" class="btn-primary mt-4" style="background: var(--accent-color); color: #fff; padding: 0.75rem 2rem; border-radius: 9999px; text-decoration: none; display: inline-block; font-weight: 700; border: none; margin-top: 1.5rem;">Explore Catalog</a>
        </div>
    @endif
</div>
@endsection
