<div class="product-card sf-bundle-standard" style="border: 1px solid var(--border-color); border-radius: 1rem; overflow: hidden; background: #fff; transition: all 0.3s ease; position: relative;">
    <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combo', ['id' => $bundle->id]) }}" class="card-img" style="display: block; position: relative; padding-top: 100%; background: #f8fafc;">
        @php 
            $imagePath = $bundle->image ? Storage::url($bundle->image) : null;
            if (!$imagePath && $bundle->type == 'pack') {
                $firstProd = $bundle->products->first();
                $imagePath = $firstProd ? $firstProd->main_image_url : asset('Images/default.png');
            } elseif (!$imagePath) {
                $imagePath = asset('Images/default.png');
            }
        @endphp
        <img src="{{ $imagePath }}" alt="{{ $bundle->title }}" onerror="this.src='{{ asset('Images/default.png') }}'" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
        <div class="social-proof-tag" style="position: absolute; bottom: 10px; left: 10px; background: var(--store-badge-bg); padding: 4px 8px; border-radius: 20px; font-size: 0.65rem; font-weight: 700; color: var(--store-badge-fg); display: flex; align-items: center; gap: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); white-space: nowrap; max-width: 90%; overflow: hidden; text-overflow: ellipsis;">
            <i class="fa-solid fa-basket-shopping"></i>
            <span>{{ rand(20, 60) }} bought</span>
        </div>
        <div class="bundle-badge-container" style="position: absolute; top: 10px; right: 10px; background: var(--store-badge-bg); color: var(--store-badge-fg); padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; z-index: 10;">
            <i class="{{ $bundle->type == 'pack' ? 'fa-solid fa-layer-group' : 'fa-solid fa-boxes-stacked' }} me-1"></i><span>{{ $bundle->type == 'pack' ? 'Pack of '.$bundle->products->sum(fn ($item) => $item->pivot->quantity ?? 1) : 'COMBO' }}</span>
        </div>
    </a>
    <div class="card-info" style="padding: 1rem; display: flex; flex-direction: column; gap: 0.25rem;">
        <div style="display: flex; align-items: baseline; gap: 0.5rem; flex-wrap: wrap;">
            <span class="p-price" style="font-weight: 800; font-size: 1.15rem; color: var(--accent-color);">{{ $currentTenant->currency ?? '₹' }}{{ number_format($bundle->total_price, 2) }}</span>
            @php
                $originalPrice = $bundle->base_price;
            @endphp
            @if($originalPrice > $bundle->total_price)
                <span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.85rem; white-space: nowrap;">{{ $currentTenant->currency ?? '₹' }}{{ number_format($originalPrice, 2) }}</span>
            @endif
        </div>
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combo', ['id' => $bundle->id]) }}" class="p-name" style="font-weight: 700; font-size: 0.85rem; line-height: 1.15rem; color: var(--primary-color); text-decoration: none; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; height: 3.45rem;">{{ $bundle->title }}</a>
    </div>
    <div class="sf-bundle-card-footer">
        <span class="p-meta">{{ $bundle->products->sum(fn ($item) => $item->pivot->quantity ?? 1) }} items included</span>
        <div class="product-action-wrapper" data-cart-key="bundle-{{ $bundle->id }}">
        <!-- Default Add Button -->
        <button type="button" class="inline-add-btn" onclick="updateInlineCart('bundle-{{ $bundle->id }}', 1)" aria-label="Add {{ $bundle->title }} to basket"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
        <div class="qty-controller" style="display:none">
            <button type="button" class="qty-btn-minus" onclick="updateInlineCart('bundle-{{ $bundle->id }}', -1)" aria-label="Remove one {{ $bundle->title }}"><i class="fa-solid fa-minus" aria-hidden="true"></i></button>
            <span class="qty-value">1</span>
            <button type="button" class="qty-btn-plus" onclick="updateInlineCart('bundle-{{ $bundle->id }}', 1)" aria-label="Add one {{ $bundle->title }}"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
        </div>
        </div>
    </div>
</div>
