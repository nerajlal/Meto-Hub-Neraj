@extends('template_1.layouts.app')
@section('title', $product->title . ' | ' . $currentTenant->name)
@section('meta_description', Str::limit(strip_tags($product->description), 160))
@section('content')
@php
    $detailData = app(\App\Services\StorefrontProduct::class)->data($product);
    $selected = $detailData['selected'];
    $pageWishlisted = auth()->check() && \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists();
@endphp
<article class="pd-page pd-{{ $storefrontTheme['layout'] }}" data-product-detail="{{ $storefrontTheme['layout'] }}">
    <nav class="pd-breadcrumb" aria-label="Breadcrumb"><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.home') }}">Home</a><span>/</span><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Shop</a>@if($product->collection)<span>/</span><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.collection', ['slug' => $product->collection->slug]) }}">{{ $product->collection->name }}</a>@endif</nav>
    @if($storefrontTheme['id'] === 'template_10')
        <div class="pd-terra-layout">@include('storefront.themes.product.gallery')<div class="pd-new-summary">@include('storefront.themes.product.identity') @include('storefront.themes.product.purchase') @include('storefront.themes.product.information')</div></div>
    @elseif($storefrontTheme['id'] === 'template_11')
        <div class="pd-mono-heading">@include('storefront.themes.product.identity')</div>
        <div class="pd-mono-layout">@include('storefront.themes.product.gallery')<div class="pd-new-summary">@include('storefront.themes.product.purchase')</div></div>
        @include('storefront.themes.product.information')
    @elseif($storefrontTheme['id'] === 'template_12')
        <div class="pd-bloom-layout"><div class="pd-new-summary">@include('storefront.themes.product.identity') @include('storefront.themes.product.purchase')</div>@include('storefront.themes.product.gallery')</div>
        @include('storefront.themes.product.information')
    @elseif($storefrontTheme['id'] === 'template_7')
        <div class="pd-atelier-layout"><div class="pd-new-summary">@include('storefront.themes.product.identity') @include('storefront.themes.product.purchase')</div>@include('storefront.themes.product.gallery')</div>
        @include('storefront.themes.product.information')
    @elseif($storefrontTheme['id'] === 'template_8')
        @include('storefront.themes.product.identity')
        <div class="pd-vista-layout">@include('storefront.themes.product.gallery')<div class="pd-new-summary">@include('storefront.themes.product.purchase') @include('storefront.themes.product.information')</div></div>
    @elseif($storefrontTheme['id'] === 'template_9')
        <div class="pd-noir-layout">@include('storefront.themes.product.gallery')<div class="pd-new-summary">@include('storefront.themes.product.identity') @include('storefront.themes.product.purchase')</div></div>
        @include('storefront.themes.product.information')
    @elseif($storefrontTheme['id'] === 'template_2')
        <div class="pd-market-layout">@include('storefront.themes.product.gallery')<div class="pd-market-summary">@include('storefront.themes.product.identity') @include('storefront.themes.product.purchase')</div></div>
        @include('storefront.themes.product.information')
    @elseif($storefrontTheme['id'] === 'template_3')
        <div class="pd-quick-layout">@include('storefront.themes.product.gallery')<div class="pd-quick-summary">@include('storefront.themes.product.identity') @include('storefront.themes.product.purchase') @include('storefront.themes.product.information')</div></div>
    @elseif($storefrontTheme['id'] === 'template_4')
        <div class="pd-pantry-layout"><div class="pd-pantry-summary">@include('storefront.themes.product.identity') @include('storefront.themes.product.purchase') @include('storefront.themes.product.information')</div>@include('storefront.themes.product.gallery')</div>
    @elseif($storefrontTheme['id'] === 'template_5')
        @include('storefront.themes.product.identity')
        <div class="pd-corner-layout">@include('storefront.themes.product.gallery') @include('storefront.themes.product.information')<aside class="pd-corner-purchase"><span class="sf-kicker">YOUR SELECTION</span>@include('storefront.themes.product.purchase')</aside></div>
    @else
        <div class="pd-studio-heading">@include('storefront.themes.product.identity')<span class="pd-studio-store">{{ $currentTenant->name }}<br>THE COLLECTION</span></div>
        <div class="pd-studio-layout">@include('storefront.themes.product.gallery')<aside class="pd-studio-summary">@include('storefront.themes.product.purchase') @include('storefront.themes.product.information')</aside></div>
    @endif
</article>
@php
    $suggestions = \App\Models\Product::where('status', 'active')->where('collection_id', $product->collection_id)->where('id', '!=', $product->id)->with(['variants', 'images'])->take(4)->get();
@endphp
@if($suggestions->isNotEmpty())<section class="sf-shelf pd-related"><div class="sf-section-heading"><span>EXPLORE MORE</span><h2>From the same collection</h2></div><div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">@foreach($suggestions as $suggestion)@include('template_1.partials.product_card', ['product' => $suggestion])@endforeach</div></section>@endif
@endsection
@section('scripts')
    @include('storefront.themes.product.scripts')
@endsection
