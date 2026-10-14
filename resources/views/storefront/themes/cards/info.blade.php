<div class="card-info sf-product-info">
    <span class="sf-product-category">{{ $product->collection?->name ?: $product->vendor ?: 'In store' }}</span>
    @if($storefrontTheme['id'] === 'template_2')
        <div class="sf-market-title-row">
            <a class="sf-product-name p-name" href="{{ $productUrl }}">{{ $product->title }}</a>
            <span class="sf-product-size">{{ ($selected['size'] ?? '') ?: '1 unit' }}</span>
        </div>
    @else
        <a class="sf-product-name p-name" href="{{ $productUrl }}">{{ $product->title }}</a>
        <span class="sf-product-size">{{ ($selected['size'] ?? '') ?: '1 unit' }}</span>
    @endif
    <div class="sf-product-price"><strong class="p-price">{{ $currentTenant->currency ?? '₹' }}{{ number_format($selected['price'] ?? 0, 2) }}</strong>@if(($selected['compare'] ?? 0) > ($selected['price'] ?? 0))<del>{{ $currentTenant->currency ?? '₹' }}{{ number_format($selected['compare'], 2) }}</del>@endif</div>
    @if($storefrontTheme['id'] !== 'template_2' && !empty($product->tags))<span class="sf-product-tag">{{ last($product->tags) }}</span>@endif
</div>
