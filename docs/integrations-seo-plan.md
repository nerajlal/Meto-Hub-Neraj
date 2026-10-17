# MetoHub integrations, SEO and AI search plan

Date: 16 October 2026  
Status: Proposed implementation plan; documentation only.  
Scope: Task19-Grocery merchant storefronts, all 15 themes, store slug URLs and verified custom domains.

## 1. Goal

Give Instagram sellers a simple way to connect their own analytics and advertising tools, customize how their store appears in search and social shares, and publish technically sound pages that search engines can understand.

The reference image suggests two product areas:

1. Integrations: Google Analytics 4, Meta Pixel, Google Tag Manager, Microsoft Clarity and Google Search Console verification, followed by Google Ads conversion tracking.
2. Search readiness: editable metadata, correct URLs, structured data, sitemaps, crawl controls, useful merchant content and measurable technical checks.

Do not promise rankings, AI citations, automatic advertising campaigns or the screenshot's 95%/83% scores. These require actual measurement, and search visibility remains outside MetoHub's control. AIEO here means making accurate store content accessible and understandable to search and AI discovery systems.

## 2. Starting point found in the repository

- Some layouts already output descriptions, canonical links and social sharing tags. Their consistency across active theme render paths needs auditing.
- Product JSON-LD exists in `template_1/product-main.blade.php` and `template_2/product-main.blade.php`. The sampled markup interpolates strings manually, uses a canonical without product query parameters and declares stock availability statically. Replace this with shared, safely encoded data derived from the actual product and selected public URL.
- The current `public/robots.txt` advertises `https://www.goslot.store/sitemap.xml`. This must be replaced with host-aware MetoHub/store rules.
- Public store routes use `/store/{store_slug}`; legacy query-based URLs and custom domains must resolve to one canonical identity per store and product.
- The initial search did not find a shared implementation for GA4, GTM, Meta Pixel or Clarity. Confirm active theme layouts before implementation; do not assume every similarly named legacy layout is used.
- SaaS subscription billing is separate from merchant customer order tracking. Storefront checkout currently uses COD.

## 3. Merchant administration

Add two dedicated pages linked from Storefront settings:

| Proposed page | Merchant controls |
| --- | --- |
| `/{tenant}/admin/settings/integrations` | Enable/disable each provider, enter public IDs, choose direct or GTM-managed tracking, review setup steps and test status |
| `/{tenant}/admin/settings/seo` | Store title/description, social image, public indexing preference, Search Console verification and technical checklist |

Add optional SEO title, description and social image overrides to product and collection editors. Use sensible store/product-derived defaults when left blank. Show search/social previews as approximations, with character guidance rather than claims of guaranteed display.

Connection states must distinguish Disabled, Configured and Verified. Saving an ID only means Configured. Verification requires a successful external check or a clearly labelled merchant-confirmed test. Show the last check time and actionable errors. Provide a top Save Changes button consistent with existing settings pages.

## 4. Provider integration design

| Provider | First release | Verification |
| --- | --- | --- |
| GA4 | Merchant measurement ID; page and ecommerce events | Merchant checks DebugView and reports |
| Meta Pixel | Merchant pixel ID; mapped standard browser events | Merchant checks Events Manager test events |
| GTM | Merchant container ID and documented data layer | Merchant uses Tag Assistant/container preview |
| Clarity | Merchant project ID; consent-aware loading and masking | Merchant verifies recordings in their project |
| Search Console | Safe HTML verification token and sitemap instructions | Merchant completes verification in Search Console |
| Google Ads | Conversion ID/label and approved conversion trigger, after base events are stable | Merchant validates using provider diagnostics |

Support direct installation or GTM-managed installation. Clearly choose who sends each provider's events; prevent duplicate direct/GTM installation for the same destination. Ads setup does not create campaigns, budgets or charge advertising accounts.

Search Console is a verification and reporting product, not a storefront tracking script. For stores on the shared MetoHub domain, investigate URL-prefix property verification scoped to the store path. Do not grant a seller DNS/domain-property access to the platform domain. Custom-domain DNS verification remains under that domain owner's control.

GTM can execute merchant-controlled code. Initially restrict it to verified merchant custom domains, after reviewing shared-origin session risks and CSP requirements. Do not offer arbitrary scripts or raw HTML snippets on shared `/store/...` hosting. Direct integrations accept validated provider identifiers only.

## 5. Shared architecture and data

Implement one tenant-aware integration renderer and one event interface shared by all active theme layouts. Theme JavaScript calls the interface after confirmed actions instead of embedding provider-specific code in every card.

Suggested storage:

