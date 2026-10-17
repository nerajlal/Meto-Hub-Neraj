/* Store-scoped, consent-gated integrations. No external request is made by this file before consent. */
(() => {
    'use strict';
    if (window.MetoHubTracking) return;
    const node = document.getElementById('metohub-tracking-config');
    if (!node) return;
    let config;
    try { config = JSON.parse(node.textContent); } catch (_) { return; }
    const dialog = document.getElementById('mh-privacy-dialog');
    const options = document.getElementById('mh-privacy-options');
    const analyticsInput = document.getElementById('mh-consent-analytics');
    const marketingInput = document.getElementById('mh-consent-marketing');
    const storageKey = `metohub-consent-v${config.consentVersion}-${config.tenant}`;
    const scope = config.consentScope;
    const loaded = {ga4: false, meta: false, gtm: false};
    const sent = new Set();
    let stopped = false;
    let consent = {analytics: false, marketing: false};
    let stored = false;
    const safeStorage = (store, action, key, value) => {
        try { return window[store][action](key, value); } catch (_) { return null; }
    };
    try {
        const saved = JSON.parse(safeStorage('localStorage', 'getItem', storageKey) || 'null');
        const age = Date.now() - saved?.at;
        if (saved?.scope === scope && age >= 0 && age < config.consentDays * 86400000) {
            consent = {analytics: saved.analytics === true, marketing: saved.marketing === true};
            stored = true;
        }
    } catch (_) { /* A blocked storage API leaves consent off. */ }
    const script = (id, url) => {
        if (document.getElementById(id)) return;
        const element = document.createElement('script');
        element.id = id; element.async = true; element.src = url; element.referrerPolicy = 'no-referrer';
        document.head.appendChild(element);
    };
    // Discard free-text query parameters before third-party code can inspect the address.
    // Preserve store/product identity and valid provider click identifiers.
    const cleanAddress = () => {
        try {
            const url = new URL(location.href);
            for (const key of [...url.searchParams.keys()]) {
                const value = url.searchParams.get(key);
                const identity = ['id', 'slug', 'page', 'store', 'tenant', 'tenant_id'].includes(key);
                const click = ['gclid', 'fbclid'].includes(key) && /^[A-Za-z0-9_-]{1,500}$/.test(value);
                if (!identity && !click) url.searchParams.delete(key);
            }
            if (url.href !== location.href) history.replaceState(history.state, '', url.href);
        } catch (_) { /* Tracking must never interfere with navigation. */ }
    };
    window.mhDataLayer = window.mhDataLayer || [];
    function gtag() { window.mhDataLayer.push(arguments); }
    const consentValues = () => ({analytics_storage: consent.analytics ? 'granted' : 'denied',
        ad_storage: consent.marketing ? 'granted' : 'denied', ad_user_data: consent.marketing ? 'granted' : 'denied',
        ad_personalization: consent.marketing ? 'granted' : 'denied'});
    gtag('consent', 'default', {analytics_storage: 'denied', ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied'});
    const seen = (provider, event) => {
        const key = `${provider}:${event.id}`;
        if (sent.has(key)) return true;
        let orders = [];
        const orderKey = `${storageKey}-orders-${provider}`;
        if (event.name === 'order_placed') {
            try { orders = JSON.parse(safeStorage('sessionStorage', 'getItem', orderKey) || '[]'); } catch (_) { orders = []; }
            if (!Array.isArray(orders)) orders = [];
            if (orders.includes(event.id)) return true;
            safeStorage('sessionStorage', 'setItem', orderKey, JSON.stringify([...orders.slice(-49), event.id]));
        }
        sent.add(key);
        return false;
    };
    const normalize = event => {
        if (!event || String(event.tenant) !== config.tenant || !['view_item', 'add_to_cart', 'remove_from_cart', 'begin_checkout', 'order_placed'].includes(event.name)) return null;
        if (typeof event.id !== 'string' || event.id.length > 120 || !/^[A-Z]{3}$/.test(event.currency) || !Number.isFinite(event.value) || event.value < 0) return null;
        if (!Array.isArray(event.items)) return null;
        const items = event.items.slice(0, 100).filter(item => item && typeof item === 'object' && typeof item.item_id === 'string' && item.item_id.startsWith(`t${config.tenant}-`) && Number.isFinite(item.price) && item.price >= 0 && Number.isInteger(item.quantity) && item.quantity > 0)
            .map(item => ({item_id: item.item_id.slice(0, 80), item_name: String(item.item_name || '').slice(0, 200), item_variant: String(item.item_variant || '').slice(0, 80), price: item.price, quantity: item.quantity}));
        return items.length ? {...event, items} : null;
    };
    const send = (raw, skipGa = false) => {
        if (stopped) return;
        const event = normalize(raw);
        if (!event) return;
        const ecommerce = {currency: event.currency, value: event.value, items: event.items};
        if (loaded.ga4 && consent.analytics && !skipGa && !seen('ga4', event)) {
            gtag('event', event.name, {...ecommerce, send_to: config.ga4, event_id: event.id,
                page_location: config.page.location, page_title: config.page.title, page_referrer: '', transport_type: 'beacon'});
        }
        if (loaded.gtm && consent.analytics && consent.marketing && !seen('gtm', event)) {
            window.mhDataLayer.push({ecommerce: null});
            window.mhDataLayer.push({event: event.name, event_id: event.id, ecommerce});
        }
        const metaName = {view_item: 'ViewContent', add_to_cart: 'AddToCart', begin_checkout: 'InitiateCheckout'}[event.name];
        if (metaName && loaded.meta && consent.marketing && !seen('meta', event)) {
            window.fbq('trackSingle', config.meta, metaName, {content_ids: event.items.map(item => item.item_id), content_type: 'product',
                contents: event.items.map(item => ({id: item.item_id, quantity: item.quantity, item_price: item.price})), currency: event.currency, value: event.value}, {eventID: event.id});
        }
        // Deliberately no Purchase conversion for unpaid COD orders.
    };
    const activate = () => {
        if (stopped) return;
        gtag('consent', 'update', consentValues());
        if (!consent.analytics && !consent.marketing) return;
        cleanAddress();
        if (config.ga4 && consent.analytics && !loaded.ga4) {
            loaded.ga4 = true; window[`ga-disable-${config.ga4}`] = false;
            gtag('js', new Date());
            gtag('config', config.ga4, {send_page_view: false, cookie_prefix: `mh${config.tenant}`, cookie_domain: 'none', cookie_path: '/',
                allow_google_signals: false, allow_ad_personalization_signals: false,
                page_location: config.page.location, page_title: config.page.title, page_referrer: '', ignore_referrer: true});
            script('mh-ga4', `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(config.ga4)}&l=mhDataLayer`);
        }
        if (config.meta && consent.marketing && !loaded.meta) {
            loaded.meta = true;
            if (!window.fbq) {
                const fbq = window.fbq = function () { fbq.callMethod ? fbq.callMethod.apply(fbq, arguments) : fbq.queue.push(arguments); };
                window._fbq = fbq; fbq.push = fbq; fbq.loaded = true; fbq.version = '2.0'; fbq.queue = [];
            }
            window.fbq.disablePushState = true;
            window.fbq('consent', 'grant');
            window.fbq('set', 'autoConfig', false, config.meta);
            window.fbq('init', config.meta);
            script('mh-meta', 'https://connect.facebook.net/en_US/fbevents.js');
        }
        if (config.gtm && consent.analytics && consent.marketing && !loaded.gtm) {
            loaded.gtm = true;
            window.mhDataLayer.push({'gtm.start': Date.now(), event: 'gtm.js', page_location: config.page.location, page_title: config.page.title});
            script('mh-gtm', `https://www.googletagmanager.com/gtm.js?id=${encodeURIComponent(config.gtm)}&l=mhDataLayer`);
        }
        const pageEvent = {id: 'page', name: 'page_view'};
        if (loaded.ga4 && consent.analytics && !seen('ga4', pageEvent)) gtag('event', 'page_view', {send_to: config.ga4, page_location: config.page.location, page_title: config.page.title, page_referrer: ''});
        if (loaded.meta && consent.marketing && !seen('meta', pageEvent)) window.fbq('trackSingle', config.meta, 'PageView', {}, {eventID: `page-${window.crypto?.randomUUID?.() || Date.now()}`});
        if (loaded.gtm && consent.analytics && consent.marketing && !seen('gtm', pageEvent)) window.mhDataLayer.push({event: 'metohub_page_view', page_location: config.page.location, page_title: config.page.title});
        (config.events || []).forEach(event => send(event));
    };
    const open = () => {
        analyticsInput.checked = consent.analytics; marketingInput.checked = consent.marketing;
        if (!dialog.open) {
            if (typeof dialog.showModal === 'function') dialog.showModal();
            else dialog.setAttribute('open', '');
        }
    };
    const persist = next => {
        const revoke = (consent.analytics && !next.analytics) || (consent.marketing && !next.marketing);
        consent = {analytics: next.analytics === true, marketing: next.marketing === true};
        safeStorage('localStorage', 'setItem', storageKey, JSON.stringify({...consent, scope, at: Date.now()}));
        stored = true;
        if (dialog.open) dialog.close();
        if (revoke && Object.values(loaded).some(Boolean)) {
            stopped = true;
            if (config.ga4) window[`ga-disable-${config.ga4}`] = true;
            if (loaded.meta) window.fbq('consent', 'revoke');
            gtag('consent', 'update', consentValues());
            // Reload removes already executing provider code and starts in basic, consent-gated mode.
            location.reload();
            return;
        }
        activate();
    };
    window.MetoHubTracking = {
        consume(payload) {
            if (!payload || String(payload.tenant) !== config.tenant || stopped) return;
            if (payload.cart && normalize(payload.cart)) config.checkout = payload.cart;
            (Array.isArray(payload.events) ? payload.events : []).forEach(event => send(event));
        },
        beginCheckout() {
            if (config.checkout?.items?.length) send(config.checkout, true);
        },
        openChoices: open,
        getConsent: () => ({...consent}),
        setConsent: persist
    };
    dialog.addEventListener('cancel', () => { if (!stored) persist({analytics: false, marketing: false}); });
    document.getElementById('mh-privacy-open').addEventListener('click', open);
    document.querySelectorAll('[data-mh-consent]').forEach(button => button.addEventListener('click', () => {
        switch (button.dataset.mhConsent) {
            case 'accept': persist({analytics: true, marketing: true}); break;
            case 'reject': persist({analytics: false, marketing: false}); break;
            case 'save': persist({analytics: analyticsInput.checked, marketing: marketingInput.checked}); break;
            case 'manage': options.hidden = false; dialog.querySelector('[data-mh-consent="save"]').hidden = false; analyticsInput.focus(); break;
        }
    }));
    document.addEventListener('click', event => {
        const link = event.target.closest?.('a[href]');
        if (!link || event.defaultPrevented || (!config.gtm && !config.meta) || !config.checkout?.items?.length || !config.checkoutUrl) return;
        try {
            const target = new URL(link.href, location.href);
            const checkout = new URL(config.checkoutUrl, location.href);
            if (target.origin === location.origin && target.pathname.replace(/\/$/, '') === checkout.pathname.replace(/\/$/, '')) window.MetoHubTracking.beginCheckout();
        } catch (_) { /* Invalid links do not emit events. */ }
    });
    if (window.jQuery) window.jQuery(document).ajaxSuccess((_event, _xhr, _settings, response) => {
        if (response?.success && response.tracking) window.MetoHubTracking.consume(response.tracking);
    });
    window.addEventListener('storage', event => {
        if (event.key === storageKey || event.key === null) {
            stopped = true;
            if (config.ga4) window[`ga-disable-${config.ga4}`] = true;
            if (loaded.meta) window.fbq('consent', 'revoke');
            location.reload();
        }
    });
    if (stored) activate(); else open();
})();
