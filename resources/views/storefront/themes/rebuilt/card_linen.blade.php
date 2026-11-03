@include('storefront.themes.rebuilt.card_visual')
<div class="rb-linen-card-caption">@include('storefront.themes.cards.info')@if($product->description)<p class="rb-card-excerpt">{{ Str::limit(strip_tags($product->description), 90) }}</p>@endif @include('storefront.themes.cards.actions')</div>
