# MetoHub production deployment

Current canonical domain: `https://betametohub.metora.in`.
Future canonical domain: `https://metohub.cloud`.
Sender: `info@metora.in`.

## Configure the server

Use `.env.production.example` as a reference, preserving the existing production `.env`, database credentials, and `APP_KEY`. Never regenerate the key on an existing installation. Fill in the SMTP hostname, username, and password for the provider hosting `info@metora.in`; keep secrets on the server. Authorize this sender and configure the provider's SPF/DKIM records and your DMARC policy. Use SMTP port 587 with STARTTLS or port 465 with `MAIL_SCHEME=smtps`.

Serve only the project's `public` directory. Deny dotfiles and PHP execution inside `public/storage`. Keep `storage` and `bootstrap/cache` writable by PHP, without making the whole project world-writable. Enforce HTTPS at the hosting layer. When a reverse proxy terminates HTTPS, configure only the actual trusted proxy addresses; confirm generated links and redirects remain HTTPS.

The public upload disk is currently rooted directly at `public/storage`. Preserve that directory and its existing files during releases. Do not replace it with `storage:link` without deliberately migrating uploads and changing the disk root. Back up these files along with the database.

## Release and validate

Back up the database, uploads, and protected environment configuration first. Keep the prior release available for rollback. Run from the project directory on the server:

```sh
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan down --retry=60
php artisan optimize:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan app:preflight
php artisan up
```

If preflight fails, correct the reported settings before opening the application. It checks configuration, database/migrations, runtime permissions, and upload mapping; it does not send email or prove external provider availability. Current storefronts load public CSS/JS directly; if a release introduces Vite assets, build them with `npm ci` and `npm run build` before deployment.

Register a new merchant using a mailbox you control. Confirm the email arrives from `info@metora.in`, its link uses the beta domain, it verifies once, and password login works afterward. Test resend and expired links. With mail unavailable, signup must explain the delivery failure and block merchant access until verification.

Check all fifteen themes on mobile and desktop: catalog, variant selection, product images, cart totals, checkout, and admin settings. Place a real controlled cash-on-delivery order and confirm stock, order summary, and merchant notification. Online storefront checkout is intentionally unavailable until gateway creation, verified payment callbacks, retries, and refunds are implemented and tested. Platform Razorpay subscriptions are separate; configure their credentials and complete provider sandbox/live validation before accepting subscription payments.

## Operations

Schedule encrypted daily database and upload backups to storage outside the web server, with documented retention and a monitored success/failure result. Use a protected MySQL option file for backup credentials rather than putting passwords in shell command arguments. Periodically restore a backup to an isolated database and upload directory, and confirm merchant login, products, and orders there. Do not run recovery rehearsals against the live database.

Monitor `/up` externally, plus disk capacity, application errors, failed notifications, and database availability. `/up` proves Laravel boots, not that every dependency works. Run preflight during deployment and monitor real checkout/email failures separately. Set up the hosting provider's log alerts. If background jobs are enabled, supervise `php artisan queue:work --tries=3 --timeout=60` and restart workers after each release using `php artisan queue:restart`. Verification emails are currently sent synchronously, with a 15-second SMTP timeout.

Perform a representative concurrent browsing/cart/checkout load test on a staging copy of the production host. Measure response times and database contention before choosing capacity. A passing local test suite alone is not proof of production capacity or recoverability.

## Move to metohub.cloud

Provision DNS and TLS for the new domain first. Change `APP_URL` to `https://metohub.cloud`, `MAIL_EHLO_DOMAIN` to `metohub.cloud`, and Google callback settings if enabled. Update registered provider callbacks and permitted origins. Rebuild configuration and route caches, restart workers, and rerun preflight and the end-to-end checks above. Keep the sender `info@metora.in` if the mailbox provider still authorizes it.

Keep the beta host available temporarily with HTTPS redirects that preserve request paths and query parameters. Verification signatures are relative, so preserving the signed path/query and the existing `APP_KEY` lets unexpired links continue working across the redirect. Host-only session cookies mean users may need to log in again on the new domain.

The retired Zoho integration has no application routes, controller, model, UI, or active configuration. Its historical migration/table is retained so existing deployments can migrate safely; it does not enable the integration.

## Blog feed

Set `BLOG_API_ENDPOINT=https://blog.metora.in/api/v1/projects/blogs`, `BLOG_ASSET_URL=https://blog.metora.in`, and the MetoHub project's `BLOG_API_KEY` in the production server environment. The key belongs only in `.env`; do not put it into Blade or browser JavaScript. Rebuild configuration with `php artisan config:cache` after changing these values.

The API currently returns the MetoHub project with an empty `blogs` array. Publish articles associated with that project in the blog platform before expecting cards to appear. An empty successful response is shown as no published articles; an unavailable feed returns a temporary-unavailability message and HTTP 503 if no saved feed exists. Successful responses cache for five minutes, with the last successful feed retained for up to a day during outages. Article bodies and section HTML pass through an allowlist renderer before display.

## Public store links

Storefront publication links use `/store/{store-slug}`, for example `/store/fresh-grocery`. Catalog, product, and checkout routes keep the same store slug. Run the tenant-slug migration during deployment to populate existing stores; new stores receive slugs automatically. Slugs remain stable when business names change, and duplicate names receive unique suffixes. The admin header and theme/domain settings show the public link. Existing `/v1?tenant_id=9` links still work.

## Subscription lifecycle activation

Follow [billing-operations.md](billing-operations.md) for signed webhooks, historical subscription reconciliation, scheduler setup, cancellation and production payment validation. The release adds subscription, payment, invoice and checkout-reservation tables; run all new migrations before enabling the updated billing pages.

## Search readiness

Follow [search-readiness.md](search-readiness.md) for merchant SEO settings, indexing controls and sitemap verification. Run the 16 October search-settings migration. Remove the obsolete static `public/robots.txt` on upload-based deployments so Laravel's dynamic host-aware endpoint can serve the correct sitemap. Set `SEARCH_INDEXABLE=false` on staging; use the intended public setting on production and rebuild configuration cache.

## Merchant integrations

Follow [store-integrations.md](store-integrations.md) for GA4, GTM, Search Console and Meta Pixel setup. Run the integration-table migration and deploy the updated `.htaccess` so shared store homepage trailing slashes reach Laravel. GTM requires verified custom-domain ownership and host-only sessions. Provider IDs belong in each merchant's Integrations settings, not the platform `.env`.

## Store owner notifications

Follow [order-notifications.md](order-notifications.md) for owner order emails, unread bell history and browser/device Web Push. This release requires `composer install` from the updated lock file, the 17 October notification migration, server VAPID keys, a real mail provider, the scheduler and a worker listening on `order-notifications`. Store owners opt in separately on each device. Review [the dependency audit](dependency-audit-2026-10-17.md) before declaring the overall release production-cleared.
