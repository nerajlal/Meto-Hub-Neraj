<section class="sf-shelf ps-bundle-shelf" aria-labelledby="ps-bundles-heading">
    <div class="sf-section-heading"><span><i class="fa-solid fa-box-open" aria-hidden="true"></i> BETTER TOGETHER</span><h2 id="ps-bundles-heading">Discover our <em>Combo Deals</em></h2><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combos') }}">All combos & packs →</a></div>
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
                $originalPrice = round((float) $bundle->base_price, 2);
                $comboPrice = round((float) $bundle->total_price, 2);
                $saving = round($originalPrice - $comboPrice, 2);
            @endphp
            <article class="ps-bundle-promo" role="listitem" data-bundle-promo="{{ $bundle->id }}" @if($bundle->promoBackgroundStyle()) style="{{ $bundle->promoBackgroundStyle() }}" @endif>
                <div class="ps-bundle-copy">
                    <span class="ps-bundle-kind">{{ $bundle->type === 'pack' ? 'Pack of '.$bundle->products->sum(fn ($item) => $item->pivot->quantity ?? 1) : 'Combo bundle' }}</span>
                    <h3><a href="{{ $bundleUrl }}">{{ $bundle->title }}</a></h3>
                    @if(filled($bundle->description))<p>{{ Str::limit(strip_tags($bundle->description), 105) }}</p>@endif
                </div>
                @if($bundleImage)<a class="ps-bundle-image" href="{{ $bundleUrl }}" tabindex="-1" aria-hidden="true"><img src="{{ $bundleImage }}" alt="" loading="lazy"></a>@endif
                <div class="ps-bundle-bottom">
                    <div class="ps-bundle-pricing">
                        @if($saving > 0)
                            <del class="ps-bundle-original"><span class="visually-hidden">Original price </span>{{ $currentTenant->currency ?? '₹' }}{{ number_format($originalPrice, 2) }}</del>
                        @endif
                        <strong class="ps-bundle-price"><span class="visually-hidden">Combo price </span>{{ $currentTenant->currency ?? '₹' }}{{ number_format($comboPrice, 2) }}</strong>
                        @if($saving > 0)<span class="ps-bundle-saving">Save {{ $currentTenant->currency ?? '₹' }}{{ number_format($saving, 2) }}</span>@endif
                    </div>
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
