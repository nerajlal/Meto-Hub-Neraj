<div class="rb-watch-brand">{{ $product->vendor ?: $currentTenant->name }}</div>
@include('storefront.themes.rebuilt.card_visual')
<div class="rb-watch-caption">@include('storefront.themes.cards.info')<a class="rb-watch-discover" href="{{ $productUrl }}">Discover this piece ↗</a>@include('storefront.themes.cards.actions')</div>
