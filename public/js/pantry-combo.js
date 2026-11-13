(() => {
    'use strict';
    const page = document.querySelector('[data-combo-detail="pantryselect"]');
    if (!page) return;
    const key = page.dataset.comboKey;
    const available = page.dataset.comboAvailable === '1';
    const button = page.querySelector('#add-to-cart-bundle-btn');
    const count = () => Math.max(0, Number(window.cartItemsMap?.[key] || 0));
    const sync = () => {
        const quantity = count();
        page.querySelector('#main-qty-value').textContent = quantity;
        button.disabled = !available && quantity === 0;
        button.textContent = quantity > 0 ? 'View basket' : (available ? 'Add to basket · ' : 'Out of stock · ') + page.dataset.comboPrice;
    };
    window.updateMainCart = delta => {
        if ((delta > 0 && !available) || (delta < 0 && count() === 0)) return;
        window.updateInlineCart?.(key, delta);
    };
    window.addOrOpenCart = () => {
        if (count() > 0) window.toggleNCart?.(true);
        else window.updateMainCart(1);
    };
    const original = window.syncCartUI;
    window.syncCartUI = function (...args) { if (typeof original === 'function') original.apply(this, args); sync(); };
    page.querySelectorAll('[data-combo-image]').forEach(thumb => thumb.addEventListener('click', () => {
        page.querySelector('#p-main-img').src = thumb.dataset.comboImage;
        page.querySelectorAll('[data-combo-image]').forEach(item => { const active = item === thumb; item.classList.toggle('active', active); item.setAttribute('aria-pressed', String(active)); });
    }));
    sync();
})();
