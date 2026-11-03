@include('storefront.themes.rebuilt.card_visual')
<div class="rb-petal-card">@include('storefront.themes.cards.info')@if($product->description)<p>{{ Str::limit(strip_tags($product->description), 75) }}</p>@endif @include('storefront.themes.cards.actions')</div>