- `store_integrations`: tenant, provider, enabled flag, validated public configuration, installation mode, verification state and check timestamps; unique tenant/provider.
- Store SEO settings: title/description/social image overrides and indexing preference, following the project's existing settings persistence conventions.
- Product/collection SEO overrides using existing ownership and validation rules.
- Consent preference in a store-scoped browser record, including policy version and expiry.
- Future server conversion outbox: tenant, order, destination, stable event ID, status, attempts and timestamps, unique per destination/conversion.

Resolve the store before reading settings; every query, cache key, preview and update must be tenant-scoped. Keep merchant settings separate from platform landing-page analytics. Public tracking IDs may reach the browser; any future provider access tokens must be encrypted server-side and redacted from logs and settings responses.

## 6. Event contract

| Internal event | When emitted | GA4 example |
| --- | --- | --- |
| Product list viewed | Visible collection/list rendered | `view_item_list` |
| Product viewed | Product detail successfully loaded | `view_item` |
| Added to cart | Cart mutation succeeds | `add_to_cart` |
| Removed from cart | Removal succeeds | `remove_from_cart` |
| Checkout started | Checkout actually begins | `begin_checkout` |
| Order placed | Server confirms a new order | Custom `order_placed` initially |
| Purchase confirmed | Approved business milestone occurs | `purchase` |

Use actual item/variant IDs, quantities, currency, discounts and server-calculated totals. Transaction/event IDs must include a stable tenant namespace and order reference. Never emit purchase from an Add button, a failed request, a query string or an unverified success page. Refreshing confirmation pages must not repeatedly create conversions.

For COD, an order placed is not collected revenue. First release should distinguish `order_placed` from paid/delivered purchase reporting. Decide the purchase milestone explicitly before advertising conversion rollout. Fulfilment/payment confirmation events may require a later consent-aware server integration; do not mislabel outstanding COD totals as paid revenue. SaaS subscription payments must never appear in a seller's sales analytics.

Meta Conversions API and server-side GA4/Ads tracking belong to a later phase. Review official APIs, consent propagation, credentials, queue retries and browser/server deduplication before implementing them.

## 7. Privacy, isolation and performance

- Introduce a store-scoped consent interface with Accept, Reject and Manage choices. Default nonessential analytics/advertising off until consent; support withdrawal and prevent new tracking after withdrawal. Review regional requirements before rollout.
- Do not send names, email addresses, phone numbers, addresses, checkout fields or private URLs to analytics. Sanitize query parameters and referrers; store paths can otherwise expose personal data.
- Session recording must mask forms and sensitive content and exclude checkout, account, authentication and order-detail pages by default.
- Shared slug stores share an origin: consent, browser storage, cart state and destination selection must remain isolated between stores.
- Restrict integration editing to authorized merchant owners/roles, apply CSRF protection, escape metadata and serialize JSON-LD with a proper JSON encoder.
- Load permitted scripts asynchronously once. A blocked provider, ad blocker or slow network must not break browsing/cart/checkout.
- Audit CSP allowances per enabled provider; avoid broad script permissions just to make an integration work.

## 8. SEO and AI search foundations

### URLs and crawling

Build a single public URL service for canonical tags, Open Graph URLs, JSON-LD, sitemaps and internal links. Preserve product identity: a route such as `/product?id=27` must not canonicalize every product to `/product`. Prefer descriptive product/collection slug routes with backward-compatible redirects after checking existing links.

Choose the preferred verified custom domain when configured; otherwise use the platform store slug URL. Redirect aliases where safe, retain meaningful pagination and handle missing/unpublished products correctly. Track store/product slug changes for permanent redirects. Never expose draft or cross-tenant URLs.

Generate platform and tenant-scoped XML sitemaps containing only canonical, published, indexable pages. Custom-domain sitemaps must remain on that host. Cache and invalidate on publication, deletion, slug or domain changes; split large sitemaps when required.

Serve host-aware robots rules and the correct sitemap URL. Exclude admin/auth/account/cart/checkout/internal search from indexing through appropriate authentication and `noindex` directives. Robots disallow alone is not access control and does not guarantee removal from search. Apply sitewide noindex to staging and unpublished stores, with intentional production defaults.

### Metadata and content

Provide unique store/product/collection titles and descriptions, social images with absolute HTTPS URLs, one descriptive main heading and meaningful image alt text. Remove unrelated brand or grocery-only defaults from fashion and other stores.

Create shared Product/Offer, BreadcrumbList and appropriate Organization/Store structured data. Derive prices, currency, availability, variant details and URLs from real catalog state. Include genuine reviews only if that feature and records exist. Do not fabricate ratings, local addresses, shipping or return policies.

Encourage merchant-written descriptions, accurate sizing, delivery/returns/contact information and useful collection introductions. Server-render essential catalog content and links. Improve image sizing/loading, layout stability and measured page performance without changing the themes' visual identities.

