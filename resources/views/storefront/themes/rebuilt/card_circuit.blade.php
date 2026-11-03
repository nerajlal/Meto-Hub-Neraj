@include('storefront.themes.rebuilt.card_visual')
<div class="rb-device-info">@include('storefront.themes.cards.info')<dl class="rb-device-facts"><div><dt>Options</dt><dd>{{ $cardData['variants']->count() }}</dd></div>@if($selected['sku'] ?? null)<div><dt>SKU</dt><dd>{{ $selected['sku'] }}</dd></div>@endif</dl>@include('storefront.themes.cards.actions')</div>
