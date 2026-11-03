<div class="rb-prism-label">{{ $product->collection?->name ?: $currentTenant->name }}</div>
@include('storefront.themes.rebuilt.card_visual')
<div class="rb-prism-card">@include('storefront.themes.cards.info')<div class="rb-prism-action">@include('storefront.themes.cards.actions')</div></div>
