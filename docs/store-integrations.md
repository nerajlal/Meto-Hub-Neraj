# Store integrations

Implemented locally on 16 October 2026. Open **Settings → Integrations**, or `/{tenant}/admin/settings/integrations` (for example `/2/admin/settings/integrations`). Search Readiness also links to it.

## Available integrations

| Integration | Merchant supplies | Application behavior |
| --- | --- | --- |
| Google Analytics 4 | `G-...` measurement ID | Consent-gated page, product, cart, checkout and custom order-placement events |
| Google Tag Manager | `GTM-...` web container ID | Consent-gated container and ecommerce data layer on an eligible verified custom domain |
| Google Search Console | HTML verification token, without the surrounding meta tag | Token published on the exact store homepage; Google performs verification in its dashboard |
| Meta Pixel / Ads | Numeric pixel/dataset ID | Consent-gated PageView, ViewContent, AddToCart and checkout-link / Buy now InitiateCheckout events |

These are per-store settings, protected by merchant authorization. Public IDs and verification tokens are appropriate here; no OAuth login, provider API secret, advertising budget or provider account password is requested. Saving an ID means **Configured**, not externally verified or receiving events.

### Installation modes

**Direct** loads GA4 and Meta independently according to the visitor's choices. They work on the shared store slug URL. Turn off automatic/enhanced form measurement in the GA4 property and automatic advanced matching/events in Meta's settings; the application emits curated events and does not supply customer contact fields or advanced matching data.

**Tag Manager** loads only GTM. Direct GA4/Meta loaders remain paused even if their settings are retained. Configure and publish your destinations, tags and triggers in the GTM container yourself. This application cannot inspect an arbitrary container or prove its provider delivery.

Search Console metadata is independent of installation mode and visitor tracking consent.

## Consent and privacy

- No provider script is inserted before consent. Rejection does not load analytics or marketing SDKs, including no unconditional Meta image beacon or GTM noscript iframe.
- Analytics and advertising choices are separate in direct mode. GTM requires both because a merchant-controlled container may use either category.
- Choices are scoped to the store, expire after 180 days and are requested again when tracking destinations or installation mode change. Blocked browser storage keeps choices only for the current page.
- The visible Privacy choices button allows changes. Withdrawal disables outgoing application events and reloads the page to unload executing SDKs. Changes synchronize across tabs for the same store.
- GA4 uses a store-specific cookie prefix and host-only cookie configuration. Meta's SDK manages its own advertising cookies under Meta's policies; these identifiers are not claimed to be independent across stores on a shared host. Application configuration, events and consent preferences remain store-scoped.
- Free-text search/filter pages, previews, account/authentication pages and the native-app user agent do not load tracking. GTM and Meta are also excluded from checkout; direct GA4 can receive curated checkout/order-placement events there.
- The application sends catalog identifiers, quantities, prices, currency and curated page information. It excludes customer names, contact details, addresses, checkout form contents and private order URLs. GA4 receives a canonical page URL and blank referrer. Before loading providers, free-text campaign/query parameters are removed from the visible address; valid Google/Meta click IDs and store/product identity are retained. Third-party SDKs still process their normal browser/network data according to provider behavior and visitor consent.

## Search Console verification

1. Open Search Console in your own Google account and create a **URL-prefix** property matching the URL shown in Integrations.
2. For a shared store, use the trailing slash: `https://betametohub.metora.in/store/your-slug/`. This keeps the property within that store's path boundary.
3. Choose HTML tag verification. Paste only its `content` token, enable the integration and save.
4. Open that exact homepage URL and inspect its source for `google-site-verification`, then click **Verify** in Google's dashboard.
5. Submit the store sitemap shown in Integrations.

The token is emitted only on the trailing-slash store homepage or its custom-domain root. It is absent from legacy `/v1?tenant_id=...` links, product pages and the platform landing page. A token on a shared theme route would grant the wrong scope, so those routes deliberately do not publish it.

A custom domain needs its own Google property and matching token. This first release stores one active token per store; if switching properties, keep your previous Google property verified using another supported method. Domain properties require Google's DNS verification; the HTML token does not verify a Domain property. This release does not import search performance reports or automatically submit sitemaps through Google's API.

## GTM custom-domain eligibility

GTM executes merchant-controlled code and is never loaded on the shared platform origin. To activate it:

1. Connect an eligible custom domain through Domain Connection; current subscription/trial entitlement rules still apply.
2. In Integrations, click **Get DNS record**. Publish the displayed TXT record at `_metohub-verification.your-domain` with the complete displayed `metohub=...` value. Some DNS panels expect only the record prefix rather than the full hostname.
3. Click **Verify DNS ownership** after propagation. The server performs a DNS TXT lookup, not an HTTP fetch of merchant-controlled URLs.
4. Verification lasts 30 days. Keep the TXT record and repeat verification before expiry. A domain change or failed recheck removes eligibility; old verification does not authorize another hostname.
5. Set installation mode to Tag Manager, enable GTM and save the container ID.
6. Test on that exact custom host. Shared publication links, checkout and account pages do not load the container.

The platform requires host-only sessions (`SESSION_DOMAIN` unset/null). A wildcard session domain pauses merchant GTM containers. Reserved platform hostnames and parent platform domains cannot be claimed as custom domains. Valid public hostnames are required; IPs, localhost and raw URLs are rejected.

The DNS proof authorizes GTM on the connected host. It does not configure DNS A/CNAME records, HTTPS certificates or Google ownership verification for the merchant.

