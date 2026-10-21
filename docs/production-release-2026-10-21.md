# Production hardening release — 21 October 2026

## Application changes completed

- Manual-order customer IDs must belong to a customer account in the current store. Product IDs must belong to that store and be active; variants are resolved through the selected product.
- Manual orders lock product/variant rows and check/decrement stock inside the order transaction. Duplicate lines consume cumulative stock; a later failure rolls back the entire order. Merchant-entered selling prices remain supported. Explicit “continue selling when out of stock” settings are honored.
- New manual orders track variant reservations in existing order-item options. Cancelled creation reserves no stock; subsequent cancellation restores reserved stock once. Reactivation reserves again and fails if stock is insufficient. Both admin and assigned delivery-staff status actions use this logic.
- Historical/manual orders without reservation metadata are not backfilled or automatically restocked. Existing storefront-order cancellation behavior is unchanged. Review legacy inventory manually; there is no safe way to infer past stock movements from an old order alone.
- Manual order numbers use ULIDs instead of a small random number pool.
- Reel create/update/reorder validation is tenant-bound. Only HTTPS Instagram post/reel URLs are accepted; saved URLs are canonicalized without tracking queries. Storefront rendering skips unsupported historical URLs instead of embedding arbitrary hosts. Supported URLs do not guarantee Instagram will make a post publicly embeddable.
- Removed email-address and account-context authentication debug logging.
- Legacy Videos GET links redirect to Instagram Reels. Old video-save POST requests return 410. Historical video records are retained.
- Demo-request mail failure returns 503 rather than false success; the endpoint is throttled and uses the configured support recipient.
- Production preflight now fails when device-notification VAPID keys are missing.
- Updated Composer’s lockfile to patched compatible dependencies; see the dependency audit below.

No database migration or new environment variable is introduced by this pass. Deploy the complete application and **updated composer.lock**, not isolated PHP files. Run all existing migrations normally in case a prior release was not applied.

## Local verification

The release is validated with the complete PHP test suite, dependency smoke tests, Composer validation/platform checks and route/view cache generation. The test database is isolated SQLite in memory. Real MySQL concurrency, mail inbox delivery and external payment/push providers still require deployment verification.

Final result: **175 tests passed, 3901 assertions** after the dependency update. Route and Blade view cache generation succeeded. Composer validation and PHP 8.2 production platform checks passed. The landing/signup browser regression passed at 320/375/768/1440px using mocked server responses.

Browser regression checks cover the landing/signup flow with mocked responses; no real accounts or email deliveries are created. The dependency smoke tests generate a PDF, round-trip an XLSX file and generate signed Web Push headers locally without sending notifications.

## Deploy

Back up the database, public uploads and protected environment configuration. Preserve the existing APP_KEY and VAPID keys. Use a staging copy first, and preserve the previous release for rollback. Do not run fresh migrations or demo seeders on production.

From the application directory, deploy the complete reviewed release and then run:

```sh
php artisan down --retry=60
composer install --no-dev --prefer-dist --optimize-autoloader
composer check-platform-reqs --no-dev
php artisan optimize:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan app:preflight
```

Stop and fix any failed command before opening the site. Do not continue into checkout while dependencies are partially installed. Use the checked-in lockfile; **do not run composer update on the production server**. Do not copy a local vendor directory over the server’s installed dependencies.

After preflight succeeds:

```sh
php artisan queue:restart
php artisan up
```

Confirm a supervised worker is actually running with:

```sh
php artisan queue:work database --queue=order-notifications,default --sleep=1 --tries=1 --timeout=240
```

That worker is a long-running process, not a one-time release command. The process manager must keep it running. The notification queue retry interval must exceed its job timeout. Laravel also needs the server scheduler invoking `php artisan schedule:run` every minute. See [notification operations](order-notifications.md) for shared-hosting alternatives and VAPID setup, and [billing operations](billing-operations.md) for signed webhooks and reconciliation.

## Production go-live gates

- APP_ENV=production, APP_DEBUG=false, APP_URL=https://metohub.cloud and secure cookies. Protect beta with SEARCH_INDEXABLE=false.
- Serve only `public/`; verify HTTPS, proxy configuration, redirect paths, private-route access, storage permissions and backups/restoration.
- SMTP is configured for the authorized sender. Confirm verification and new-order email inbox delivery using a controlled test store.
- Confirm the notification worker, scheduler and push keys, then grant notification permission on a supported test device.
- In a provider test setup, verify subscription checkout, signed webhooks, cancellation and reconciliation. Configure live credentials only after these checks pass.
- Place one controlled COD order, verify the owner alert, stock reduction and printable invoice/label. Test a manual order and cancellation separately, then confirm there is no duplicate restock.
- Test concurrent reservations against the actual MySQL database using disposable test products. SQLite tests establish transaction rollback/state behavior but do not establish production database contention behavior.
- Confirm legal business details, tax wording and refund policy with the owner. Current order documents are not promised as statutory GST invoices.

These gates have not been executed on the production host from this workspace. Local tests and a clean dependency audit do not establish that production infrastructure or third-party delivery is configured correctly.
