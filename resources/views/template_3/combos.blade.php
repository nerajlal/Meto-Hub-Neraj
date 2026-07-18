@extends('template_3.layouts.app')

@section('title', 'Deals | ' . ($currentTenant->name ?? 'Fresh Grocery'))

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <!-- Breadcrumbs -->
    <div class="breadcrumb">
        <a href="{{ route('v1.home') }}"><i class="fa-solid fa-house"></i> Home</a> 
        <span>/</span> 
        <span style="color: var(--text-main); font-weight: 600;">Deals</span>
    </div>

    <div class="collection-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
        <div>
            <h1 class="collection-title" style="font-size: 2rem; font-weight: 800; color: var(--primary-color);">Deals</h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Discover our farm-fresh combo packs and volume savings.</p>
        </div>
    </div>

    @if($bundles->count() > 0)
        <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
            @foreach($bundles as $bundle)
                @include('template_3.partials.bundle_card', ['bundle' => $bundle])
            @endforeach
        </div>
    @else
        <div class="empty-state" style="text-align: center; padding: 4rem 2rem; background: #fff; border-radius: 1.5rem; border: 1px solid var(--border-color); color: var(--text-muted);">
            <i class="fa-solid fa-layer-group mb-4" style="font-size: 3rem; opacity: 0.2; color: var(--accent-color);"></i>
            <h2 style="color: var(--primary-color); margin-bottom: 1rem; font-weight: 700;">No Deals Available</h2>
            <p>We are currently curating new combo packs. Please check back soon!</p>
            <a href="{{ route('v1.all-products') }}" class="btn-primary mt-4">Browse Groceries</a>
        </div>
    @endif
</div>
@endsection
