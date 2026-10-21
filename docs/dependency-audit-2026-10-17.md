# Dependency audit — 17 October 2026

Remediated in the tested 21 October dependency update: see [dependency-audit-2026-10-21.md](dependency-audit-2026-10-21.md). Findings below describe the earlier lockfile.

Composer reported 63 advisory entries across 19 locked packages while adding owner Web Push. Some advisories overlap. These entries are findings, not a completed remediation.

The added packages (`minishlink/web-push`, `web-token/jwt-library`, `spomky-labs/base64url`, `spomky-labs/pki-framework`, `symfony/polyfill-php82`) had no advisory entries in this audit. No existing package version was updated by this install.

| Package | Dependency scope | Advisory entries |
| --- | --- | --- |
| `dompdf/dompdf` | Production | 6 |
| `firebase/php-jwt` | Production | 1 |
| `guzzlehttp/guzzle` | Production | 9 |
| `guzzlehttp/psr7` | Production | 4 |
| `laravel/framework` | Production | 4 |
| `league/commonmark` | Production | 14 |
| `league/flysystem` | Production | 1 |
| `phpoffice/phpspreadsheet` | Production | 3 |
| `phpseclib/phpseclib` | Production | 5 |
| `phpunit/phpunit` | Development | 1 |
| `psy/psysh` | Production | 1 |
| `symfony/http-foundation` | Production | 2 |
| `symfony/http-kernel` | Production | 1 |
| `symfony/mailer` | Production | 1 |
| `symfony/mime` | Production | 2 |
| `symfony/polyfill-intl-idn` | Production | 1 |
| `symfony/process` | Production | 1 |
| `symfony/routing` | Production | 2 |
| `symfony/yaml` | Development | 4 |

Re-run `composer audit --locked` for current advisory IDs, affected versions and upstream links. Plan a separate tested dependency update; do not run an unrestricted production `composer update` as a deployment repair. This document does not establish exploitability or assert that the whole site is safe.
