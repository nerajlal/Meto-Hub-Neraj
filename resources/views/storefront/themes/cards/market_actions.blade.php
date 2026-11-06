<div class="product-action-wrapper" data-cart-key="{{ $cartKey }}">
    <button type="button" class="inline-add-btn" @disabled($isOut) onclick="updateInlineCart(this.closest('[data-cart-key]').dataset.cartKey, 1)" aria-label="Add {{ $product->title }} to basket"><i class="fa-solid fa-plus" aria-hidden="true"></i><span class="sf-add-label">{{ $isOut ? 'Out of stock' : 'Add to basket' }}</span></button>
    <div class="qty-controller" style="display:none">
        <button type="button" class="qty-btn-minus" onclick="updateInlineCart(this.closest('[data-cart-key]').dataset.cartKey, -1)" aria-label="Remove one {{ $product->title }}"><i class="fa-solid fa-minus" aria-hidden="true"></i></button>
        <span class="qty-value">1</span>
        <button type="button" class="qty-btn-plus" onclick="updateInlineCart(this.closest('[data-cart-key]').dataset.cartKey, 1)" aria-label="Add one {{ $product->title }}"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
    </div>
</div>
