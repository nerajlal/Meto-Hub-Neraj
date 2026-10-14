<div class="card-info sf-product-info">
    <span class="sf-product-category">{{ $product->collection?->name ?: $product->vendor ?: 'In store' }}</span>
    @if(in_array($storefrontTheme['id'], ['template_2', 'template_3', 'template_4', 'template_5', 'template_6', 'template_7', 'template_8', 'template_9', 'template_10', 'template_11', 'template_12', 'template_13', 'template_14', 'template_15']))
        <div class="{{ ['template_2' => 'sf-market-title-row', 'template_3' => 'sf-quick-title-row', 'template_4' => 'sf-pantry-title-row', 'template_5' => 'sf-corner-title-row', 'template_6' => 'sf-studio-title-row', 'template_7' => 'sf-new-title-row', 'template_8' => 'sf-new-title-row', 'template_9' => 'sf-new-title-row', 'template_10' => 'sf-new-title-row', 'template_11' => 'sf-new-title-row', 'template_12' => 'sf-new-title-row', 'template_13' => 'sf-new-title-row', 'template_14' => 'sf-new-title-row', 'template_15' => 'sf-new-title-row'][$storefrontTheme['id']] }}">
            <a class="sf-product-name p-name" href="{{ $productUrl }}">{{ $product->title }}</a>
            <span class="sf-product-size">{{ ($selected['size'] ?? '') ?: '1 unit' }}</span>
        </div>
    @else
        <a class="sf-product-name p-name" href="{{ $productUrl }}">{{ $product->title }}</a>
        <span class="sf-product-size">{{ ($selected['size'] ?? '') ?: '1 unit' }}</span>
    @endif
    <div class="sf-product-price"><strong class="p-price">{{ $currentTenant->currency ?? '₹' }}{{ number_format($selected['price'] ?? 0, 2) }}</strong>@if(($selected['compare'] ?? 0) > ($selected['price'] ?? 0))<del>{{ $currentTenant->currency ?? '₹' }}{{ number_format($selected['compare'], 2) }}</del>@endif</div>
    @if(!in_array($storefrontTheme['id'], ['template_2', 'template_3', 'template_5', 'template_7', 'template_8', 'template_9', 'template_10', 'template_11', 'template_12', 'template_13', 'template_14', 'template_15']) && !empty($product->tags))<span class="sf-product-tag">{{ last($product->tags) }}</span>@endif
</div>
