(() => {
    document.addEventListener('click', event => {
        const option = event.target.closest('[data-market-variant]');
        if (!option || option.disabled) return;
        const card = option.closest('.sf-product-market');
        if (!card) return;
        const variant = JSON.parse(option.dataset.marketVariant);
        const actions = card.querySelector('.product-action-wrapper');
        card.querySelectorAll('[data-market-variant]').forEach(button => button.setAttribute('aria-pressed', String(button === option)));
        card.querySelector('.p-price').textContent = variant.price;
        const prices = card.querySelector('.sf-product-price');
        let compare = prices.querySelector('del');
        if (variant.compare) {
            if (!compare) { compare = document.createElement('del'); prices.append(compare); }
            compare.textContent = variant.compare;
        } else if (compare) compare.remove();
        card.querySelector('.sf-product-image img').src = variant.image;
        card.querySelector('.sf-stock-label')?.remove();
        actions.dataset.cartKey = variant.key;
        const add = actions.querySelector('.inline-add-btn');
        add.disabled = !variant.available;
        add.querySelector('.sf-add-label').textContent = 'Add to basket';
        add.style.display = 'flex';
        actions.querySelector('.qty-controller').style.display = 'none';
        if (typeof window.updateProductCardUI === 'function') {
            window.updateProductCardUI(variant.key, Number(window.cartItemsMap?.[variant.key] || 0));
        }
    });
})();
