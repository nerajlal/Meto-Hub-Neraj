<div class="product-card" style="border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; background: #fff; transition: box-shadow 0.2s; position: relative;">
    <a href="{{ route('v1.combo', ['id' => $bundle->id]) }}" class="card-img-wrap" style="display: block; position: relative; padding-top: 100%; background: #f8fafc;">
        @php 
            $imagePath = $bundle->image ? Storage::url($bundle->image) : null;
            if (!$imagePath && $bundle->type == 'pack') {
                $firstProd = $bundle->products->first();
                $imagePath = $firstProd ? $firstProd->main_image_url : asset('Images/placeholder-grocery.webp');
            } elseif (!$imagePath) {
                $imagePath = asset('Images/placeholder-grocery.webp');
            }
        @endphp
        <img src="{{ $imagePath }}" alt="{{ $bundle->title }}" onerror="this.src='{{ asset('Images/placeholder-grocery.webp') }}'" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; top: 8px; right: 8px; background: var(--accent-color); color: #fff; padding: 3px 6px; border-radius: 6px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">
            <i class="fa-solid fa-tags me-1"></i> DEAL
        </div>
    </a>
    <div class="card-info" style="padding: 0.75rem; display: flex; flex-direction: column; gap: 0.25rem;">
        <div style="display: flex; align-items: baseline; gap: 0.5rem; flex-wrap: wrap;">
            <span class="p-price" style="font-weight: 700; font-size: 1.1rem; color: var(--accent-color);">₹{{ number_format($bundle->total_price, 2) }}</span>
            @php
                $originalPrice = $bundle->products->sum(function($p) {
                    return $p->variants->min('price') ?? 0;
                });
            @endphp
            @if($originalPrice > $bundle->total_price)
                <span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.8rem; white-space: nowrap;">₹{{ number_format($originalPrice, 2) }}</span>
            @endif
        </div>
        <a href="{{ route('v1.combo', ['id' => $bundle->id]) }}" class="p-name" style="font-weight: 600; font-size: 0.85rem; color: var(--text-main); text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $bundle->title }}</a>
        <span class="p-meta" style="font-size: 0.75rem; color: var(--text-muted);">{{ $bundle->products->count() }} Items</span>
    </div>
    <button class="cart-add-btn" data-product-id="{{ $bundle->id }}" data-type="bundle" style="position: absolute; bottom: 8px; right: 8px; width: 32px; height: 32px; border-radius: 50%; background: var(--primary-color); border: none; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer;">
        <i class="fa-solid fa-plus"></i>
    </button>
</div>
