# Crunchiko live storefront audit — 13 November 2026

Store: https://crunchiko.metohub.cloud/

## Scope

Read-only audit of the live homepage, catalog, both collections, three products, three combos/packs, About, Contact, shipping/returns/terms, robots, sitemap index and its three child files, sharing thumbnail, an invalid URL, empty cart and empty checkout. Chrome checks used 1440 px desktop and 390 px mobile viewports. Product variant interaction was exercised with the cart-write function replaced by a local recording stub. No orders, payments, registrations or contact forms were submitted. No production data or application code was changed.

This is not a penetration test, a payment certification, a legal review, or a Lighthouse performance score. Browser checks cannot establish whether customer quotes are genuine or whether food/certification claims are documented.

## Findings to fix first

### 1. Broken JSON-LD context — high

The homepage, product 82, About and combo 9 emit JSON that parses, but lacks the literal `@context` key. Its replacement key contains Blade-generated PHP text beginning `<?php $__contextArgs`. This is not correctly formed schema.org JSON-LD; Organization, Product/Offer and Breadcrumb data should not be assumed recognized by search engines.

Local cause: `resources/views/storefront/seo/head.blade.php` passes an array containing the literal `@context` inside a Blade output expression. The Blade context directive is interfering with that string. Build the JSON-LD document in PHP outside Blade and render the serialized result, then test the actual rendered key/value, not merely JSON parsing or the existence of `@graph`.

### 2. Invented purchase counts — high, trust

On combo 9's related cards, consecutive refreshes showed `34 bought / 37 bought`, then `20 bought / 24 bought`. The local shared bundle-card template explicitly uses `rand(20, 60)`.

Remove the counters or derive them from real qualifying order quantities. Random values must not be presented as sales history.

### 3. Shipping promise and charging disagree — high

The shipping page promises free shipping above ₹499 and a flat charge below it. The live checkout renders `Shipping FREE`; the checked COD order code sets `$shipping = 0`, and online-payment order creation also sets shipping to zero.

Decide the actual shipping policy, add a merchant-configurable fee/threshold if needed, and use one server calculation for checkout, order creation and payment verification. No real order below ₹499 was submitted; this finding combines observed checkout wording and local implementation evidence.

### 4. Refund promise differs between pages — high, content accuracy

Combo pages promise: “get a refund at your door” for anything not up to standard. Returns policy instead describes eligible returns within seven days and approved refunds to the original payment method within 3–5 business days.

The combo guarantee is hardcoded in the shared bundle-detail view. Replace it with merchant-controlled wording consistent with the actual returns policy. Define the handling for food items, damaged parcels and COD purchases using the merchant's real policy.

### 5. Green theme contrast remains inconsistent — medium/high, usability

The Contact page has very low-contrast dark supporting text and field labels against olive green. Combo breadcrumb text is also low contrast. In the mobile combo sticky purchase bar, the quantity text is extremely pale against its light background. These were visually confirmed in screenshots.

Fix the Contact page, combo detail view, checkout and shared sticky purchase controls together; changing only the homepage palette misses shared views. The primary product detail page and cards are generally much more readable.

### 6. Empty checkout still looks ready to place an order — medium

An empty cart correctly shows an empty state in the drawer. However, visiting `/store/crunchiko/checkout` directly shows the full form and an enabled `Complete Order • ₹0.00` button, with a zero-value summary.

Show an empty-checkout state or redirect to shopping. Disable order submission until there are valid items. This audit did not submit the empty form, so it does not claim the backend accepts zero-item orders.

### 7. Review verification labels are unconditional — medium, trust

Every review in the checked shared component gets a `Verified Customer` label; the template does not condition it on a matched purchase. The audit cannot determine whether these client-entered reviews are genuine.

Use a recorded verified-purchase signal or a neutral label such as Customer Review. Validate the provenance of existing reviews separately; do not infer authenticity from a five-star display.

## Additional improvements

### Combo metadata and naming

Different combo URLs currently share `Combo | Crunchiko` and the store-logo sharing image. Use each combo's actual title, description and image. A pack-of-two detail page is labeled `BUNDLE`, `Value Pack Combo` and `Weekly Combos`; the homepage correctly labels it `Pack of 2`. Use consistent pack/combo names.

### Food-store copy

- Hero CTA says `Explore Crunhiko` — misspells Crunchiko.
- Contact text mentions “fresh produce” even though this catalog sells packaged snacks.
- Product description uses “vacuum-dried” while the brand markets vacuum-fried chips.
- Product delivery section says to contact the store despite an estimated date and a published shipping policy.
- Checkout says “Review your fresh items”; checkout step numbering goes 1, 2, 4 when scheduling is disabled.

