# MetoHub Docker and VPS migration plan

Prepared: 22 October 2026. Project: Task19-Grocery. Public domain: https://metohub.cloud.

**Status: planning document only.** No Dockerfile, Compose stack, server configuration or application code is created by this document. Commands and file names below describe the future implementation and must be validated in staging before use. No production migration or deployment has been performed.

## 1. Objective and scope

Package the existing Laravel application into reproducible containers and move the production installation to a VPS while preserving merchants, customers, orders, subscriptions, uploads and credentials. Container recreation must preserve all business data.

Include the marketing website, blogs, SEO endpoints, all storefront themes, merchant admin, superadmin, checkout, invoice/label rendering, integrations, platform subscription billing, owner notifications and customer shipment emails. This is one multi-tenant application and one shared database; do not create a separate application container or database for every store.

Keep external services external: SMTP, Razorpay, the blog API, Google/Meta services, DNS and browser push services. Docker packages the software that talks to them; it does not replace those services. Mobile applications under `mobile-apps/` are separate clients and are outside this web deployment.

The first migration should preserve database-backed sessions, cache and queues. Redis, Kubernetes, multi-server scaling, new payment capabilities and a framework upgrade are separate projects.

## 2. Findings from this repository

| Area | Current implementation | Migration consequence |
| --- | --- | --- |
| Runtime | Laravel 12; Composer requires PHP ^8.2 | Build PHP-FPM and CLI with matching extensions; validate the locked dependencies on the selected PHP release |
| Dependencies | Composer lock file; Razorpay, Dompdf, PhpSpreadsheet and Web Push | Install production dependencies from the lock file during image build |
| Frontend | Vite 7/Tailwind 4 plus directly served files in `public` | Preserve all public assets; build Vite assets in a separate build stage |
| Database | MySQL connection configuration | Inventory actual production MySQL/MariaDB version, charset and collation before selecting target database image |
| Public uploads | Public disk is rooted at `public/storage` | Mount a persistent upload volume at that exact path |
| Private files | Local disk rooted at `storage/app/private` | Preserve private files separately; never expose this path through the web server |
| Sessions/cache | Production example uses database drivers | Preserve the relevant tables, key and shared configuration |
| Queues | Database; notification jobs use `order-notifications` | Worker must listen on that queue as well as `default` |
| Scheduling | Hourly billing reconciliation; minute owner and shipment alert dispatch | Run exactly one scheduler for the initial installation |
| Notifications | Owner email/push and customer shipment email | Preserve pending alert/job records and VAPID keys; configure SMTP and a worker |
| Search/blog | Dynamic robots, sitemap and external blog feed | Send these routes to Laravel; do not create static files that shadow them |
| Domains | `/store/{slug}` plus merchant custom domains | Preserve Host and HTTPS information; provision certificates for every enabled domain |

This review did not inspect production secrets or connect to the production database. Application tests passing locally do not establish VPS capacity or external service availability.

## 3. Proposed service layout

Use Docker Engine with the Compose plugin on a maintained Linux VPS. Use a single host initially, with immutable, release-tagged images. This is a single point of failure; backups and recovery are required even with restart policies.

```text
Visitors / Razorpay webhooks
           |
       DNS + HTTPS
           |
    edge: Caddy (80/443)
           |
    web: Nginx (internal)
           |
    app: PHP-FPM (internal)
           |
    db: MySQL-compatible server (internal)
           |
    shared database-backed jobs, cache and sessions

worker: same application image, CLI queue worker
scheduler: same application image, CLI scheduler
release: one-off application container for migrations/checks
backup: scheduled external backup process or isolated backup container
```

| Service | Responsibility | Important requirements |
| --- | --- | --- |
| `edge` | TLS, HTTP redirect, host routing | Persist certificate state; only service exposing web ports |
| `web` | Static assets and Laravel front controller | Only `public` is the document root; PHP forwarded exclusively to app |
| `app` | Laravel PHP-FPM requests | Same release as worker/scheduler; least-privilege runtime user |
| `worker` | Background email/push and other jobs | Graceful shutdown, adequate timeout, automatic restart |
| `scheduler` | Dispatch scheduled Laravel commands | One instance; same database/cache and application configuration |
| `db` | Persistent application data | Named volume, health check, least-privilege application user, no public port |
| `release` | Migrate, preflight, administrative release commands | Explicit invocation once; never an automatic migration on every app start |

Using Caddy for TLS and Nginx for application serving is the proposed baseline. If the VPS already has a managed reverse proxy, reuse that edge layer and avoid competing listeners on ports 80/443. Do not install an unnecessary second ingress stack.

