@php
    $detailData = $detailData ?? app(\App\Services\StorefrontProduct::class)->data($product);
@endphp
<script>
(() => {
    const options = {{ \Illuminate\Support\Js::from($detailData['variants']) }};
    const productId = {{ $product->id }};
    const currency = {{ \Illuminate\Support\Js::from($currentTenant->currency ?? '₹') }};
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const format = value => currency + Number(value).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
    let selected = options.find(option => String(option.id) === document.getElementById('selected-variant-id')?.value) || options[0];
    let pending = false;
    const message = value => {
        const region = document.getElementById('pd-message');
        if (region) region.textContent = value;
        else if (value && typeof showCartToast === 'function') showCartToast(value);
    };
    const cartKey = () => String(productId) + (selected?.size ? '-' + selected.size : '');
    window.updateImg = (src, element) => {
        document.getElementById('p-main-img').src = src;
        element.parentElement.querySelectorAll('.thumb-item').forEach(item => {
            item.classList.toggle('active', item === element);
            item.setAttribute('aria-pressed', item === element ? 'true' : 'false');
            item.style.borderColor = item === element ? 'var(--accent-color)' : 'var(--border-color)';
        });
    };
    window.syncMainProductUI = () => {
        const available = !!selected?.available;
        const count = Number(window.cartItemsMap?.[cartKey()] || 0);
        const price = document.getElementById('p-price-display');
        if (price) price.textContent = format(selected?.price || 0);
        document.getElementById('main-qty-value').textContent = count;
        const add = document.getElementById('add-to-cart-page-btn');
        add.disabled = pending || !available;
        add.textContent = !available ? 'Out of stock' : count ? 'View basket' : 'Add to basket · ' + format(selected.price);
        add.style.background = available ? 'var(--accent-color)' : '#e2e8f0';
        add.style.color = available ? '#fff' : '#64748b';
        const buy = document.getElementById('buy-now-btn');
        buy.disabled = pending || !available;
        document.querySelectorAll('#main-product-actions .qty-control button').forEach((button, index) => {
            const removes = (button.getAttribute('onclick') || '').includes('-1');
            button.disabled = pending || (removes ? count === 0 : !available);
        });
        const compare = document.getElementById('pd-compare') || document.querySelector('.p-compare-at');
        if (compare) {compare.hidden = !(selected?.compare > selected?.price); compare.textContent = format(selected?.compare || 0);}
        const badge = document.querySelector('.p-discount-badge');
        if (badge) {badge.hidden = !(selected?.compare > selected?.price);badge.textContent = selected?.compare ? 'Save ' + Math.round((1-selected.price/selected.compare)*100) + '%' : '';}
        const stock = document.getElementById('pd-stock');
        if (stock) stock.textContent = available ? 'Available to order' : 'Currently unavailable';
        const sku = document.getElementById('pd-sku');
        if (sku) {sku.hidden = !selected?.sku;sku.textContent = selected?.sku ? 'SKU: ' + selected.sku : '';}
    };
    window.selectProductVariant = (id, element) => {
        selected = options.find(option => Number(option.id) === Number(id));
        if (!selected) return;
        document.querySelectorAll('.size-rect').forEach(item => {
            item.classList.toggle('active', item === element);
            item.setAttribute('aria-pressed', item === element ? 'true' : 'false');
            item.style.borderColor = item === element ? 'var(--accent-color)' : 'var(--border-color)';
            item.style.boxShadow = 'none';
        });
        document.getElementById('selected-variant-id').value = selected.id;
        message('');
        syncMainProductUI();
    };
    window.selectVariant = (element, price, size, id) => selectProductVariant(id, element);
    const originalSync = window.syncCartUI;
    window.syncCartUI = (...args) => {if (typeof originalSync === 'function') originalSync(...args);syncMainProductUI();};
    async function submitCart(data, checkout = false) {
        if (pending) return;
        pending = true;message('');syncMainProductUI();
        try {
            const response = await fetch({{ \Illuminate\Support\Js::from(app(\App\Services\StorefrontTheme::class)->url('cart.add')) }}, {method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify(data)});
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Unable to update your basket. Please try again.');
            window.MetoHubTracking?.consume(result.tracking);
            window.cartItemsMap = result.cartItemsMap || {};
            window.cartTotalCount = result.cartCount;
            window.cartTotalValue = result.cartTotal;
            window.cartImages = result.cartImages || [];
            document.querySelectorAll('[data-cart-count], #cart-count').forEach(element => element.textContent = result.cartCount);
            if (checkout) {window.MetoHubTracking?.beginCheckout();location.href = {{ \Illuminate\Support\Js::from(app(\App\Services\StorefrontTheme::class)->url('v1.checkout')) }};return;}
            if (typeof syncCartUI === 'function') syncCartUI(true);
            if (typeof refreshNCart === 'function') refreshNCart();
            message('Added to your basket.');
        } catch (error) {message(error.message || 'Unable to update your basket. Please try again.');}
        finally {pending = false;syncMainProductUI();}
    }
    window.updateMainCart = delta => {
        if (pending || !selected) return;
        const count = Number(window.cartItemsMap?.[cartKey()] || 0);
        if (delta > 0 && !selected.available) return;
        if (delta > 0 && count === 0) return submitCart({id:productId,quantity:1,variant_id:selected.id,size:selected.size,type:'product'});
        if (typeof updateInlineCart === 'function') updateInlineCart(cartKey(), delta);
    };
    window.addOrOpenCart = () => {
        if (pending || !selected?.available) return;
        if (Number(window.cartItemsMap?.[cartKey()] || 0) > 0) {toggleNCart(true);return;}
        updateMainCart(1);
    };
    window.buyNow = () => {
        if (!selected?.available || pending) return;
        if (Number(window.cartItemsMap?.[cartKey()] || 0) > 0) {window.MetoHubTracking?.beginCheckout();location.href = {{ \Illuminate\Support\Js::from(app(\App\Services\StorefrontTheme::class)->url('v1.checkout')) }};return;}
        submitCart({id:productId,quantity:1,variant_id:selected.id,size:selected.size,type:'product'}, true);
    };
    document.getElementById('buy-now-btn').onclick = buyNow;
    window.addPackToCart = (event, id) => {event.preventDefault();submitCart({id,quantity:1,type:'bundle'});};
    window.showProductInfo = (name, button) => {
        document.querySelectorAll('.pd-info-tabs button').forEach(tab => tab.setAttribute('aria-selected', tab === button ? 'true' : 'false'));
        document.querySelectorAll('.pd-information [role="tabpanel"]').forEach(panel => panel.hidden = panel.id !== 'pd-' + name);
    };
    window.switchTab = (name, button) => {
        document.querySelectorAll('.tab-content-minimal').forEach(panel => panel.classList.toggle('d-none', panel.id !== 'tab-' + name));
        document.querySelectorAll('.tab-link').forEach(tab => tab.classList.toggle('active', tab === button));
    };
    syncMainProductUI();
})();
</script>
