# Search readiness: setup and deployment

Implemented 16 October 2026. Scope: merchant search settings and storefront technical SEO. Tracking integrations and Search Console verification are available separately in Settings → Integrations; see [store-integrations.md](store-integrations.md).

## Merchant setup

Open **Settings → Search Readiness**, or `/{tenant}/admin/settings/seo` (for example `/9/admin/settings/seo`). It is also linked from Storefront Pages settings.

- Set the store search title, description and optional public social image URL.
- Leave overrides empty to use store/catalog defaults. Product and collection create/edit pages have their own overrides.
- Use the indexing checkbox to request inclusion or exclusion. Disabling it adds `noindex` and removes the store's URLs from sitemaps; the store remains publicly accessible.
- Readiness checks evaluate saved merchant content and configuration. They do not contact search providers or measure rankings. The search preview is approximate.

All 15 active themes use a shared metadata component: title, description, canonical, robots, Open Graph, Twitter cards and safely serialized JSON-LD. Product offers use catalog prices, currency and availability rather than a hardcoded in-stock value. No ratings or reviews are fabricated.

Product canonicals preserve the product ID. Collection canonicals preserve the collection slug. Tracking parameters are removed from canonicals; filtered/search and preview URLs are excluded from indexing. Existing theme URLs continue working and point to the canonical store slug URL. No product URL migration or automatic slug redirects are introduced in this release.

## Server deployment

Back up the database and deploy the complete change, including the new configuration, controllers, middleware, shared views and migration.

**Remove the old static `public/robots.txt` from the server when deploying.** This file has been deleted from the repository and replaced by a dynamic route. Upload tools that only overwrite files may leave the old file behind; that would bypass Laravel and keep advertising the wrong sitemap. Check for similarly overriding web-server rules/static sitemap files.

Keep the existing `.env` and `APP_KEY`. Confirm `APP_URL` is your actual public HTTPS platform URL. Add:

```env
# Public production installation:
SEARCH_INDEXABLE=true
```

Use `SEARCH_INDEXABLE=false` on private staging/test deployments. This server switch overrides merchant preferences, adds noindex response headers and excludes all pages from generated sitemaps. Indexing settings are not an access-control mechanism.

From the server project directory:

```sh
php artisan optimize:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

This release adds `2026_10_16_000001_add_search_readiness_settings`. No new Composer or npm dependency is required.

## Sitemap and crawl endpoints

- `/robots.txt`: dynamic host-aware rules and sitemap location; removes the old unrelated domain reference.
- `/sitemap.xml`: platform sitemap index, or that tenant's index on its enabled custom host.
- `/sitemap-platform.xml`: public platform pages on the platform host.
- `/store/{slug}/sitemap.xml`: one merchant's index.
- `/store/{slug}/sitemap/{pages|products|collections}/{page}.xml`: public page/catalog entries; catalog batches contain at most 2,000 entries.

Indexes point directly to sitemap documents, not nested sitemap indexes. Only active products and published collections are listed; stores that disable indexing are excluded. Generated XML escapes URLs and is streamed. Catalog changes appear without a cache invalidation job. Sitemap endpoints do not rely on a visitor's previous tenant session.

On an eligible custom host, URLs remain on that host; another store's sitemap cannot be retrieved through it. A configured custom domain is not promoted as canonical on the platform host merely because a domain string was saved: this release does not introduce domain ownership verification or force cross-host redirects. Platform and custom host canonical consolidation can follow a future verified-domain lifecycle.

Private admin, account, cart, checkout and authentication responses receive noindex headers. Search filters and theme previews are excluded from indexing. Store-level exclusion uses noindex on HTML and omits sitemap entries. Robots must still let crawlers fetch the HTML to observe noindex; exclusion can take time and is not guaranteed to be immediate.

## Release verification

1. Open the search settings page at mobile and desktop sizes. Save changes, refresh and confirm values persist.
2. Check page source on homepage, product and collection pages in each theme: one canonical and one JSON-LD block; correct store identity, product ID, metadata, stock, prices and currency.
3. Open all applicable sitemap/robots URLs. Ensure HTTPS and the correct host; confirm deleted/draft products, hidden collections and another tenant's URLs are absent.
4. Disable store indexing: verify noindex and sitemap exclusion; restore it for public launch. Check staging remains blocked independently.
5. Test sharing preview images and validate representative product markup using Google's Rich Results Test. Actual eligibility/indexing must be checked externally after deployment.

The feature tests exercise merchant isolation, validation, all 15 themes, actual product schema values, legacy product/collection routes, custom hosts, XML output, draft exclusions and indexing controls. Browser QA checks the settings layout at 375, 768 and 1440 pixels and its live text preview.

## Integration implementation

1. Google Analytics 4.
2. Google Tag Manager, with an explicit shared-origin security policy and duplicate tracking prevention.
3. Google Search Console verification and submission guidance.
4. Meta Pixel/Ads event measurement.

These integrations now have merchant settings and consent-gated support. See [store-integrations.md](store-integrations.md) for scope and external verification. No provider accounts have been automatically authenticated or verified.

## Reference

[Google AI features and websites](https://developers.google.com/search/docs/appearance/ai-features): standard SEO practices apply; this implementation does not guarantee AI citations or rankings.
