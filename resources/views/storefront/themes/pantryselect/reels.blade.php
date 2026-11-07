@php
    $reelCards = collect($reels ?? [])->map(function ($reel) use ($currentTenant) {
        $post = \App\Services\InstagramPostUrl::normalize($reel->instagram_url);
        if (!$post || (int) $reel->tenant_id !== (int) $currentTenant->id) return null;
        $product = $reel->link_type === 'product' && $reel->product?->status === 'active'
            && (int) $reel->product->tenant_id === (int) $currentTenant->id ? $reel->product : null;
        $collection = $reel->link_type === 'collection' && $reel->collection?->status
            && (int) $reel->collection->tenant_id === (int) $currentTenant->id ? $reel->collection : null;
        $productData = $product ? app(\App\Services\StorefrontProduct::class)->data($product) : null;
        return ['post' => $post, 'title' => $reel->title ?: ($product?->title ?: ($collection?->name ?: 'A moment from '.$currentTenant->name)),
            'item' => $product?->title ?: $collection?->name,
            'price' => $productData && $productData['selected'] ? $productData['selected']['price'] : null,
            'from' => $productData && $productData['variants']->pluck('price')->unique()->count() > 1,
            'shop' => $product ? app(\App\Services\StorefrontTheme::class)->url('v1.product', ['id' => $product->id])
                : ($collection ? app(\App\Services\StorefrontTheme::class)->url('v1.collection', ['slug' => $collection->slug]) : null),
            'action' => $product ? 'Shop product' : 'Explore collection'];
    })->filter()->values();
    $reelCount = $reelCards->count();
@endphp
@if($reelCount)
<section class="ps-reels ps-reels-count-{{ min($reelCount, 4) }}" aria-labelledby="ps-reels-title" data-pantry-reels>
    <div class="ps-reels-heading">
        <span class="ps-reels-eyebrow"><i class="fa-brands fa-instagram" aria-hidden="true"></i> FROM THE FEED</span>
        <h2 id="ps-reels-title">Shop our <em>reels.</em></h2>
        @if($reelCount > 1)
        <div class="ps-reels-controls" aria-label="Browse reels">
            <button type="button" data-reels-prev aria-label="Previous reels" aria-controls="ps-reels-rail"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
            <button type="button" data-reels-next aria-label="Next reels" aria-controls="ps-reels-rail"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
        </div>
        @endif
    </div>
    <div class="ps-reels-rail" id="ps-reels-rail" data-reels-rail tabindex="0" aria-label="Instagram reel cards">
        @foreach($reelCards as $card)
        <article class="ps-reel-card">
            <div class="ps-reel-embed">
                <iframe src="{{ $card['post'] }}embed/" title="Instagram reel: {{ $card['title'] }}"
                    loading="lazy" allow="autoplay; encrypted-media; picture-in-picture"
                    sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
            <div class="ps-reel-shopping">
                <div>
                    @if($card['shop'])<a class="ps-reel-product" href="{{ $card['shop'] }}">{{ $card['item'] }}</a>@else<strong class="ps-reel-product">{{ $card['title'] }}</strong>@endif
                    @if($card['price'] !== null)<span class="ps-reel-price">{{ $card['from'] ? 'From ' : '' }}{{ $currentTenant->currency ?? '₹' }}{{ number_format($card['price'], 2) }}</span>@endif
                </div>
                @if($card['shop'])<a class="ps-reel-shop" href="{{ $card['shop'] }}" aria-label="{{ $card['action'] }}: {{ $card['item'] }}"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>@endif
            </div>
            <a class="ps-reel-original" href="{{ $card['post'] }}" target="_blank" rel="noopener noreferrer">Open on Instagram <span aria-hidden="true">↗</span></a>
        </article>
        @endforeach
    </div>
</section>
@endif
