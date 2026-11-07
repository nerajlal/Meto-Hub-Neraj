# Customer payments: Razorpay, Paytm and PhonePe

Implemented locally on 7 November 2026. Provider calls in automated tests are mocked. A real staging/UAT transaction and provider approval are still required before enabling a merchant's live payments.

This feature accepts payments into each store's own approved payment-gateway account. It does not use the platform's SaaS subscription keys, collect money into MetoHub for later UPI payouts, or turn an offline QR/Soundbox account into an online gateway automatically.

## Merchant setup

Open `/{store}/admin/settings/payment`. Saving credentials does not certify that the account is activated or that the credentials work. Keep the store currency set to INR.

### Paytm

- Activate website/app payments in the Paytm merchant dashboard.
- Enter the **online-payment MID**, **16-character Merchant Key**, **Test/Live environment** and the exact **Website Name** assigned by Paytm (`WEBSTAGING` is typical for staging; `DEFAULT` is typical for production).
- Configure the payment-status webhook URL shown in MetoHub. Per-checkout browser callback URLs are supplied automatically.
- Test and production credentials must match the selected environment. An offline MID or QR identifier is not sufficient evidence of online activation.
- The official `paytm/paytmchecksum` Composer library signs requests and verifies API/callback responses. Test API and CheckoutJS use `securestage.paytmpayments.com`; live uses `secure.paytmpayments.com`.
- The website opens Paytm CheckoutJS using a short-lived transaction token. No Merchant Key is returned to the browser. Both `transactionStatus` and modal-close handlers trigger a server status check; browser-reported success is never trusted.
- MetoHub verifies the signed Transaction Status response, online MID, order reference, exact rupee amount and final `TXN_SUCCESS`. Pending, failed, pre-authorization, malformed signatures and mismatched payments cannot create paid orders. Provider-reported full refunds before fulfilment close the payment; partial refunds remain under review.

### PhonePe

- Activate **Standard Checkout V2** with PhonePe.
- Enter **Client ID**, **Client Secret**, **Client Version** and **Test/Live environment**.
- Choose a webhook **username** and **password** (at least 16 password characters) and configure the same values in PhonePe. Enable `checkout.order.completed` and `checkout.order.failed` at the webhook URL shown in MetoHub.
- Legacy V1 Merchant ID / Salt Key / Salt Index settings are no longer used. The migration disables the old PhonePe switch until V2 credentials are configured; it preserves the old encrypted values.
- Authentication uses server-side OAuth client credentials. Tokens are encrypted in the application cache, refreshed before expiry and never returned to the shopper. The production token endpoint is under `api.phonepe.com/apis/identity-manager`; sandbox is under `api-preprod.phonepe.com/apis/pg-sandbox`.
- The browser redirects to the provider-returned HTTPS checkout URL only after its host is checked against PhonePe's domain. A return redirect is not proof of payment.
- The server queries the saved merchant order reference with OAuth, checks `COMPLETED`, the exact amount, provider order identity, and one matching completed payment transaction. Webhook authentication follows PhonePe V2's SHA-256 username/password header format; the webhook body is still not accepted as proof of payment.

### Razorpay

The Razorpay integration remains supported. See [Razorpay setup and verification](customer-razorpay-payments.md) for its Key ID, Key Secret, webhook and capture settings.

## Shared checkout guarantees

- The server validates customer fields, retrieves actual variant/bundle prices, applies existing discounts, checks stock and adds configured tax. Submitted browser amounts, currency and paid flags cannot override the quote. Current shipping remains free, matching existing order checkout.
- A saved checkout binds one browser session, optional signed-in customer, tenant, gateway, cart selection, delivery details and amount. The UUID is the idempotency reference. Switching gateways cannot silently reuse the previous gateway's checkout.
- Credentials and checkout snapshots/tokens are encrypted at rest. Secret inputs render blank, preserve existing secrets when left blank and are excluded from flashed validation input. Stripe stays removed from the settings UI and unavailable at checkout.
- The provider's merchant order reference is recorded before outbound initiation. Razorpay retains its provider-created order ID; Paytm/PhonePe use deterministic, gateway-prefixed merchant references. Unknown creation results are retained for reconciliation rather than treated as successful orders.
- Paid order creation locks the payment and stock rows. Unique payment references are scoped by gateway; callbacks, status checks and scheduled recovery are idempotent. Direct `/order/place` calls cannot create online paid orders.
- Owner/customer notifications run after the order is saved. The payment-status response removes matching paid cart rows without deleting unrelated new cart contents.

## Recovery and stock review

The shopper can use **Check payment status**. Paytm callbacks and PhonePe webhooks also initiate authenticated server checks. `payments:reconcile` runs every five minutes, up to 100 eligible records per run, independently of browser sessions or storefront subscription availability.

