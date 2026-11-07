<section class="sf-shelf ps-bundle-shelf" aria-labelledby="ps-bundles-heading">
    <div class="sf-section-heading"><span>BETTER TOGETHER</span><h2 id="ps-bundles-heading">Discover our bundles</h2><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combos') }}">All bundles →</a></div>
    <div class="ps-bundle-rail" role="list" aria-label="Bundles and packs">
        @foreach($bundles as $bundle)
            @php
                $bundleUrl = app(\App\Services\StorefrontTheme::class)->url('v1.combo', ['id' => $bundle->id]);
                $bundleProduct = $bundle->products->first();
                $bundleProductData = $bundleProduct ? app(\App\Services\StorefrontProduct::class)->data($bundleProduct) : null;
                $bundleVariantImage = $bundleProductData ? $bundleProductData['variants']->firstWhere('id', $bundleProduct->pivot->product_variant_id) : null;
                $bundleProductImage = $bundleProductData ? $bundleProductData['images']->first() : null;
                $bundleImage = $bundle->image ? app(\App\Services\StorefrontProduct::class)->imageUrl($bundle->image) : ($bundleVariantImage['image_url'] ?? $bundleProductImage);
                $bundleOut = $bundle->is_out_of_stock;
            @endphp
            <article class="ps-bundle-promo" role="listitem" data-bundle-promo="{{ $bundle->id }}" @if($bundle->promoBackgroundStyle()) style="{{ $bundle->promoBackgroundStyle() }}" @endif>
                <div class="ps-bundle-copy">
                    <span class="ps-bundle-kind">{{ $bundle->type === 'pack' ? 'Pack of '.$bundle->products->sum(fn ($item) => $item->pivot->quantity ?? 1) : 'Combo bundle' }}</span>
                    <h3><a href="{{ $bundleUrl }}">{{ $bundle->title }}</a></h3>
                    @if(filled($bundle->description))<p>{{ Str::limit(strip_tags($bundle->description), 105) }}</p>@endif
                </div>
                @if($bundleImage)<a class="ps-bundle-image" href="{{ $bundleUrl }}" tabindex="-1" aria-hidden="true"><img src="{{ $bundleImage }}" alt="" loading="lazy"></a>@endif
                <div class="ps-bundle-bottom">
                    <strong class="ps-bundle-price">{{ $currentTenant->currency ?? '₹' }}{{ number_format($bundle->total_price, 2) }}</strong>
                    <div class="product-action-wrapper" data-cart-key="bundle-{{ $bundle->id }}">
                        <button type="button" class="inline-add-btn" onclick="updateInlineCart('bundle-{{ $bundle->id }}', 1)" aria-label="Add {{ $bundle->title }} to basket" @disabled($bundleOut)><span>{{ $bundleOut ? 'Sold out' : 'Add' }}</span>@unless($bundleOut)<i class="fa-solid fa-plus" aria-hidden="true"></i>@endunless</button>
                        <div class="qty-controller" style="display:none">
                            <button type="button" class="qty-btn-minus" onclick="updateInlineCart('bundle-{{ $bundle->id }}', -1)" aria-label="Remove one {{ $bundle->title }}">−</button><span class="qty-value">1</span><button type="button" class="qty-btn-plus" onclick="updateInlineCart('bundle-{{ $bundle->id }}', 1)" aria-label="Add one {{ $bundle->title }}">+</button>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
