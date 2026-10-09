@extends('template_1.layouts.app')
@section('title', ($title ?? 'All Products') . ' | ' . $currentTenant->name)
@section('content')
@php
    $catalogTitle = !empty($keyword) ? 'Results for “'.$keyword.'”' : ($title ?? 'All Products');
    $clearUrl = app(\App\Services\StorefrontTheme::class)->url(isset($collection) ? 'v1.collection' : 'v1.all-products', isset($collection) ? ['slug' => $collection->slug] : []);
    $departments = \App\Models\Collection::where('status', true)->get();
@endphp
<section class="ct-page ct-{{ $storefrontTheme['layout'] }}" data-product-catalog="{{ $storefrontTheme['layout'] }}">
    <header class="ct-heading">
        <div><span class="sf-kicker">{{ ['template_2'=>'FRESH FROM THE STORE','template_3'=>'THE EVERYDAY COLLECTION','template_4'=>'THE PANTRY EDIT','template_5'=>'BROWSE THE DEPARTMENTS','template_6'=>'THE COMPLETE COLLECTION'][$storefrontTheme['id']] }}</span><h1>{{ $catalogTitle }}</h1><p>{{ $collection->description ?? 'Explore the selection from '.$currentTenant->name.'.' }}</p></div>
        <span class="ct-result-count"><strong>{{ $products->total() }}</strong> {{ $products->total() === 1 ? 'product' : 'products' }}</span>
    </header>
    @if($departments->isNotEmpty())<nav class="ct-departments" aria-label="Product departments"><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}" @if(!isset($collection)) aria-current="page" @endif>All products</a>@foreach($departments as $department)<a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.collection', ['slug' => $department->slug]) }}" @if(($collection->id ?? null) === $department->id) aria-current="page" @endif>{{ $department->name }}</a>@endforeach</nav>@endif
    <button type="button" class="ct-filter-toggle" aria-expanded="false" aria-controls="catalog-filters" onclick="const filters=document.getElementById('catalog-filters');const open=filters.classList.toggle('is-open');this.setAttribute('aria-expanded', String(open))"><i class="fa-solid fa-sliders" aria-hidden="true"></i> Filters & sort</button>
    <div class="ct-layout">
        @include('storefront.themes.catalog.filters')
        <div class="ct-results">
            @if($products->isNotEmpty())<div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">@foreach($products as $product)@include('template_1.partials.product_card')@endforeach</div><div class="ct-pagination">{{ $products->links('pagination::bootstrap-5') }}</div>
            @else<div class="ct-empty"><span class="sf-kicker">YOUR SELECTION</span><h2>No products found</h2><p>Try another search or adjust your filters.</p><a class="sf-primary-button" href="{{ $clearUrl }}">Browse the collection</a></div>@endif
        </div>
    </div>
    @if(isset($bundles) && $bundles->isNotEmpty())<section class="sf-shelf ct-bundles"><div class="sf-section-heading"><span>BETTER TOGETHER</span><h2>Explore the bundles</h2></div><div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">@foreach($bundles->take(4) as $bundle)@include('template_1.partials.bundle_card')@endforeach</div></section>@endif
</section>
@endsection