A provider return page shows a generic result unless the original browser/customer owns the checkout. It does not reveal customer details to someone who only knows the UUID. Confirmed owned checkouts clear matching cart contents and offer a link to the order. A shopper who started on a custom domain may return to the platform domain without the original domain's cookie: the Return to store checkout link takes them back to their original configured storefront domain to check status with its existing cookie and sessionStorage. Canonical provider callback/return URLs use configured `APP_URL`, not the incoming Host header.

Unknown/timeout responses do not trigger transport-level retries of a creation POST. Paytm can explicitly resume its same idempotent order reference after a signed status check, including refreshing an expired token. PhonePe unknown/pending attempts remain pending until an authenticated status check establishes completion or failure/expiry; do not create a second payment just because the redirect was lost. Failed/expired checkouts allow a fresh UUID after checking status. Keep reconciling failed attempts in case a delayed provider update establishes success.

Stock is **not reserved while a payment window is open**. If stock disappears or order creation fails after payment succeeds, the captured payment stays recorded as `review`; the shopper is told not to pay again. The admin dashboard displays a warning and Settings → Payment lists affected references and amounts.

- Razorpay/Paytm can retry fulfilment after stock is restored, after checking their available provider refund fields.
- Authenticated PhonePe refund notifications place unfulfilled checkouts on hold; they never create an order.
- PhonePe's V2 order status does not expose a cumulative refund amount. Reviewed PhonePe payments do **not** automatically retry fulfilment. The merchant must inspect PhonePe, confirm that no refund was issued, then click **Verify & retry order**. The payment is still re-verified through the authenticated API before order creation.
- Issue refunds through the provider dashboard when needed. Automated refund execution and synchronization of refunds on already completed orders are not part of this release. Keep reviewed PhonePe payments on hold after a refund; do not confirm the retry checkbox.
- No automatic purge of payment snapshots is implemented. Preserve encryption keys and unresolved payment records. Do not revoke old merchant keys while payments created using them are unresolved.

## Deployment

1. Back up the database and encryption key. Deploy/rebuild the app image using the updated `composer.lock` (adds the official Paytm checksum package).
2. Apply both customer-payment migrations before serving the new admin forms. Existing secrets are encrypted by the earlier migration; the new migration adds Paytm/PhonePe V2 settings and provider-aware payment records. Existing orders are not recharged or rewritten.
3. Use the same application release and `APP_KEY` on web, workers and scheduler. Keep `APP_URL` set to the canonical HTTPS platform domain.
4. Configure each merchant's test gateway, callbacks and webhooks, then complete provider sandbox/UAT tests. Enable live only after account activation and testing.

For the current VPS, from `/srv/apps/metohub-next/runtime` after deploying the updated image:

```sh
docker compose --project-name metohub-next-test exec -T -u www-data app php artisan migrate --force
docker compose --project-name metohub-next-test exec -T -u www-data app php artisan config:cache
docker compose --project-name metohub-next-test exec -T -u www-data app php artisan view:cache
docker compose --project-name metohub-next-test exec -T -u www-data app php artisan queue:restart
```

For a non-Docker deployment, first run `composer install --no-dev --prefer-dist --optimize-autoloader` against the updated lock file, then migrate and rebuild caches. No global Paytm/PhonePe credentials need to be added to `.env`; merchant credentials belong in their own store settings.

Manually reconcile a specific reference:

```sh
php artisan payments:reconcile --checkout=CHECKOUT_UUID
```

Rollback must preserve payment history and encrypted settings. The alternative-gateway migration refuses a destructive rollback if Paytm/PhonePe payment records exist; use a matching application/database backup instead.

## Verification and remaining operational checks

Automated tests use isolated SQLite fixtures, mocked HTTP providers, real Paytm checksum generation/verification, and mocked browser SDKs. They cover bad signatures, amount/order/merchant mismatches, pending/failure, lost callbacks, duplicate submissions, cross-browser isolation, encryption, stock review and recovery. They cannot verify a merchant's live activation or provider dashboard settings.

Installing the checksum package exposed Composer audit findings in existing Symfony packages. The Paytm checksum package has no reported advisory in that audit. The existing dependency advisories are a separate production-hardening task and were not silently addressed by unrelated package upgrades here.

## Official references

- [Paytm getting started](https://www.paytmpayments.com/docs/getting-started)
- [Paytm Initiate Transaction API](https://www.paytmpayments.com/docs/api/initiate-transaction-api/)
- [Paytm Transaction Status verification](https://www.paytmpayments.com/docs/jscheckout-verify-payment/)
- [Paytm CheckoutJS](https://www.paytmpayments.com/docs/js-checkout/getting-started/)
- [Paytm checksum library](https://github.com/paytm/Paytm_PHP_Checksum)
- [PhonePe official V2 SDK reference](https://github.com/PhonePe/phonepe-pg-sdk-php)
- [PhonePe official V2 request/response source](https://github.com/PhonePe/phonepe-pg-sdk-go)