## Ecommerce event contract

The application uses a dedicated `mhDataLayer` for Google tags and GTM. GTM is loaded with `l=mhDataLayer`.

| Event | Trigger |
| --- | --- |
| `metohub_page_view` | GTM page context after consent; configure one page-view route in the container |
| GA4 `page_view` / Meta `PageView` | Direct installation, once per page after the relevant consent |
| `view_item` / Meta `ViewContent` | Product page with actual selected catalog variant |
| `add_to_cart` / Meta `AddToCart` | Successful server cart mutation with a positive quantity delta |
| `remove_from_cart` | Successful server cart mutation with a negative quantity delta |
| `begin_checkout` | Direct GA4 on checkout with nonempty basket; GTM/Meta on a checkout link or product Buy now action from a public page |
| `order_placed` | Successful server order response; custom GA4 event, distinct from paid revenue |

Items use tenant-namespaced product/bundle IDs, quantity, variant, currency and catalog prices. Cart snapshots refresh prices and discounts from the server; they do not trust a submitted tracking price. Basket-wide savings are allocated proportionally for event unit prices and may have rounding differences. The order event uses the actual saved order total. Failed requests emit no conversion. Events received twice by the JavaScript layer are deduplicated using event IDs; order-placement IDs are stable and also retained per store/provider within the browser session.

For GTM, configure triggers for these data-layer events and ecommerce variables. Use either your Google tag's automatic page view or the `metohub_page_view` trigger, not both. The application never loads a second direct tag in GTM mode. Container authors must configure their own consent checks and prevent duplicate destination tags.

COD order placement is not paid revenue. This release sends no GA4 `purchase` or Meta `Purchase`, no server Conversions API event and no SaaS subscription payment to merchant analytics. Collection/payment-confirmation conversions require a separate future implementation. Browser delivery is best effort and can be blocked or interrupted; this is not a financial ledger.

## Deployment

Back up the database and deploy all integration controllers, services, views, middleware, JS/CSS and the new migration. No new Composer/npm package or provider `.env` secret is needed.

```sh
php artisan optimize:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Migration: `2026_10_16_000002_create_store_integrations`.

Deploy the updated `public/.htaccess`: the shared store homepage trailing slash must be preserved for isolated Search Console properties. If using Nginx or host-level redirect rules, allow `/store/{slug}/` to reach Laravel without stripping its slash. SEO homepage canonicals and sitemaps now use that slash. Other existing store links continue to work.

If CSP is configured at the host, allow only the provider hosts needed by enabled integrations after reviewing each provider's requirements; do not broadly enable arbitrary script origins. DNS TXT lookup must be supported by the PHP host for domain verification.

## Validation performed locally

- Feature tests: permissions, settings validation, all 15 themes, Search Console scope and trailing-slash routes, private contexts, DNS proof, domain/host restrictions, real successful cart deltas and COD payload privacy.
- Browser checks at 375, 768 and 1440 pixels: responsive settings and consent dialog, no script insertion before consent or after reject, analytics-only vs advertising choices, event deduplication, foreign-tenant rejection, withdrawal reload and GTM exclusive mode.
- Browser QA intercepts provider loaders and does not send live analytics or advertising events. No real provider account has been externally verified by these checks.

Before rollout, test with your actual IDs and provider dashboards, a consented test visitor, successful/failed cart requests and a controlled COD order. Confirm no duplicate events in GA4 Realtime, GTM Tag Assistant and Meta Test Events. Complete Google ownership verification separately.

## Official references

- [Google consent implementation](https://developers.google.com/tag-platform/security/guides/consent).
- [Google tag cookie configuration](https://developers.google.com/tag-platform/security/guides/customize-cookies).
- [GA4 configuration reference](https://developers.google.com/analytics/devguides/collection/ga4/reference/config).
- [Google Tag Manager setup](https://support.google.com/tagmanager/answer/14842164).
- [Search Console ownership verification](https://support.google.com/webmasters/answer/9008080).
- [Meta's official Pixel tag template](https://github.com/facebook/GoogleTagManager-WebTemplate-For-FacebookPixel/blob/main/template.tpl) for consent, automatic configuration and per-pixel event calls.

## Flow review — 17 October 2026

The follow-up review fixed these cases:

- Tracking snapshot failures now skip analytics rather than interrupt storefront rendering or successful cart actions.
- Changing/removing a custom domain atomically clears its previous GTM ownership proof. Changing back does not restore old proof.
- A temporary DNS lookup failure reports an actionable error and preserves existing proof; a completed lookup with no matching TXT record invalidates proof.
- Checkout-link tracking checks the current origin and exact store checkout path and ignores cancelled clicks.
- Malformed/foreign event items are ignored safely. Clearing saved consent in another tab stops further application events and reloads the page.
- Integration assets use file modification timestamps in their URLs to avoid retaining an old browser-cached script after deployment.

Validation: 104 PHP tests / 3,049 assertions passed; route/view compilation passed. The dependency-free runtime suite runs the actual integration JavaScript in Chrome with browser/provider APIs mocked. No live analytics is sent by that suite:

```sh
python3 tests/browser/run_store_integrations.py
# Optional explicit browser executable:
python3 tests/browser/run_store_integrations.py --browser /path/to/chrome
```

This review adds no database migration or package dependency. Deploy the changed files and clear/rebuild Laravel's caches using the deployment commands above. External provider verification and event delivery remain checks to perform with actual merchant accounts.