## 4. VPS sizing and host setup

A planning starting point is 2–4 vCPU, 4–8 GB RAM and 80+ GB SSD, subject to real catalog/upload size and traffic measurements. This is an estimate, not a capacity guarantee. Larger images, PDF/XLSX generation and backups can require more resources.

Before provisioning, measure existing database and upload sizes, peak traffic, worker duration and expected growth. Maintain free space for database growth, two release images, backup staging and logs. Run a representative load test before deciding final capacity.

Host setup should include SSH keys, restricted administrative access, system security updates, time synchronization, Docker Engine/Compose, off-host backup credentials and external monitoring. Expose only required SSH access and ports 80/443. Do not publish MySQL, PHP-FPM or the Docker API. Check both host/provider firewalls and Docker port publishing behavior.

Prefer building images in CI and pulling them onto the VPS so builds do not compete with production requests. Produce images for the VPS architecture; a Mac build must not accidentally publish an ARM-only image for an amd64 VPS.

## 5. Files to create during the implementation phase

These are proposed deliverables, not files added by this documentation task:

| Proposed file | Purpose |
| --- | --- |
| `Dockerfile` | Multi-stage frontend/dependency build and production PHP runtime |
| `.dockerignore` | Exclude secrets, local dependencies, uploads, backups and unrelated mobile projects |
| `compose.yaml` | Shared service/network/volume definitions |
| `compose.production.yaml` | Production images, runtime permissions, resource/restart/log settings |
| `docker/php/production.ini` | OPcache, memory, upload limits and runtime error settings |
| `docker/php/fpm.conf` | Pool size, access/error logs and worker settings |
| `docker/nginx/default.conf` | Public root, static delivery, front controller and upload protections |
| `docker/caddy/Caddyfile` | Domain routing, HTTPS and trusted forwarding policy |
| `docker/entrypoint.sh` | Bounded readiness checks, runtime initialization and process execution |
| `docker/release.sh` | Explicit release steps with failure checks |
| `docker/backup.sh` | Backup, encryption, off-host transfer and result reporting |
| CI deployment workflow | Test, build, tag, scan, publish and deploy a release |
| Operations runbook | Final commands, recovery, monitoring and rollback procedures |

Exclude `.env*` secrets from image build context; retain only deliberately public example templates if required. Also exclude `.git`, `vendor`, `node_modules`, database dumps, local caches, logs, `public/storage` uploads and personal attachments. Review exclusions so required public images, Blade templates and catalog assets remain included.

## 6. Image and runtime design

Select a maintained PHP release compatible with Laravel and every locked dependency, and pin image versions/digests. PHP 8.4 is a candidate to validate in CI; the current Composer constraint permits it, but that alone does not prove compatibility. Do not couple this migration to a Laravel major-version upgrade.

Use stages for Composer, Node assets and the production runtime. Choose a Node version satisfying the locked Vite package engine requirements and run `npm ci` followed by `npm run build`. Copy `public/build` and directly served public assets into the resulting web/app images. Node and development servers are not needed at runtime.

Run `composer install --no-dev --prefer-dist --optimize-autoloader` against `composer.lock`, not `composer update`. Composer package discovery must work without connecting to production services. Build with a minimal nonsecret environment if needed; never bake the live `.env`, `APP_KEY` or config cache into a layer. Validate with `composer check-platform-reqs --no-dev` in the final runtime.

Required production extensions found in the lock file include ctype, curl, dom, fileinfo, filter, gd, hash, iconv, json, libxml, mbstring, openssl, pcre, session, simplexml, tokenizer, xml, xmlreader, xmlwriter, zip and zlib. Several are built into PHP. Additionally include PDO/MySQL for the database, OPcache for FPM, and pcntl/posix for worker signal handling; validate Web Push crypto support and consider GMP/intl where appropriate. Install image libraries needed for the formats actually used and fonts needed by invoices. Check PDF generation and spreadsheet export in the Linux image.

Application, worker, scheduler and release containers must use the same application image digest. Build Nginx's public assets from that release too. Do not mount the whole host checkout over production code or run Composer/npm on each startup.

Use an unprivileged application runtime, writable designated directories, restart policies and bounded log rotation. Drop unnecessary capabilities and keep the Docker socket out of application containers. Give workers a stop grace period longer than the 240-second job timeout. Tune PHP-FPM pool size to measured memory use.

## 7. Persistence and permissions

