<div class="product-card" style="border: 1px solid var(--border-color); border-radius: 1rem; overflow: hidden; background: #fff; transition: all 0.3s ease; position: relative;">
    <a href="{{ route('v1.combo', ['id' => $bundle->id]) }}" class="card-img" style="display: block; position: relative; padding-top: 100%; background: #f8fafc;">
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
        <div class="social-proof-tag" style="position: absolute; bottom: 10px; left: 10px; background: rgba(255, 255, 255, 0.9); padding: 4px 8px; border-radius: 20px; font-size: 0.65rem; font-weight: 700; color: #059669; display: flex; align-items: center; gap: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); white-space: nowrap; max-width: 90%; overflow: hidden; text-overflow: ellipsis;">
            <i class="fa-solid fa-basket-shopping"></i>
            <span>{{ rand(20, 60) }} bought</span>
        </div>
        <div class="bundle-badge-container" style="position: absolute; top: 10px; right: 10px; background: var(--accent-color); color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; z-index: 10;">
            <i class="{{ $bundle->type == 'pack' ? 'fa-solid fa-layer-group' : 'fa-solid fa-boxes-stacked' }} me-1"></i><span>{{ $bundle->type == 'pack' ? 'BUNDLE' : 'COMBO' }}</span>
        </div>
    </a>
    <div class="card-info" style="padding: 1rem; display: flex; flex-direction: column; gap: 0.25rem;">
        <div style="display: flex; align-items: baseline; gap: 0.5rem; flex-wrap: wrap;">
            <span class="p-price" style="font-weight: 800; font-size: 1.15rem; color: var(--accent-color);">{{ $currentTenant->currency ?? '₹' }}{{ number_format($bundle->total_price, 2) }}</span>
            @php
                $originalPrice = $bundle->products->sum(function($p) {
                    return $p->variants->min('price') ?? 0;
                });
            @endphp
            @if($originalPrice > $bundle->total_price)
                <span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.85rem; white-space: nowrap;">{{ $currentTenant->currency ?? '₹' }}{{ number_format($originalPrice, 2) }}</span>
            @endif
        </div>
        <a href="{{ route('v1.combo', ['id' => $bundle->id]) }}" class="p-name" style="font-weight: 700; font-size: 0.85rem; line-height: 1.15rem; color: var(--primary-color); text-decoration: none; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; height: 3.45rem;">{{ $bundle->title }}</a>
        <span class="p-meta" style="font-size: 0.75rem; color: var(--text-muted); padding-right: 2.2rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $bundle->products->count() }} Products Included</span>
    </div>
    <div class="product-action-wrapper" data-cart-key="bundle-{{ $bundle->id }}" style="position: absolute; bottom: 10px; right: 10px; z-index: 15;">
        <!-- Default Add Button -->
        <button class="inline-add-btn shadow-sm" onclick="updateInlineCart('bundle-{{ $bundle->id }}', 1)" style="width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; border: none; display: flex; align-items: center; justify-content: center; color: var(--primary-color); cursor: pointer; transition: all 0.2s ease;">
            <i class="fa-solid fa-plus"></i>
        </button>
        
        <!-- Quantity Controller (Hidden by default) -->
        <div class="qty-controller shadow-sm" style="display: none; align-items: center; justify-content: space-between; width: 90px; height: 36px; background: #fff; border: 1px solid var(--border-color); border-radius: 20px; padding: 0 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <button class="qty-btn-minus" onclick="updateInlineCart('bundle-{{ $bundle->id }}', -1)" style="width: 28px; height: 28px; border: none; background: transparent; color: var(--text-muted); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.8rem;">
                <i class="fa-solid fa-minus"></i>
            </button>
            <span class="qty-value" style="font-weight: 700; font-size: 0.9rem; color: var(--primary-color);">1</span>
            <button class="qty-btn-plus" onclick="updateInlineCart('bundle-{{ $bundle->id }}', 1)" style="width: 28px; height: 28px; border: none; background: transparent; color: var(--accent-color); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.8rem;">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>
</div>
