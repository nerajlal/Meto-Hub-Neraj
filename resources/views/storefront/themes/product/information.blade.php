<div class="pd-information">
    @if($storefrontTheme['id'] === 'template_3')
        <div class="pd-info-tabs" role="tablist" aria-label="Product information"><button type="button" id="pd-tab-description" role="tab" aria-controls="pd-description" aria-selected="true" onclick="showProductInfo('description', this)">Details</button><button type="button" id="pd-tab-delivery" role="tab" aria-controls="pd-delivery" aria-selected="false" onclick="showProductInfo('delivery', this)">Delivery</button></div>
        <section id="pd-description" role="tabpanel" aria-labelledby="pd-tab-description"><h2>About this product</h2><p>{!! nl2br(e($product->description ?: 'For more information about this product, contact the store.')) !!}</p></section><section id="pd-delivery" role="tabpanel" aria-labelledby="pd-tab-delivery" hidden><h2>Delivery information</h2><p>{!! nl2br(e($currentTenant->delivery_info ?: 'Contact the store for delivery information.')) !!}</p></section>
    @elseif(in_array($storefrontTheme['id'], ['template_4', 'template_6']))
        <details open><summary>Product details</summary><p>{!! nl2br(e($product->description ?: 'For more information about this product, contact the store.')) !!}</p></details><details><summary>Delivery information</summary><p>{!! nl2br(e($currentTenant->delivery_info ?: 'Contact the store for delivery information.')) !!}</p></details>
    @else
        <section><span class="sf-kicker">THE DETAILS</span><h2>About this product</h2><p>{!! nl2br(e($product->description ?: 'For more information about this product, contact the store.')) !!}</p></section><section><span class="sf-kicker">FROM STORE TO DOOR</span><h2>Delivery information</h2><p>{!! nl2br(e($currentTenant->delivery_info ?: 'Contact the store for delivery information.')) !!}</p></section>
    @endif
</div>
