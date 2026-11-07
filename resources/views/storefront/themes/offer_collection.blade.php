@extends('template_1.layouts.app')
@section('title', $collection->name . ' | ' . $currentTenant->name)
@section('styles')
<link rel="stylesheet" href="{{ asset('css/offer-collection.css').'?v='.filemtime(public_path('css/offer-collection.css')) }}">
@endsection
@section('content')
@php
    $fixedOffers = $offers->whereIn('type', ['bundle', 'pack']);
    $pools = $offers->where('type', 'pool');
@endphp
<section class="offer-collection" data-offer-collection="{{ $collection->id }}">
    <header class="offer-collection-heading">
        <div><span class="sf-kicker">{{ $currentTenant->name }}</span><h1>{{ $collection->name }}</h1>@if($collection->description)<p>{{ $collection->description }}</p>@endif<span>{{ $offers->count() }} {{ $offers->count() === 1 ? 'offer' : 'offers' }}</span></div>
        @if($collection->image)<img src="{{ Storage::url($collection->image) }}" alt="{{ $collection->name }}" width="600" height="400">@endif
    </header>
    @if($fixedOffers->isNotEmpty())
        @if($storefrontTheme['id'] === 'template_31')
            @include('storefront.themes.pantryselect.bundles', ['bundles' => $fixedOffers])
        @else
            <section class="offer-fixed"><h2>Bundles & packs</h2><div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">@foreach($fixedOffers as $bundle)@include('template_1.partials.bundle_card')@endforeach</div></section>
        @endif
    @endif
    @foreach($pools as $pool)
        <section class="offer-pool" data-pool-offer="{{ $pool->id }}">
            <div class="offer-pool-heading"><span class="sf-kicker">MIX & MATCH</span><h2>{{ $pool->title }}</h2><p>Choose {{ $pool->min_quantity }} qualifying items · Save {{ $currentTenant->currency ?? '₹' }}{{ number_format($pool->discount_value, 2) }}</p><p>Add qualifying products to your basket to see the applicable offer.</p></div>
            <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
                @forelse($pool->products->where('status', 'active') as $product)@include('template_1.partials.product_card')@empty<p>No qualifying products are available right now.</p>@endforelse
            </div>
        </section>
    @endforeach
    @if($offers->isEmpty())<div class="offer-empty"><h2>New offers are on the way</h2><p>Browse the rest of our store while we get them ready.</p><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Explore products →</a></div>@endif
</section>
@endsection
