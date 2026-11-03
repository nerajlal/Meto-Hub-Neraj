# Google indexing problem and resolution

Date: 3 November 2026  
Production website: https://metohub.cloud  
Status: **The public homepage's blocking HTTP header was removed and verified. Google recrawling/indexing remains a separate follow-up.**

## What happened

Google Search Console reported that the homepage was excluded from indexing:

> No: 'noindex' detected in 'X-Robots-Tag' HTTP header

Google could crawl the URL and fetch it successfully, but the response explicitly instructed Google not to index it. Successful sitemap processing did not override that instruction. The empty Page indexing report alone did not identify this cause; URL Inspection and its live test did.

Other symptoms included an old Glam Box search result, missing main-page search results and a generic search-result icon. Those symptoms did not by themselves establish the cause. The confirmed technical blocker was the homepage's `noindex` response header.

## Confirmed causes

### 1. Application indexing configuration needed verification

`config/search.php` reads `SEARCH_INDEXABLE`. The `SearchIndexing` middleware adds `X-Robots-Tag: noindex, nofollow` when indexing is disabled, and intentionally also protects private pages, previews and errors.

The production environment was configured with:

```dotenv
APP_URL=https://metohub.cloud
SEARCH_INDEXABLE=true
```

Laravel's configuration cache was rebuilt and the effective setting was verified as boolean `true`. Despite this, the public homepage still returned two blocking headers. This proved the environment setting alone was not sufficient to resolve the remaining problem.

### 2. Container Nginx added a blanket header

The shared image source `deploy/container-nginx.conf` contained:

```nginx
add_header X-Robots-Tag "noindex, nofollow" always;
```

Its active container path was `/etc/nginx/conf.d/default.conf`. The configuration was built into the web image, not mounted from the host. Only public uploads were mounted in that container.

### 3. Host Nginx added another blanket header

The production HTTPS server block for `metohub.cloud` also contained the same rule in:

```text
/etc/nginx/sites-enabled/metohub-next-test
```

Despite its historical `test` name, this configuration serves the production domain and proxies to `127.0.0.1:8892`. The separate staging site is `test.metohub.cloud`, using port `8893`.

The corresponding repository source is `deploy/routing/metohub-next-test.conf`.

## Changes made

1. Set/confirmed `SEARCH_INDEXABLE=true` in the production runtime environment and rebuilt the Laravel config cache.
2. Removed the blanket header from `deploy/container-nginx.conf` and deployed the corrected web image through the existing deployment process.
3. Removed the blanket production rule from repository source `deploy/routing/metohub-next-test.conf` so future routing setup does not restore it.
4. Removed the rule from the active host Nginx production configuration, validated syntax and reloaded Nginx.
5. Kept staging host rules and Laravel's private-page indexing protection. We did not globally hide upstream robots headers or delete the middleware.

Repository deployment checks passed: **11 tests**. Those were offline orchestration checks, not a substitute for checking the actual production response.

## Commands used and verification

PHP runs inside Docker on this VPS; running `php artisan` directly on the host failed because host PHP is not installed. Installing host PHP was unnecessary.

From `/srv/apps/metohub-next/runtime`:

```bash
docker compose --project-name metohub-next-test exec -T -u www-data app php artisan config:clear
docker compose --project-name metohub-next-test exec -T -u www-data app php artisan config:cache
docker compose --project-name metohub-next-test exec -T -u www-data app php artisan tinker --execute="dump(config('search.indexable'));"
```

The last command returned `true`.

We compared the container response with the public response to isolate the layers:

```bash
curl -sS -D - -o /dev/null -H 'Host: metohub.cloud' -H 'X-Forwarded-Proto: https' http://127.0.0.1:8892/
curl -sS -D - -o /dev/null https://metohub.cloud/
```

After the image fix, the container response had no `noindex`, while the public response still had one. That isolated the final blocker to the host proxy.

The host configuration was backed up to:

```text
/root/metohub-nginx-before-seo-fix.conf
```

The production file was edited to remove its `add_header X-Robots-Tag` rule. Nginx was then validated and reloaded:

```bash
sudo nginx -t && sudo systemctl reload nginx
```

The final public GET response supplied from the VPS at **15:44:18 UTC on 3 November 2026 (9:14:18 PM IST)** returned:

```text
HTTP/1.1 200 OK
Content-Type: text/html; charset=utf-8
Cache-Control: no-cache, private
X-Content-Type-Options: nosniff
```

**Neither of the previous `X-Robots-Tag: noindex, nofollow` headers remained.** Session cookie values are deliberately omitted from this document.

Nginx configuration directives are edited in configuration files; `add_header` is not a terminal command. Likewise, typing a configuration file path alone attempts to execute the file rather than open it.

## What this did not prove or change

- The final response confirmed the homepage HTTP-header fix. It did not prove every public URL, HTML robots meta tag, favicon response or Google indexing status was correct.
- No Search Console live-test success or completed reindexing result was provided after the final fix.
- The old `/store/glam-box` URL was reported as returning 404. Google may retain an old result until it reprocesses the removed page; do not recreate the page or redirect it to an unrelated homepage just to remove the result.
- Beta URLs listed under Search Console's “Referring page” are discovery history, not the homepage's canonical URL. The inspected canonical was already `https://metohub.cloud/`.
- The favicon not appearing in search was not independently diagnosed. Local favicon files/links existed, but production accessibility and Google's processing still require checking.

## Follow-up after deployment

1. Inspect `https://metohub.cloud/` in Search Console and run **Test live URL**. Confirm indexing is allowed, including both headers and HTML robots directives.
2. Once the live test passes, request indexing. Repeat inspection for `/pricing`, `/themes`, `/features` and `/about`.
3. Confirm production robots.txt allows crawling and references `https://metohub.cloud/sitemap.xml`; confirm main pages appear in the platform sitemap.
4. Check `/favicon.ico` and `/favicon-192.png` return actual readable images, then allow Google to recrawl the homepage/icon.
5. Keep staging nonindexable. For retired beta URLs, use deliberate production redirects; if beta remains a testing environment, keep it private or nonindexable.

The header fix is immediately testable. Actual indexing, old-result removal, report updates and favicon changes require Google processing and are not immediate or guaranteed.

## Prevent recurrence

- Do not ship unconditional `noindex` in a web image shared between production and staging.
- Keep environment-specific indexing restrictions at the appropriate host/application layer.
- Verify the domain served by each configuration, rather than assuming its filename determines whether it is production.
- Include homepage header and HTML robots checks in production smoke tests, alongside a check that staging/private pages remain nonindexable.
- A repository routing change does not automatically change the host's active Nginx file. The existing release script updates application images; host configuration changes require explicit application, syntax validation and reload.
- Keep changes to production routing in both the repository source and the active host configuration so a later deployment or setup does not reintroduce the rule.

Official reference: [Google's noindex documentation](https://developers.google.com/search/docs/crawling-indexing/block-indexing).
