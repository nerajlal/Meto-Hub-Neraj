@extends('template_3.layouts.app')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <!-- Breadcrumbs -->
    <div class="breadcrumb">
        <a href="{{ route('v3.home') }}"><i class="fa-solid fa-house"></i> Home</a> 
        <span>/</span> 
        <span style="color: var(--text-main); font-weight: 600;">{{ $title ?? 'Category' }}</span>
    </div>

    <!-- Collection Header -->
    <div class="catalog-header">
        <div>
            <h1>{{ $title ?? 'Category' }}</h1>
            <span class="catalog-count-badge">{{ $products->total() }} items</span>
        </div>
    </div>

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
            <p>We couldn't find any products in this category right now.</p>
            <a href="{{ route('v3.home') }}" class="btn-primary">Return Home / Shop All</a>
        </div>
    @endif
</div>
@endsection
