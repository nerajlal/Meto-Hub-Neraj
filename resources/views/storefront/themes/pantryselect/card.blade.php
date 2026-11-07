<div class="ps-card-visual">
    @include('storefront.themes.cards.image')
    @if(!empty($product->tags))<span class="ps-card-tag sf-product-tag">{{ last($product->tags) }}</span>@endif
</div>
<div class="ps-card-caption">@include('storefront.themes.cards.info')@include('storefront.themes.cards.actions')</div>
