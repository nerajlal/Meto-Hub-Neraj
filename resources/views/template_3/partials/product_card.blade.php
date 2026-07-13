<div class="product-card">
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
        
        $initialStock = $product->variants->count() > 0 ? $product->variants->first()->stock : $product->variants->sum('stock');
        $isOut = $initialStock <= 0 && !$product->continue_selling_when_out_of_stock;
    @endphp

    <button class="wishlist-btn {{ $isWishlisted ? 'active' : '' }}" onclick="toggleWishlist(event, {{ $product->id }})">
        <i class="{{ $isWishlisted ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
    </button>

    <a href="{{ route('v3.product', ['id' => $product->id]) }}" class="card-img-wrap">
        @if(isset($product->volume_pricing) && (is_array($product->volume_pricing) ? count($product->volume_pricing) > 0 : $product->volume_pricing->count() > 0))
        <div class="pack-deal-badge">
            <i class="fa-solid fa-boxes-stacked"></i> Pack Deal
        </div>
        @endif
        
        <img src="{{ $imagePath }}" alt="{{ $product->title }}" onerror="this.src='{{ asset('Images/placeholder-grocery.webp') }}'" style="{{ $isOut ? 'opacity: 0.6; filter: grayscale(100%);' : '' }}">
        
        @if($isOut)
        <div class="sold-out-badge">
            SOLD OUT
        </div>
        @endif
        
        @php
            $tags = is_string($product->tags) ? json_decode($product->tags, true) : $product->tags;
        @endphp
        @if(!empty($tags))
        <div class="product-tag-badge">
            {{ is_array($tags) ? end($tags) : (is_object($tags) ? $tags->last() : '') }}
        </div>
        @endif
    </a>

    <div class="card-content">
        <div class="p-price-wrap">
            <span class="p-price">₹{{ number_format($product->starting_price, 2) }}</span>
            @if($product->compare_at_price > $product->starting_price)
                <span class="p-compare">₹{{ number_format($product->compare_at_price, 2) }}</span>
            @endif
        </div>
        
        <a href="{{ route('v3.product', ['id' => $product->id]) }}">
            <h3 class="p-name">{{ $product->title }}</h3>
        </a>
        
        <span class="p-meta">{{ $product->variants->first()->size ?? '1 Unit' }}</span>
        
        <div class="p-action product-action-wrapper" data-cart-key="{{ $product->id }}{{ isset($product->variants->first()->size) && $product->variants->first()->size ? '-' . $product->variants->first()->size : '' }}">
            @if(!$isOut)
            <button class="add-btn inline-add-btn" onclick="event.preventDefault(); updateInlineCart('{{ $product->id }}{{ isset($product->variants->first()->size) && $product->variants->first()->size ? '-' . $product->variants->first()->size : '' }}', 1)">
                <i class="fa-solid fa-plus"></i>
            </button>
            @endif
            
            <div class="qty-control qty-controller" style="display: none;">
                <button class="qty-btn-minus" onclick="event.preventDefault(); updateInlineCart('{{ $product->id }}{{ isset($product->variants->first()->size) && $product->variants->first()->size ? '-' . $product->variants->first()->size : '' }}', -1)">
                    <i class="fa-solid fa-minus"></i>
                </button>
                <span class="qty-value">1</span>
                <button class="qty-btn-plus" onclick="event.preventDefault(); updateInlineCart('{{ $product->id }}{{ isset($product->variants->first()->size) && $product->variants->first()->size ? '-' . $product->variants->first()->size : '' }}', 1)">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        </div>
    </div>
</div>
