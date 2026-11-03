<section class="sf-collection-hero sf-collection-hero-{{ $storefrontTheme['layout'] }}">
    <div class="sf-collection-copy"><span class="sf-kicker">{{ $hero['eyebrow'] }}</span><h1>{{ $hero['heading'] }}</h1><p>{{ $hero['description'] }}</p><a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">{{ $hero['button_label'] }} ↗</a></div>
    <div class="sf-collection-media">@if($sliders->isNotEmpty())@include('storefront.themes.components.banner')@elseif($collections->first()?->image)<img src="{{ Storage::url($collections->first()->image) }}" alt="{{ $collections->first()->name }}">@else<div class="sf-new-monogram" aria-hidden="true">{{ mb_substr($currentTenant->name, 0, 1) }}</div>@endif</div>
    <div class="sf-collection-index"><span>{{ $currentTenant->name }}</span><span>{{ $collections->count() }} collections / Explore the edit</span></div>
</section>