Google states that established SEO practices also apply to its AI search features. There is no separate required AI markup for inclusion. Treat `llms.txt` as an optional later experiment, not an indexing requirement or an AI ranking guarantee.

## 9. Readiness dashboard and marketing

Show factual checks such as canonical present, valid product schema, sitemap available, unique description and last measured performance. Each check needs its method, status, affected pages and timestamp. Start with Passed/Needs attention/Not checked rather than inventing a composite score.

Do not display SEMrush branding, the reference screenshot's scores or “Analysed using SEMrush” unless an actual authorized service/report supports it. A future external audit integration must account for provider costs, quotas, credentials and report freshness. Never equate an internal checklist with an external SEO or AI visibility score.

Suggested landing copy after the corresponding features ship:

> Connect your analytics and advertising tools. Add your own tracking IDs and measure store activity.
>
> Search-ready storefronts. Customize metadata and publish clear product information with technical SEO foundations built in.

Do not advertise an integration as available before implementation and end-to-end verification.

## 10. Delivery sequence

1. **Audit and URL foundation:** map all 15 themes' active layouts, fix canonical identity and incorrect robots host, design redirects and shared metadata/schema services.
2. **Merchant SEO controls:** store/product/collection fields, previews, shared rendering, robots, sitemaps and publication controls.
3. **Consent and direct integrations:** isolated settings, GA4, Meta Pixel and Clarity; safe loading and a common ecommerce event contract.
4. **Advertising and verification:** Search Console guidance, Google Ads setup, diagnostics and GTM on eligible custom domains.
5. **Readiness and release:** real checklist, performance measurements, merchant help, gradual rollout and accurate landing-page messaging.
6. **Optional advanced work:** server conversions, OAuth/report imports, external SEO audits and measured AI discovery experiments.

Do not change subscription prices or promise plan availability as part of this plan. Recommended starting policy is technical SEO foundations for every store; decide integration entitlements explicitly and enforce them server-side if tiered. Accounts, consent text and conversion milestone choices are rollout inputs, not reasons to begin charging users automatically.

## 11. Acceptance and rollout

- Check every theme on mobile and desktop: correct tenant metadata/schema, one loader per destination and identical event semantics.
- Test two stores consecutively on the shared host and on verified custom domains; no tracking IDs, consent or data may leak between stores.
- Test rejection, acceptance, withdrawal, provider failure and ad blockers. Reject means no nonessential tracking requests.
- Check valid/invalid provider IDs, permissions, injection attempts and absence of private customer data in network payloads.
- Confirm cart events only after success, real totals, COD status separation and conversion deduplication on reload/retry.
- Validate JSON-LD with quotes, Unicode and HTML descriptions; compare stock/prices against visible content.
- Check product query IDs, legacy URLs, redirects, pagination, unpublished/deleted products, domain switches, sitemaps and staging noindex.
- Verify with provider test tools and search validators using test stores before merchant rollout. Record actual results and performance before/after.
- Feature-flag rollout; independently disable a provider without disabling storefront checkout. Rollback must not delete merchant configuration or customer orders.

Definition of done: merchant-editable settings, shared support across every active theme, verified tenant isolation and consent, correct crawlable catalog URLs, honest diagnostics, operational documentation and passing tests. Production provider verification remains a separate recorded release step.

## 12. Official references

- [GA4 ecommerce and purchase setup](https://developers.google.com/analytics/devguides/collection/ga4/set-up-ecommerce) — event implementation and transaction identifiers.
- [Google AI features and websites](https://developers.google.com/search/docs/appearance/ai-features) — relationship between SEO and Google AI discovery.
- [Microsoft Clarity Consent V2](https://learn.microsoft.com/en-us/clarity/setup-and-installation/clarity-consent-api-v2) — provider consent signalling.

Recheck current official Meta, Google Tag Manager, Google Ads and Search Console documentation during implementation. This plan does not assert provider account eligibility or completed external verification.

## Implementation progress — 16 October 2026

Search readiness has been implemented locally: merchant/store/product/collection metadata settings, shared support for all 15 active themes, identity-preserving canonicals, safe product/organization/breadcrumb structured data, host-aware robots/sitemaps, noindex controls and content checks. See [search-readiness.md](search-readiness.md) for exact scope, deployment and verification.

Descriptive product URL migration, verified cross-host canonical consolidation, external SEO audits and performance scoring are later work. No ranking or AI visibility scores are reported. The next integration sequence is GA4, GTM, Search Console verification, then Meta Ads measurement, with consent and tenant isolation first.

Integration settings are now implemented locally for GA4, GTM, Search Console HTML verification and Meta Pixel. See [store-integrations.md](store-integrations.md) for the consent model, domain eligibility, event contract, exact supported features and provider verification still required. OAuth/report imports, paid Purchase conversions and server conversion APIs remain future phases.
