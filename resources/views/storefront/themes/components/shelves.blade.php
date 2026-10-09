@php
    $featuredProducts = $bestsellers->pluck('product')->filter(fn($product) => $product && $product->status === 'active');
    $catalogProducts = \App\Models\Product::where('status', 'active')->with(['variants', 'images', 'discounts', 'bundles'])->latest()->take(12)->get();
@endphp
@if($featuredProducts->isNotEmpty())
<section class="sf-shelf"><div class="sf-section-heading"><span>SELECTED BY YOUR STORE</span><h2>{{ $storefrontTheme['id'] === 'template_4' ? 'The favourites edit.' : 'Featured favourites' }}</h2><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">See everything →</a></div><div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }} sf-featured-grid">
    @foreach($featuredProducts as $product)@include('template_1.partials.product_card')@endforeach
</div></section>
@endif
@foreach($collections as $collection)
    @if($collection->products->isNotEmpty())
        <section class="sf-shelf" id="department-{{ $collection->id }}"><div class="sf-section-heading"><span>SHOP THE DEPARTMENT</span><h2>{{ $collection->name }}</h2><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.collection', ['slug' => $collection->slug]) }}">Browse collection →</a></div>
            <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">@foreach($collection->products as $product)@include('template_1.partials.product_card')@endforeach</div>
        </section>
    @endif
@endforeach
@if($collections->every(fn($collection) => $collection->products->isEmpty()))
<section class="sf-shelf"><div class="sf-section-heading"><span>IN YOUR STORE</span><h2>{{ $storefrontTheme['id'] === 'template_4' ? 'Discover the essentials.' : 'Shop the latest' }}</h2><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Browse all →</a></div>
<div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">@forelse($catalogProducts as $product)@include('template_1.partials.product_card')@empty<p class="sf-empty-store">Our shelves are getting ready. Check back soon.</p>@endforelse</div></section>
@endif
@if($bundles->isNotEmpty())
<section class="sf-shelf sf-bundle-shelf"><div class="sf-section-heading"><span>BETTER TOGETHER</span><h2>Discover our bundles</h2><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combos') }}">All bundles →</a></div><div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">@foreach($bundles as $bundle)@include('template_1.partials.bundle_card')@endforeach</div></section>
@endif
@include('storefront.themes.components.reels')