| Data | Proposed persistent location | Access |
| --- | --- | --- |
| MySQL data | Database named volume | Database service only |
| Public uploads | `/var/www/html/public/storage` | App/worker read-write; Nginx read-only |
| Private application files | `/var/www/html/storage/app/private` | Application processes only |
| Laravel maintenance marker | Shared `storage/framework` state or explicit coordinated maintenance storage | All application processes see the same maintenance state |
| Compiled Blade views | Container-local/release-specific writable directory | Avoid old and new releases sharing compiled views |
| Temporary files and `bootstrap/cache` | Container-local writable runtime paths | Rebuilt consistently for each service/release |
| Logs | Rotated container logs and/or retained protected log volume | Operators; not publicly served |
| TLS certificates | Caddy data/config volumes | Edge only; backed up securely |

**Do not run `storage:link` as a generic setup step.** This app's public disk uses `public/storage` directly, rather than the stock Laravel public-disk layout. Preserve that mapping and verify a real upload survives container recreation. Audit the source installation for any historical uploads in other directories before final sync.

Keep cache directories release-specific while coordinating maintenance state. A shared `storage/framework` mount must explicitly account for views and other generated caches rather than accidentally reusing old release files. Numeric UID/GID ownership must permit app writes without `chmod 777` or world-writable code.

Volumes are persistence, not backups. Never use `docker compose down -v` or indiscriminate volume pruning against production.

## 8. Environment and secrets

Use `.env.production.example` as an inventory and preserve the existing production `APP_KEY` exactly. Moving an existing installation is not a reason to generate a key. Preserve VAPID keys, credentials, provider plan mappings and tenant integration records.