Use consistent merchant-specific copy. Check that claims such as FSSAI Certified, 100% natural and nutritional statements have supporting merchant records. No independent verification of those claims was performed.

### Sitemap coverage

The sitemap index and all child XML files return 200. Current contents: four general pages, three product URLs and two collection URLs. The directly linked `/combos`, individual combo pages and policy pages are absent. This is not a crawl block, but decide which of those public pages should be indexed and include their canonical URLs consistently.

### Mobile polish

Product gallery thumbnails expose a visible horizontal scrollbar. The portrait mobile hero occupies much of the initial viewport; this matches the requested 800 × 1200 ratio, rather than being a rendering bug. Consider a shorter mobile banner only if faster access to products is preferred.

The Instagram embed displays the public reel in the checked browser, including Instagram's own header and controls. It is not a custom autoplay player; availability for other users remains controlled by Instagram.

### Policies and business information

Terms are one short generic paragraph. Footer links platform privacy, not an explicitly store-specific privacy explanation. Confirm the business/contact address and actual delivery/refund terms with the merchant before expanding these pages. This audit makes no legal-compliance determination.

### Security headers — limited observation

The checked homepage returned `X-Content-Type-Options: nosniff`, but did not include HSTS, CSP, X-Frame-Options or Referrer-Policy headers. This alone does not prove an exploitable vulnerability. Review policy choices against custom domains, preview frames, payment gateways and Instagram embeds before adding restrictive headers.

## Checks that passed

- All twenty initial public-page requests returned expected 200 statuses, except the deliberate missing URL, which returned 404.
- No horizontal page overflow at 1440 or 390 px on homepage, product 82, combo 9, Contact or About.
- No broken top-level image elements or uncaught main-window JavaScript errors on those pages in the checked browser.
- Store search with `q=jackfruit` returns Natural Jackfruit Chips only.
- Selecting the 250g variant updates the card to ₹399 and the add action targets `82-250g` with quantity increment 1; server cart writes were stubbed.
- Combo prices checked: ₹549 versus ₹559; pack of two ₹749 versus ₹798; Ultimate Crunch Pack ₹999 versus ₹1,097.
- Empty cart drawer has an appropriate message and Continue Shopping action.
- Share thumbnail now returns 200, `image/png`, 18,656 bytes, with a correctly rendered 200 × 200 Crunchiko logo. The previous thumbnail 500 error was not present. WhatsApp's cached/final layout was not tested.
- Homepage canonical is the Crunchiko domain. Product canonical includes the correct product identifier.
- `robots.txt` permits crawling and points to the live store sitemap. No homepage noindex header/meta was observed.
- Top-level resource sample included WebP product/banner files around 160–180 KB. This is not enough evidence for a speed rating.

## Checkout limitation

The live empty checkout exposed COD only. This does not establish that Razorpay/Paytm/PhonePe are broken: they may not be enabled/configured for this merchant. No gateway credentials, real payment, paid callback, tax-bearing cart, successful order, email/push notification, invoice or shipment workflow was exercised.

## Suggested order

1. Correct JSON-LD output, remove random sales claims, and align delivery/refund behavior with merchant policy.
2. Fix Contact/combo/checkout contrast and empty-checkout handling.
3. Correct review-verification labels, combo metadata and inconsistent copy.
4. Broaden selected sitemap coverage and perform a merchant-approved real checkout/payment test.

Public evidence pages: homepage `/`; product `/store/crunchiko/product?id=82`; pack `/store/crunchiko/combo?id=9`; Contact `/store/crunchiko/contact`; shipping `/store/crunchiko/shipping-policy`; returns `/store/crunchiko/return-policy`; checkout `/store/crunchiko/checkout`.

## Local fixes prepared after this audit

The requested shared/theme fixes are implemented locally, pending deployment and a new live check:

- Storefront JSON-LD is serialized in the PHP service, preserving the literal schema.org context and escaping embedded content.
- Random purchase counters are removed from the shared standard bundle cards.
- Reviews use neutral Customer Reviews / Customer review labels; no purchase-verification claim is invented.
- An empty or entirely unavailable cart receives a dedicated empty-checkout view; valid carts retain the normal checkout.
- Theme 31 Contact labels/support text, combo breadcrumb/quantity controls and checkout surfaces have explicit contrast styles.
- Combo metadata uses the actual combo title, description and cover (or first product image fallback).

Shipping and refund behavior/content remain unchanged at the user's explicit request. Merchant-saved copy, sitemap expansion and other optional audit observations are outside this patch. Tests cover actual rendered JSON-LD keys, combo metadata, empty/valid carts and neutral review labels. Payment regression tests use mocked gateways, not real charges. Desktop/mobile checks used isolated fixture data rather than modifying the live store.
