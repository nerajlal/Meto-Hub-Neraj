<section class="ps-cover" data-theme-composition="pantryselect">
    @if($sliders->isNotEmpty())
        <div class="ps-cover-media">@include('storefront.themes.components.market_carousel')</div>
    @endif
    <div class="ps-cover-caption"><div><span class="sf-kicker">{{ $hero['eyebrow'] }}</span><h1>{{ $hero['heading'] }}</h1><p>{{ $hero['description'] }}</p></div><a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">{{ $hero['button_label'] }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
</section>
@if($collections->isNotEmpty())
<nav class="ps-categories" aria-label="Shop by collection">
    @foreach($collections as $collection)
        <a class="ps-category" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.collection', ['slug' => $collection->slug]) }}">
            <span class="ps-category-art">@if($collection->image)<img src="{{ Storage::url($collection->image) }}" alt="" loading="lazy">@else<span aria-hidden="true">{{ mb_substr($collection->name, 0, 1) }}</span>@endif</span>
            <span>{{ $collection->name }}</span>
        </a>
    @endforeach
    <a class="ps-category" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combos') }}"><span class="ps-category-art"><i class="fa-solid fa-box-open" aria-hidden="true"></i></span><span>Bundles & packs</span></a>
</nav>
@endif
@if(filled($currentTenant->delivery_info))
<div class="ps-store-note"><i class="fa-solid fa-truck-fast" aria-hidden="true"></i><p>{{ Str::limit(strip_tags($currentTenant->delivery_info), 250) }}</p><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.shipping-policy') }}">Delivery details ↗</a></div>
@endif