Container networking changes the database hostname from loopback to the Compose service name:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://metohub.cloud
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_DOMAIN=null
CACHE_STORE=database
QUEUE_CONNECTION=database
ORDER_NOTIFICATIONS_QUEUE_CONNECTION=database
DB_QUEUE_RETRY_AFTER=300
SEARCH_INDEXABLE=true
```

These values are a partial configuration example; they are not a complete replacement `.env`. Supply the real database name/user/password securely. If the database is managed externally, use its private reachable hostname instead of `db` and configure required transport security.

Keep SMTP, Razorpay API/webhook secrets, blog key, Google OAuth and Web Push private key outside Git and images. Use a protected host environment file as a practical initial option; restrict access and remember privileged Docker operators can inspect container environments. Compose `--env-file` interpolation is distinct from passing variables into services; implementation must explicitly supply the intended runtime configuration to every application service.

Docker secrets mount files; existing Laravel environment settings do not automatically read `_FILE` variants. Choosing secrets files requires a deliberate loader/entrypoint implementation. Standalone Compose secrets must not be described as an automatically encrypted secret vault. Rebuild Laravel config caches with the real runtime environment after injection, separately for app/worker/scheduler/release as appropriate.

Staging must use separate data, keys, domains, test Razorpay credentials and a mail sandbox. Set `SEARCH_INDEXABLE=false`; prevent copied production alert queues from sending to real customers or devices.

## 9. Web server, TLS and tenant domains

Serve only `/var/www/html/public`. Send application requests through Laravel's front controller and deny dotfiles, private paths and arbitrary PHP execution. Uploaded files must never execute as PHP, including paths under `/storage`. Preserve dynamic `/robots.txt`, `/sitemap.xml` and `/llms.txt` routing. Nginx does not use the repository's Apache `.htaccess`; recreate necessary behavior explicitly, including store homepage trailing-slash handling.

Configure request/upload limits consistently at edge, Nginx and PHP. Set safe timeouts and test large product uploads. Cache versioned assets, but avoid shared caching of authenticated admin/customer pages, cart responses and private order data.

Preserve the validated original Host and HTTPS scheme through the proxy chain. Configure Laravel's trusted proxy handling for the actual topology; this is an implementation item because trusted proxies are not explicitly configured in the current bootstrap. Reject arbitrary hosts at the edge and verify forwarded headers cannot generate attacker-controlled links. Check secure cookies, signed verification URLs, redirects and canonical URLs behind TLS termination.

Support `metohub.cloud` and explicitly configured aliases with valid certificates. Redirect `www` to the canonical hostname if used. Inventory merchant custom domains and provision DNS/TLS for each before switching traffic. Storefront slug URLs do not require wildcard DNS. Add platform wildcard/subdomain support only if those hostnames are actually offered.

For the initial move, explicitly configure verified merchant domains at the edge. Automatic custom-domain onboarding later needs controlled TLS provisioning. Do not enable unrestricted on-demand TLS: a protected allowlist/authorization mechanism is needed, and a Caddy authorization endpoint is not currently implemented. A wildcard platform certificate does not cover merchants' independent domains.

## 10. Queue and scheduler requirements

Planned worker command:

```sh
php artisan queue:work database --queue=order-notifications,default --sleep=1 --tries=1 --timeout=240
```

Use the configured queue connection if it differs. Keep `DB_QUEUE_RETRY_AFTER=300` greater than the job timeout; use a stop grace period of at least 300 seconds and verify shutdown behavior. Preserve pending jobs and outbox rows during migration. Mail alerts use application retry handling, so do not assume the worker retry count alone represents delivery policy.

Run one scheduler container with `php artisan schedule:work`, supervised through container restart behavior. Alternatively, use one host cron invoking `schedule:run` every minute; choose one method only.

Current scheduled responsibilities:

| Task | Frequency | Impact if absent |
| --- | --- | --- |
| `billing:reconcile` | Hourly | Subscription/payment state can remain stale |
| `notifications:deliver-orders` | Every minute | Owner email/push retry dispatch stops |
| `notifications:deliver-shipments` | Every minute | Customer shipment email retry dispatch stops |

A scheduler running is not enough if its worker is stopped. Monitor both scheduling and actual job completion. Stop source workers/scheduler before activating destination workers/scheduler during cutover to avoid duplicate processing or competing payment actions.

## 11. Build and staging sequence

1. Inventory the source database version, tables, upload paths, cron/worker processes, domain list, credentials and outbound network requirements.
2. Build the proposed containers with pinned dependencies, excluding secrets and live uploads.
3. Run the application tests using an isolated database, never the production connection. Check final PHP platform requirements and image vulnerabilities.
4. Restore a sanitized database/upload copy into staging. Disable real recipient emails, push deliveries, payment writes and indexing before any worker starts.
5. Validate the application under HTTPS with the exact proxy chain and persistent mounts.
6. Exercise migrations, deployment failure handling, container recreation and restore on staging.
7. Measure memory, CPU, database contention, queue delay and upload growth under representative load.
8. Finalize exact production Compose files, pinned image tags, backup settings and release commands only after these checks pass.

## 12. Production cutover runbook

Plan a short write freeze instead of claiming a zero-downtime move without replication. Reduce DNS TTL ahead of the window. Keep access to the old host and a verified backup.

1. Prepare target containers, private database, permissions, volumes, credentials and certificates. Restrict public application access until data is imported.
2. Perform a preliminary database export/upload sync to estimate transfer time and validate restore compatibility.
3. Begin the write freeze on the source: maintenance mode plus ingress control for any remaining write paths. Stop source workers/scheduler gracefully and check in-flight jobs and database writes are finished.
4. Coordinate provider webhooks during the freeze. Return a retryable non-success response or route to the active destination only once ready; do not acknowledge and discard events. Review provider delivery retries after cutover.
5. Take a final consistent database export and final upload/private-file sync. Include jobs, sessions, cache/locks and notification outbox tables. Use proper transaction-aware database backup tooling and verify nontransactional tables separately.
6. Restore into the destination database, validate row counts and selected totals, and copy uploads into their persistent volumes. Keep the original `APP_KEY`. Preserve an untouched backup and old release.
7. Run migrations once using the release container, then generate runtime caches and run `php artisan app:preflight`. Stop if any step fails. Never run `migrate:fresh`, database reset or demo seeders.
8. Test the destination using controlled host routing before general DNS changes. Verify login, sample product images, orders and provider URLs without sending real messages or charging cards unintentionally.
9. Switch domain DNS/routing, including every merchant custom domain. Check A and AAAA records; an old AAAA record can keep some clients on the source.
10. Keep the old origin read-only during DNS overlap or proxy it to the destination with correct Host/TLS handling. Do not allow both copies to accept independent orders.
11. Start destination worker and scheduler, ensure no stale copied lock blocks work, and monitor queue/outbox processing. Run billing reconciliation and review webhook delivery history.
12. Open normal application access and perform authorized controlled checks. Keep the source frozen until the rollback window closes.

Same-domain relocation does not require changing `APP_URL`. Moving from a beta hostname does: update canonical domain, provider callbacks and DNS deliberately. Keep redirects for the beta host with signed paths/query strings intact.

## 13. Routine releases after migration

Publish immutable application/web images tagged with the same release identifier. Take a database backup, assess schema compatibility and prepare maintenance/rollback before deployment. Use explicit release commands rather than doing migrations in each startup.

The following commands illustrate Laravel release operations; exact Compose invocations depend on the future files:

```sh
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan app:preflight
```

Coordinate maintenance and cache generation across the service instances. Do not blindly clear shared database cache during a rolling release: subscription and notification locks use it. Recreate app/web/worker/scheduler from the new release; a queue restart signal alone does not update a container's image. Let in-flight jobs exit gracefully and ensure old scheduler processes are gone. Open traffic after health and smoke checks pass.

## 14. Backup, restore and rollback

Back up the database, public uploads, private files, protected environment configuration, `APP_KEY`, VAPID keys and certificate state. Keep release/image references. Encrypt sensitive backups and send them to a different host/provider account with retention and failure alerts.

An initial policy proposal is daily full backups, more frequent database recovery points according to acceptable order loss, and 7 daily/4 weekly/3 monthly retained copies. The owner must choose recovery point and recovery time targets. Daily-only backups can lose nearly a day's orders. Use binary logs or managed point-in-time recovery where the chosen target requires it.

Do not copy raw live database volume files as an ordinary consistent database backup. Use compatible database backup/export methods, verify completeness and test restores into an isolated environment. Coordinate upload backup consistency with database records. A VPS snapshot is supplementary, not the only recovery strategy.

For application-only failures, restore the prior compatible image while preserving database/uploads. For incompatible schema changes, restore only through an explicit recovery plan; blindly running migrations down can destroy data. If the new host has accepted orders/payments, switching DNS back to the stale source loses writes. Freeze traffic and reconcile new transactions before any data rollback. Re-check Razorpay state and notification outboxes after recovery to avoid duplicate charges or messages.

## 15. Monitoring and acceptance criteria

Monitor public HTTPS uptime, `/up`, container restarts, disk/inodes, database availability/backup age, memory/CPU, application errors, queue age, failed jobs, unsent notification attempts, scheduler execution, webhook failures and certificate expiry. `/up` is an application boot signal; it does not prove SMTP, payments or the database workflow is healthy.

The implementation is ready for cutover only when:

- Restarting/recreating every service preserves the database, uploads and keys.
- All 15 themes render correctly and product uploads remain visible after a release.
- Merchant/superadmin/customer access stays isolated; secure cookies and CSRF work behind HTTPS.
- COD orders, stock updates, manual orders, invoices/PDFs and XLSX exports work in the container runtime.
- Courier tracking appears in My Orders; shipment mail retries and duplicate-save handling work.
- Owner bell notifications, email and opted-in device push work with controlled recipients.
- Platform Razorpay checkout/webhook/reconciliation/cancellation pass authorized test-mode checks; no duplicate checkout is created.
- Expired admin accounts remain Billing-only, storefronts stay open for the configured 30-day grace period, and access returns after a verified paid period.
- Blogs, robots, sitemap, llms, canonical URLs and custom-domain routing work correctly; staging stays nonindexable.
- Restore and rollback rehearsals succeed, and no old worker/scheduler remains active after cutover.

## 16. Decisions to confirm before implementation

Choose the VPS provider/region/architecture, final sizing, domain/DNS operator, database engine/version, backup destination and retention, acceptable downtime, recovery targets and existing merchant custom-domain inventory. These are deployment inputs; no secret values belong in this document.

Recommended first implementation: one VPS, Compose, Caddy edge, Nginx, one PHP application image reused for app/worker/scheduler, a compatible MySQL service, database-backed cache/session/queue, persistent uploads and off-host backups. Introduce managed database/object storage/Redis or additional app replicas only after measured need and a separate migration design.

## References

Project documents: [production deployment](production-deployment.md), [release checks](production-release-2026-10-21.md), [Razorpay setup](razorpay-setup.md), [owner notifications](order-notifications.md), [customer shipment notifications](customer-shipment-notifications.md), [subscription expiry](subscription-expiry-policy.md), [search readiness](search-readiness.md), and [merchant integrations](store-integrations.md).

Official technical references checked for this plan: [Docker Compose production deployment](https://docs.docker.com/compose/how-tos/production/), [Docker volumes](https://docs.docker.com/engine/storage/volumes/), [Compose secrets](https://docs.docker.com/reference/compose-file/secrets/), [Laravel 12 deployment](https://laravel.com/docs/12.x/deployment), [Laravel 12 queues](https://laravel.com/docs/12.x/queues), and [Caddy automatic HTTPS](https://caddyserver.com/docs/automatic-https).
