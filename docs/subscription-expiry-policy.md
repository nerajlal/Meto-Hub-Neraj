# Subscription expiry policy — 21 October 2026

## Merchant admin

After both trial and verified paid access end, merchant login goes directly to Billing, ignoring an old intended dashboard destination. The sidebar contains only Billing; notification polling and the bell are hidden. Billing actions and receipts remain accessible, and logout still works.

Other merchant admin pages redirect to Billing. JSON requests return 402 with a billing URL; HTML form submissions redirect with 303 without applying changes. Middleware enforces the restriction even for direct requests. Platform super-admin access remains available for support. Records are retained, and normal access returns after a renewed paid period is confirmed.

## Storefront

The storefront and customer checkout remain available for **30 days after access expires**, subject to existing COD-disable preferences. At the exact grace cutoff, homepage, catalog, product and checkout routes show a “Store temporarily unavailable” page explaining that subscription payment needs renewal. Direct order/cart requests using storefront tenant middleware fail too.

Unavailable responses return HTTP 503, no-store caching, noindex and Retry-After headers. They expose no account, customer or payment details. Configured custom domains stay available during grace and show the same unavailable page afterward. Active Base plans do not gain a custom-domain entitlement.

Expired stores are omitted from merchant sitemap lists after grace. Renewal restores availability on the next request.

**Operational consequence:** customers can place orders during grace while merchant admin is locked to Billing. This follows the requested policy; the merchant must renew to manage those orders in admin.

## Time and legacy subscriptions

`config/saas.php` sets `storefront_grace_days` to 30. The expiry anchor is the later of the original trial end and the latest recorded paid-through date with a positive paid count and a paid period that has started. Visits, Billing page loads and store-name changes do not reset the clock.

A legacy `subscription_status=active` flag without paid-period evidence no longer grants indefinite access. **Reconcile legacy paid subscriptions before production rollout**, so paying merchants have accurate paid-from/paid-through records:

```sh
php artisan billing:reconcile
```

This command reads provider state without creating charges. It needs correct provider credentials; investigate reconciliation failures before rollout. Stores without a provider reference need a deliberate billing-record review. Do not invent dates or reset tenant creation dates to bypass access checks.

Deploy normally and refresh Laravel configuration, routes and views. No database reset, new migration or new environment variable is required.

## Verification

Tests cover paid/trial expiry, the exact 30-day boundary, custom domains, direct checkout requests, real application COD order creation during grace, Billing-only access, login redirects, legacy bypass prevention and renewal recovery. Browser checks cover Billing-only and unavailable screens at 320/375/768/1440px using isolated in-memory data. No real payments were made.
