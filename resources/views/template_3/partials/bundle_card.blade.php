<div class="product-card">
    <a href="{{ route('v3.combo', ['id' => $bundle->id]) }}" class="card-img-wrap">
        @php 
            $imagePath = $bundle->image ? Storage::url($bundle->image) : null;
            if (!$imagePath && $bundle->type == 'pack') {
                $firstProd = $bundle->products->first();
                $imagePath = $firstProd ? $firstProd->main_image_url : asset('Images/placeholder-grocery.webp');
            } elseif (!$imagePath) {
                $imagePath = asset('Images/placeholder-grocery.webp');
            }
        @endphp
        <img src="{{ $imagePath }}" alt="{{ $bundle->title }}" onerror="this.src='{{ asset('Images/placeholder-grocery.webp') }}'">
        <div class="pack-deal-badge">
            <i class="fa-solid fa-tags"></i> DEAL
        </div>
    </a>
    
    <div class="card-content">
        <div class="p-price-wrap">
            <span class="p-price">₹{{ number_format($bundle->total_price, 2) }}</span>
            @php
                $originalPrice = $bundle->products->sum(function($p) {
                    return $p->variants->min('price') ?? 0;
                });
            @endphp
            @if($originalPrice > $bundle->total_price)
                <span class="p-compare">₹{{ number_format($originalPrice, 2) }}</span>
            @endif
        </div>
        
        <a href="{{ route('v3.combo', ['id' => $bundle->id]) }}">
            <h3 class="p-name">{{ $bundle->title }}</h3>
        </a>
        
        <div style="display: flex; justify-content: flex-end; align-items: flex-end; margin-top: auto;">
            <div class="p-action product-action-wrapper" data-cart-key="bundle-{{ $bundle->id }}">
                <button class="add-btn inline-add-btn" onclick="event.preventDefault(); updateInlineCart('bundle-{{ $bundle->id }}', 1)">
                    <i class="fa-solid fa-plus"></i>
                </button>
                <div class="qty-control qty-controller" style="display: none;">
                    <button class="qty-btn-minus" onclick="event.preventDefault(); updateInlineCart('bundle-{{ $bundle->id }}', -1)">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                    <span class="qty-value">1</span>
                    <button class="qty-btn-plus" onclick="event.preventDefault(); updateInlineCart('bundle-{{ $bundle->id }}', 1)">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
