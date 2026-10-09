    <div class="product-action-wrapper" data-cart-key="{{ $product->id }}{{ isset($product->variants->first()->size) && $product->variants->first()->size ? '-' . $product->variants->first()->size : '' }}" style="position: absolute; bottom: 10px; right: 10px; z-index: 15;">
        @if(!$isOut)
        <!-- Default Add Button -->
        <button class="inline-add-btn shadow-sm" onclick="updateInlineCart('{{ $product->id }}{{ isset($product->variants->first()->size) && $product->variants->first()->size ? '-' . $product->variants->first()->size : '' }}', 1)" style="width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; border: none; display: flex; align-items: center; justify-content: center; color: var(--primary-color); cursor: pointer; transition: all 0.2s ease;">
            <i class="fa-solid fa-plus"></i>
        </button>
        @endif
        
        <!-- Quantity Controller (Hidden by default) -->
        <div class="qty-controller shadow-sm" style="display: none; align-items: center; justify-content: space-between; width: 90px; height: 36px; background: #fff; border: 1px solid var(--border-color); border-radius: 20px; padding: 0 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <button class="qty-btn-minus" onclick="updateInlineCart('{{ $product->id }}{{ isset($product->variants->first()->size) && $product->variants->first()->size ? '-' . $product->variants->first()->size : '' }}', -1)" style="width: 28px; height: 28px; border: none; background: transparent; color: var(--text-muted); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.8rem;">
                <i class="fa-solid fa-minus"></i>
            </button>
            <span class="qty-value" style="font-weight: 700; font-size: 0.9rem; color: var(--primary-color);">1</span>
            <button class="qty-btn-plus" onclick="updateInlineCart('{{ $product->id }}{{ isset($product->variants->first()->size) && $product->variants->first()->size ? '-' . $product->variants->first()->size : '' }}', 1)" style="width: 28px; height: 28px; border: none; background: transparent; color: var(--accent-color); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.8rem;">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>
