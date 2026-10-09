@php
    $cardData = app(\App\Services\StorefrontProduct::class)->data($product);
    $selected = $cardData['selected'];
    $isOut = !$selected || !$selected['available'];
    $cartKey = $product->id . (!empty($selected['size']) ? '-'.$selected['size'] : '');
    $productUrl = app(\App\Services\StorefrontTheme::class)->url('v1.product', ['id' => $product->id]);
    $isWishlisted = auth()->check() && \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists();
@endphp
<article class="product-card sf-product sf-product-{{ $storefrontTheme['layout'] }}" data-product-id="{{ $product->id }}" data-card-style="{{ $storefrontTheme['layout'] }}">
    @if($storefrontTheme['id'] === 'template_2')
        <div class="sf-card-visual">@include('storefront.themes.cards.image')</div>
        @include('storefront.themes.cards.info')
        <div class="sf-card-market-footer">@include('storefront.themes.cards.actions')</div>
    @elseif($storefrontTheme['id'] === 'template_3')
        <div class="sf-card-visual">@include('storefront.themes.cards.image')</div>
        <div class="sf-card-quick-body">@include('storefront.themes.cards.info') @include('storefront.themes.cards.actions')</div>
    @elseif($storefrontTheme['id'] === 'template_4')
        <div class="sf-card-visual">@include('storefront.themes.cards.image')</div>
        <div class="sf-card-pantry-caption">@include('storefront.themes.cards.info') @include('storefront.themes.cards.actions')</div>
    @elseif($storefrontTheme['id'] === 'template_5')
        <div class="sf-card-visual">@include('storefront.themes.cards.image')</div>
        <div class="sf-card-corner-body">@include('storefront.themes.cards.info') @include('storefront.themes.cards.actions')</div>
    @else
        <div class="sf-card-studio-label"><span>{{ $product->collection?->name ?: $currentTenant->name }}</span><a href="{{ $productUrl }}" aria-label="View {{ $product->title }}">↗</a></div>
        <div class="sf-card-visual">@include('storefront.themes.cards.image')</div>
        <div class="sf-card-studio-caption">@include('storefront.themes.cards.info') @include('storefront.themes.cards.actions')</div>
    @endif
</article>
