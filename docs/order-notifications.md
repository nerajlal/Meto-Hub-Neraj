# Store owner order notifications

Implemented locally on 17 October 2026. Scope: this Laravel project, merchant email, admin inbox and standard browser Web Push. Native Android/iOS projects under `mobile-apps/` are not modified.

## Owner experience

Open the bell in the admin header, then **View all & device settings**, or `/{tenant}/admin/notifications` (for example `/2/admin/notifications`).

- New checkout and manually created admin orders produce one stored alert for the registered store owner (the oldest admin account in that tenant).
- The bell refreshes while the admin is open: normally every 20 seconds, less frequently in the background. It shows unread counts, recent alerts and links to the full history. Existing pending-order and stock summaries remain available.
- Owners can mark one/all alerts read. Opening an order through an alert or its email marks it read through an authenticated POST after the page opens; link previews do not consume unread state.
- An owner email goes to the registered admin account email, not the customer's email or the public storefront contact email. It includes order/items/total and a protected order link. The existing customer summary is separate.
- Device alerts are optional. Click **Enable on this device** and grant browser permission. **Disable on this device** unsubscribes that browser; the device list can disable old devices. Permission is never requested automatically on page load.
- The notification bar shows a generic MetoHub new-order message without customer name, address, phone or email. Clicking opens the protected order route. If login has expired, the common login resumes only a same-origin admin path within the authenticated owner's store.

Notification history is generated for new orders after deployment. There is no historical email/push blast for existing orders. The in-app alert remains available independently of email or push delivery success.

## Device requirements

Web Push uses the browser Push API, an application service worker and VAPID keys. No Firebase project or native-app store release is introduced by this implementation.

- Public deployments require HTTPS; localhost can be used for development.
- Supported browsers must expose Service Worker, PushManager and Notification APIs.
- On iPhone/iPad, add the admin page to the Home Screen, open that installed web app and enable notifications there. The admin manifest and icons are included. This does not add an offline order editor or cache authenticated pages.
- Delivery while the admin is closed depends on the browser, OS background support, permission, network, battery/Focus settings and worker availability. Browser/OS settings control sound and presentation. Force-stopped/unsupported environments are not guaranteed to receive alerts.
- Subscriptions belong to a specific browser profile/origin/store/user. Re-enable on each device and after moving to a different platform domain. Up to ten active devices are allowed per owner. Do not rotate VAPID keys routinely; existing devices need to resubscribe if those keys change.

The worker is scoped to `/{tenant}/admin/`, has no fetch handler and never caches admin/customer responses. Browser keys/endpoints are encrypted in the database with the existing Laravel `APP_KEY`; preserve it. The API rejects arbitrary endpoints, non-HTTPS endpoints and malformed keys. Only supported browser push-service hosts are allowed and redirects are disabled when sending.

## Delivery architecture

The order and its alert are stored in the same database transaction. Rollback creates neither a persistent alert nor a delivery job. After commit, a small job containing the alert ID is queued on the `order-notifications` queue. No owner email/push request is made inside checkout.

Email and per-device push results are stored separately. A failed channel does not erase the in-app alert or repeat a channel already marked successful. Retries use increasing delays, up to six attempts; failed/expired devices are disabled. The owner screen shows Pending/Sent/Needs attention for email.

A scheduler recovery command finds due records and expired enqueue leases. Cache locks prevent concurrent attempts for the same alert. SMTP/push delivery is at least once: a process crash after a provider accepts a message but before its result is stored can cause a duplicate on retry. Push tags help replace repeated device alerts. SMTP acceptance is not proof of inbox placement, and push-service acceptance is not proof of a visible OS alert.

## Production deployment

Back up the database and deploy the full release, including `composer.json`, `composer.lock`, the migration, models, services, job, commands, routes, views, notification JS, worker, manifest route and icons. This release adds a real production dependency: `minishlink/web-push` and its locked dependencies.

```sh
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan optimize:clear
php artisan migrate --force
```

Migration: `2026_10_17_000001_create_merchant_order_notifications`.

Configure a real SMTP/delivery mail provider and authorize `info@metora.in` as described in the existing production guide. `log`/`array` mailers are deliberately not treated as successful owner email delivery. Confirm the store owner's account email is valid and verified when verification is required.

Generate VAPID keys **once on the server**:

```sh
php artisan notifications:vapid-keys
```

Copy the printed values into the existing protected server `.env`. Do not share the private key in chat or commit it:

```env
ORDER_NOTIFICATIONS_QUEUE_CONNECTION=database
DB_QUEUE_RETRY_AFTER=300
WEBPUSH_VAPID_SUBJECT=mailto:info@metora.in
WEBPUSH_VAPID_PUBLIC_KEY=generated-public-key
WEBPUSH_VAPID_PRIVATE_KEY=generated-private-key
```

Local development keys were generated without displaying them and remain in the ignored local `.env`; they do not configure the server. Never regenerate `APP_KEY` to set up push.

Then rebuild caches and restart existing Laravel workers so they use the release/configuration:

```sh
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

Ensure PHP has curl, mbstring and OpenSSL. The notification-only worker `/merchant-push-worker.js` and icon files must be publicly accessible from the same origin. Do not redirect the worker to login. Configure any host CSP/connect policy for the actual browser and provider requirements without allowing arbitrary remote scripts.

### Queue worker

A worker must listen to the **order-notifications** queue; a worker listening only to `default` will not deliver these alerts. Recommended supervised process, from the project directory:

```sh
php artisan queue:work database --queue=order-notifications,default --sleep=1 --tries=1 --timeout=240
```

Keep the database queue retry interval above the job timeout (`300 > 240`). Use a shared persistent cache for locks when running multiple workers. Sync/null queue drivers are rejected so owner delivery cannot run inside checkout. Configure the process manager to restart the worker and run it as the normal application user.

On shared hosting without a supervised worker, use a host cron entry to drain notifications, with an overlap lock such as `flock` if the host supports it:

```sh
php artisan queue:work database --queue=order-notifications --stop-when-empty --max-time=50 --tries=1 --timeout=240
```

Run that command every minute from the actual project path. Use host-specific absolute PHP paths and a lock; a long job can outlast a cron interval. Shared-hosting cron adds latency compared with a persistent worker.

### Scheduler

The existing server cron must run `php artisan schedule:run` every minute. Laravel schedules `notifications:deliver-orders` every minute with overlap protection. This command queues due outbox records; it does not replace a queue worker.

```sh
php artisan notifications:deliver-orders
php artisan schedule:list
```

After fixing a delivery configuration issue, retry one undelivered alert explicitly:

```sh
php artisan notifications:retry-order ALERT_ID
```

Use the notification database ID, not the displayed order number. Channels already marked sent are preserved; expired/disabled devices must be re-enabled through the owner UI. Review failed jobs and outbox status when diagnosing problems. The UI and `app:preflight` cannot prove a supervised worker or cron process is running.

## Validation

Feature tests cover committed checkout, rollback, owner-only recipients, email rendering, retries, private feed/read/device operations, expired trials, encrypted subscriptions, endpoint validation, expired/reassigned devices, safe login return paths and scheduler recovery.

Browser QA uses mocked provider/permission/subscription APIs: the bell loads, layouts fit 375/768/1440 pixels, permission occurs only on an explicit click, the worker scope is correct and enable/disable requests are made. Owner email layout is inspected on mobile and desktop. A repeatable worker suite runs the actual worker code with mock browser APIs:

```sh
python3 tests/browser/run_merchant_push_worker.py
```

No real owner email or OS push notification was sent by these tests. Before enabling for merchants:

1. Place one controlled COD order and check the owning store's bell, email inbox and phone notification bar; verify another store receives nothing.
2. Close the dashboard and repeat with a consented device; click the OS alert, including after a session expires.
3. Test a denied permission, an unsubscribed device and iPhone Home Screen flow on actual devices.
4. Confirm scheduler/worker execution, retry behavior and email delivery authentication/inbox placement.

The dependency installation also reported existing project advisories. See [dependency-audit-2026-10-17.md](dependency-audit-2026-10-17.md); the added Web Push dependencies had no advisory entries in this check. The broader dependency audit is separate from notification behavior and should be addressed before claiming the entire platform is production-cleared.

## Provider references

- [Official PHP Web Push library](https://github.com/web-push-libs/web-push-php).
- [MDN Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API).
- [Apple/WebKit Home Screen Web Push](https://webkit.org/blog/13878/web-push-for-web-apps-on-ios-and-ipados/).
