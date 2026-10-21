# Dependency remediation — 21 October 2026

Initial `composer audit --locked --no-interaction`: 63 advisory entries affecting 19 packages, matching the unresolved findings recorded on 17 October.

Updated dependencies locally within the existing composer.json constraints on PHP 8.2.4 using Composer’s resolver. Lockfile operations: one installation, 81 updates, two removals. No root dependency constraints were relaxed and no advisory was ignored.

Notable versions: Laravel 12.44.0 → 12.70.1, Dompdf 3.1.5 → 3.1.6, Guzzle 7.10.0 → 7.16.0, CommonMark 2.8.0 → 2.10.4, PhpSpreadsheet 5.8.0 → 5.10.0. Transitive updates include php-jwt 7.2.1 and phpseclib 4.0.2 as permitted by their updated consumers. Application code does not call their former major-version APIs directly.

Composer’s post-update advisory check reported **no security vulnerability advisories found**. Composer manifest validation and production platform requirement checks pass locally. PHP tests and PDF/XLSX/Web Push signature smoke tests verify application/package integration; Google OAuth and payment providers still need controlled deployment smoke tests.

This is a dated dependency-advisory result, not proof that no vulnerabilities exist. Deploy with `composer install --no-dev --prefer-dist --optimize-autoloader` using the new lockfile. Re-run `composer audit --locked` periodically and after future dependency changes.
