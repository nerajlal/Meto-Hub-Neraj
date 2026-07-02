<div class="product-card" style="border: 1px solid var(--border-color); border-radius: 1rem; overflow: hidden; background: #fff; transition: all 0.3s ease; position: relative;">
    <a href="{{ route('v3.product', ['id' => $product->id]) }}" class="card-img" style="display: block; position: relative; padding-top: 100%; background: #f8fafc;">
        @php 
            $imagePath = $product->main_image_url;
            if (!$imagePath) {
                $imagePath = asset('Images/placeholder-grocery.webp');
            }
            $isWishlisted = false;
            if (auth()->check()) {
                $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->exists();
            }
        @endphp
        <img src="{{ $imagePath }}" alt="{{ $product->title }}" onerror="this.src='{{ asset('Images/placeholder-grocery.webp') }}'" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
        
        <!-- Wishlist Button -->
        <button class="wishlist-toggle-btn" onclick="toggleWishlist(event, {{ $product->id }})" style="position: absolute; top: 10px; right: 10px; z-index: 10; width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.95); border: none; display: flex; align-items: center; justify-content: center; color: {{ $isWishlisted ? '#ef4444' : '#64748b' }}; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <i class="{{ $isWishlisted ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
        </button>

        <!-- Social Proof bought count -->
        <div class="social-proof-tag" style="position: absolute; bottom: 10px; left: 10px; background: rgba(255, 255, 255, 0.9); padding: 4px 8px; border-radius: 20px; font-size: 0.65rem; font-weight: 700; color: #059669; display: flex; align-items: center; gap: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); white-space: nowrap; max-width: 90%; overflow: hidden; text-overflow: ellipsis;">
            <i class="fa-solid fa-bolt" style="color: #10b981;"></i>
            <span>Popular</span>
        </div>

        <!-- Pack Offer indicator badge -->
        @if($product->bundles->where('type', 'pack')->isNotEmpty())
            <div style="position: absolute; top: 10px; left: 10px; background: #6366f1; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3); white-space: nowrap;">
                <i class="fa-solid fa-boxes-stacked me-1"></i><span class="hide-text-mobile">Pack Deal</span>
            </div>
        @endif
    </a>
    
    <div class="card-info" style="padding: 1rem; display: flex; flex-direction: column; gap: 0.25rem;">
        <div style="display: flex; align-items: baseline; gap: 0.5rem;">
            <span class="p-price" style="font-weight: 800; font-size: 1.15rem; color: var(--accent-color);">₹{{ number_format($product->starting_price, 2) }}</span>
            @if($product->compare_at_price > $product->starting_price)
                <span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.85rem;">₹{{ number_format($product->compare_at_price, 2) }}</span>
            @endif
        </div>
        <a href="{{ route('v3.product', ['id' => $product->id]) }}" class="p-name" style="font-weight: 700; font-size: 0.95rem; color: var(--primary-color); text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.6rem;">{{ $product->title }}</a>
        <span class="p-meta" style="font-size: 0.75rem; color: var(--text-muted);">{{ $product->type ?? 'Grocery' }} • {{ $product->variants->first()->size ?? '1 Unit' }}</span>
    </div>
    
    <button class="cart-add-btn" data-product-id="{{ $product->id }}" data-default-size="{{ $product->variants->first()->size ?? '' }}" style="position: absolute; bottom: 10px; right: 10px; width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; border: none; display: flex; align-items: center; justify-content: center; color: var(--primary-color); cursor: pointer; transition: all 0.2s ease;">
        <i class="fa-solid fa-plus"></i>
    </button>
</div>
