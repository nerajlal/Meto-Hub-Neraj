# MetoHub flow review — 21 October 2026

## Scope and evidence

**Follow-up:** the confirmed code findings below have now been addressed. See [production-release-2026-10-21.md](production-release-2026-10-21.md) for implementation details, historical-stock limits and remaining production verification gates, and [dependency-audit-2026-10-21.md](dependency-audit-2026-10-21.md) for dependency remediation. The original findings are retained as review history.

Reviewed registration/email verification, tenant authorization, subscription access, settings navigation, order creation, reels, notification scheduling and deployment configuration. Ran the existing complete PHP suite: **159 passed, 3776 assertions**.

This is a bounded source-and-test review, not a claim that every route, dependency or production infrastructure control has been audited. The existing suite does not cover every finding below. No production server access, real payments, mail deliveries or customer orders were used. Application code was not changed during this review.

## Fix next

### 1. Manual-order relationships need tighter validation

Source: `app/Http/Controllers/Admin/OrderController.php`, `store()`.

- `user_id` uses a global database `exists:users,id` rule; an existing customer is assigned without an explicit current-tenant/type lookup inside the transaction.
- `variant_id` uses a global `exists:product_variants,id` rule, followed by `ProductVariant::find()`. It is not checked against the selected product. ProductVariant itself has no tenant scope.
- Products are fetched through scoped Eloquent queries, which helps protect product lookup. That does not establish ownership of a separately submitted customer or variant ID.

Use tenant-bound customer validation and resolve variants through the selected store-owned product. Add regression tests submitting another store’s customer and variant IDs, and a mismatched variant from the same store. This is a source-confirmed missing check; this pass did not execute a cross-store exploit reproduction.

### 2. Manual orders do not update inventory

The manual-order transaction creates order items but contains no stock check/decrement. A merchant can record an offline sale while variant stock remains unchanged.

Define stock behavior for manual orders, check and lock the relevant variants inside the transaction, and deduct stock consistently with the sale state. Test concurrent/insufficient stock, multi-item rollback and cancelled-order behavior before claiming complete online/offline inventory consistency. Do not change the merchant’s ability to record a custom manual selling price merely to address inventory.

### 3. Reels should validate store ownership and provider URLs

Source: `app/Http/Controllers/Admin/ReelController.php`.

Product/collection validation currently uses global `exists` checks. The URL is validated as a generic URL, while the storefront builds an Instagram embed by appending `/embed`. Restrict linked records to the resolved tenant, and accept supported Instagram URL formats rather than arbitrary hosts/schemes. Test links belonging to another store and non-Instagram URLs. Do not delete existing reels as a cleanup shortcut.

### 4. Remove authentication debugging with personal identifiers

Source: `app/Http/Controllers/Admin/AuthController.php`, `commonLogin()`.

Debug log statements include the submitted email address, login result and tenant/account context. Remove unnecessary personal identifiers and noisy debug statements. Preserve useful security monitoring through intentional, minimal events where required. Do not print credentials or log file contents during verification.

## Cleanup after those fixes

- The legacy Videos editor/routes/model remain available even though the overview no longer links to them. A future retirement should deliberately disable/redirect the legacy editor and preserve saved data; hiding the row did not remove the feature’s backend.
- Delivery staff was removed from the overview at the owner’s request; existing delivery assignment/staff routes remain. This was a navigation change, not removal of all delivery functionality.
- `LandingController::handleDemoRequest()` reports success when its mail operation fails. Check the route’s actual usage before fixing or retiring this legacy flow. The current contact form uses its separate controller and already reports mail failure.
- Some older marketing pages outside the main footer journey still use grocery-specific or legacy positioning. Review or retire them deliberately; do not treat the recent homepage/themes/pricing pass as a complete editorial audit of every route.

## Production verification still required

- Confirm production domain/environment, HTTPS, redirect behavior, private-route access and error responses.
- Check SMTP verification and order mail using controlled test accounts.
- Run a persistent notification queue worker and the Laravel scheduler. `routes/console.php` schedules order-notification delivery every minute and billing reconciliation hourly; source registration does not prove server execution.
- Verify Razorpay webhook signatures, reconciliation and actual subscription state in a controlled provider test setup before live billing.
- Verify push setup/VAPID and device permission behavior on supported devices.
- Verify backup and restore operations, logging configuration and production file permissions.
- Confirm business/legal/tax information and externally managed blog content. Current order invoices are not represented as statutory GST invoices.

## Recommended sequence

1. Manual-order customer/variant ownership and stock consistency.
2. Reel ownership and Instagram URL validation.
3. Authentication log cleanup and legacy entry-point review.
4. Controlled production smoke test covering signup → verification → store setup → order → notification → invoice/label, plus subscription billing separately.

No additional customer-facing features are needed for this pass. Prioritize correctness and operational verification over expanding the product.
