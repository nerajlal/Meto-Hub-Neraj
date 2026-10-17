/* Dependency-free runtime checks. Load this file after assigning window.integrationRuntimeSource. */
(() => {
    'use strict';
    const assert = (condition, message) => { if (!condition) throw new Error(message); };
    const event = (id, name = 'add_to_cart') => ({id, name, tenant: '1', currency: 'INR', value: 350, items: [{item_id: 't1-p1', price: 350, quantity: 1}]});
    const defaults = {tenant: '1', mode: 'direct', ga4: 'G-TEST123456', meta: '123456789012345', gtm: null,
        consentVersion: 1, consentDays: 180, consentScope: 'direct', page: {location: 'https://shop.example/store/own/product?id=1', title: 'Shirt'},
        checkoutUrl: 'https://shop.example/store/own/checkout', checkout: event('checkout', 'begin_checkout'), events: [event('product', 'view_item')]};
    function boot(extra = {}, saved = {}) {
        const config = {...defaults, ...extra};
        const scripts = [], handlers = {}, nodes = {};
        const local = new Map(Object.entries(saved)), session = new Map();
        const storage = map => ({getItem: key => map.get(key) ?? null, setItem: (key, value) => map.set(key, value)});
        const node = () => ({addEventListener() {}, focus() {}});
        for (const id of ['mh-privacy-options', 'mh-consent-analytics', 'mh-consent-marketing', 'mh-privacy-open']) nodes[id] = node();
        nodes['metohub-tracking-config'] = {textContent: JSON.stringify(config)};
        nodes['mh-privacy-dialog'] = {...node(), open: false, showModal() { this.open = true; }, close() { this.open = false; }, querySelector: () => ({hidden: true})};
        const location = {href: config.page.location, origin: 'https://shop.example', reloads: 0, reload() { this.reloads++; }};
        const document = {getElementById: id => nodes[id], createElement: () => ({}), querySelectorAll: () => [],
            addEventListener: (name, callback) => { handlers[name] = callback; },
            head: {appendChild(element) { scripts.push(element.src); nodes[element.id] = element; }}};
        const window = {localStorage: storage(local), sessionStorage: storage(session), crypto: {randomUUID: () => 'test-uuid'},
            addEventListener: (name, callback) => { handlers[name] = callback; }};
        const history = {state: null, replaceState(_state, _title, url) { location.href = url; }};
        new Function('window', 'document', 'location', 'history', window.integrationRuntimeSource || globalThis.integrationRuntimeSource)(window, document, location, history);
        return {window, scripts, nodes, handlers, location, config, local};
    }
    const count = (test, name) => test.window.mhDataLayer.filter(entry => entry[0] === 'event' && entry[1] === name).length;
    const test = boot();
    assert(test.scripts.length === 0 && test.nodes['mh-privacy-dialog'].open, 'Fresh visit must require consent without loading providers');
    test.window.MetoHubTracking.setConsent({analytics: false, marketing: false});
    assert(test.scripts.length === 0, 'Reject must not load providers');
    test.window.MetoHubTracking.setConsent({analytics: true, marketing: false});
    assert(test.scripts.length === 1 && test.scripts[0].includes('/gtag/js?'), 'Analytics-only choice must load only GA4');
    test.window.MetoHubTracking.consume({tenant: '1', events: [event('cart')]});
    test.window.MetoHubTracking.consume({tenant: '1', events: [event('cart')]});
    assert(count(test, 'add_to_cart') === 1, 'Cart event must be deduplicated');
    test.window.MetoHubTracking.consume({tenant: '2', events: [event('foreign')]});
    test.window.MetoHubTracking.consume({tenant: '1', events: [{...event('invalid'), items: [null, {item_id: 't2-p1', price: 350, quantity: 1}]}]});
    assert(count(test, 'add_to_cart') === 1, 'Foreign and malformed items must be ignored safely');
    test.window.MetoHubTracking.setConsent({analytics: true, marketing: true});
    assert(test.scripts.length === 2 && test.scripts[1].includes('fbevents.js'), 'Marketing consent must load Meta once');
    assert(count(test, 'view_item') === 1, 'Changing consent must not duplicate GA product views');
    const click = (href, defaultPrevented = false) => test.handlers.click({defaultPrevented, target: {closest: () => ({href})}});
    click('https://external.example/store/own/checkout');
    click('https://shop.example/store/other/checkout');
    click('https://shop.example/store/own/checkout', true);
    assert(!test.window.fbq.queue.some(entry => entry[2] === 'InitiateCheckout'), 'Unrelated and cancelled links must not count as checkout');
    click('https://shop.example/store/own/checkout');
    assert(test.window.fbq.queue.filter(entry => entry[2] === 'InitiateCheckout').length === 1, 'Own checkout must emit one Meta checkout event');
    test.window.MetoHubTracking.consume({tenant: '1', events: [event('order-1', 'order_placed')]});
    assert(count(test, 'order_placed') === 1 && !test.window.fbq.queue.some(entry => entry[2] === 'Purchase'), 'COD must not emit a paid Purchase');
    test.handlers.storage({key: null});
    assert(test.location.reloads === 1 && test.window['ga-disable-G-TEST123456'], 'Clearing saved choices in another tab must disable tracking and reload');
    test.window.MetoHubTracking.consume({tenant: '1', events: [event('after-clear')]});
    assert(count(test, 'add_to_cart') === 1, 'No new events after withdrawal');
    const gtm = boot({mode: 'gtm', ga4: null, meta: null, gtm: 'GTM-TEST1234', consentScope: 'gtm'});
    gtm.window.MetoHubTracking.setConsent({analytics: true, marketing: false});
    assert(gtm.scripts.length === 0, 'GTM must require both consent categories');
    gtm.window.MetoHubTracking.setConsent({analytics: true, marketing: true});
    assert(gtm.scripts.length === 1 && gtm.scripts[0].includes('/gtm.js?') && !gtm.window.fbq, 'GTM mode must exclude direct tags');
    const key = 'metohub-consent-v1-1';
    const expired = boot({}, {[key]: JSON.stringify({scope: 'direct', at: Date.now() - 181 * 86400000, analytics: true, marketing: true})});
    assert(expired.scripts.length === 0 && expired.nodes['mh-privacy-dialog'].open, 'Expired consent must be requested again');
    const changed = boot({}, {[key]: JSON.stringify({scope: 'old-destinations', at: Date.now(), analytics: true, marketing: true})});
    assert(changed.scripts.length === 0, 'Destination changes must require new consent');
    document.body.textContent = 'PASS: consent, deduplication, privacy, checkout identity, cross-tab withdrawal, expiry and GTM mode';
})();
