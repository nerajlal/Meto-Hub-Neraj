# Customer Razorpay checkout

Implemented 7 November 2026. This is separate from MetoHub subscription billing and split payouts. Each store uses its own Razorpay account; MetoHub does not route these customer payments to a personal UPI number.

## Enable after deployment

1. Deploy the updated app image/code and back up the production database and APP_KEY. Do not revoke old Razorpay API keys while checkouts created with them are unresolved. Keep APP_KEY stable: gateway secrets and saved checkout snapshots are encrypted with it.
2. Apply migrations before serving the updated admin pages. The migration widens and encrypts existing gateway secrets, adds a merchant webhook secret and creates `customer_payments`. It does not change existing paid orders.
3. In the store's Admin → Settings → Payment, enter its Razorpay Key ID, Key Secret and a separate customer-payment webhook secret (at least 16 characters). Leave saved secret fields blank to preserve them. Never put merchant secrets in public HTML, JavaScript or chat.
4. In that merchant's Razorpay dashboard, enable automatic capture and add the exact HTTPS webhook URL shown on the settings page. Select `payment.captured` and `order.paid`. Use the same webhook secret. This is NOT `/billing/razorpay/webhook`, which is for platform subscriptions.
5. Use matching test credentials first. Only enable live credentials after testing success, cancellation, failure, interrupted browser completion and webhook recovery. Configure the webhook in the corresponding test/live dashboard mode.
6. Ensure Laravel's scheduler runs. `payments:reconcile` is scheduled every five minutes. Existing queue workers and production mail settings are required for owner/device notifications.

For the existing VPS, after deploying the new image/code, run from `/srv/apps/metohub-next/runtime`:

```sh
docker compose --project-name metohub-next-test exec -T -u www-data app php artisan migrate --force
docker compose --project-name metohub-next-test exec -T -u www-data app php artisan config:cache
docker compose --project-name metohub-next-test exec -T -u www-data app php artisan view:cache
docker compose --project-name metohub-next-test exec -T -u www-data app php artisan queue:restart
```

The current multi-gateway release adds the official Paytm checksum dependency to composer.lock; rebuild the image or run composer install. No new platform billing environment keys are required. If using a different deployment project name, use that project's name. Restart/redeploy scheduler and worker containers with the same application release. Do not roll application code back to plaintext-secret handling while leaving the encrypted database in place; restore a matching release/database backup if rollback is necessary.

## Server-enforced flow

- `/payment/initiate` validates checkout fields and enabled methods, reads the actual store cart, refreshes variants/prices/discounts, checks aggregate stock and adds the store's configured tax. Browser amounts/currency/paid flags are ignored. Current shipping remains free, matching existing checkout. INR only; minimum ₹1.
- The checkout UUID, browser session ownership, optional signed-in customer, merchant, exact cart/variant selection, delivery details and quoted amount are saved before the provider call. Payload and the credential snapshot are encrypted.
- A persisted idempotency UUID and matching in-progress cart fingerprint reuse the provider order. Order-creation POST requests are never automatically retried on timeout.
- The success callback posts its signature to `/payment/verify`. The server uses the stored provider order ID for HMAC verification, then fetches both payment and order from Razorpay using the merchant credential snapshot.
- A paid order requires full capture, exact amount/currency/order/receipt/store metadata and no partial refund. An authorized-only payment is not paid. Direct `/order/place` requests cannot create online paid orders; COD and WhatsApp always remain unpaid until a separate manual record update.
- Finalization locks the checkout and stock rows. Payment IDs and provider order IDs have unique database constraints. Repeated callbacks, webhooks and reconciliation cannot create additional orders or reserve stock twice.
- Customer/merchant order notifications are triggered only after the order is created. The browser clears matching paid cart rows after confirmation, preserving unrelated new cart items.

## Interrupted payments and review

The checkout has a **Check payment status** button and retains its reference in sessionStorage across a reload in the same tab. Signed webhooks and scheduled reconciliation also finish captured payments without a browser callback. Recovery runs independently of the current browser's tenant and does not require the store to remain accessible during maintenance/subscription expiry.

Reconciliation can find a provider order by the UUID receipt after an uncertain creation response. It never repeats an uncertain creation POST. If a successful provider lookup after a one-minute delay confirms no order exists, the browser may start a new checkout. Provider failures leave the attempt pending for retry.

Stock is checked before opening payment but is **not reserved while the customer has the payment window open**. Stock, quoted prices and product selection are snapshots; final stock availability is checked atomically after capture. If an item sells out, is deleted, or an order write fails after capture, the payment is retained as `review`, with the payment reference and amount visible in Settings → Payment. A warning also appears throughout the merchant admin dashboard. The customer is explicitly told not to pay again.

Restore the exact variant's stock and let reconciliation retry, or refund the customer in Razorpay. A full provider-confirmed refund before order creation closes the attempt as `refunded`; it does not create an order. Partial refunds remain under review. Refunds for already completed orders are not automatically processed or synchronized by this feature. Do not cancel/mark orders refunded locally and assume money has been returned.

To check one saved checkout:

```sh
php artisan payments:reconcile --checkout=CHECKOUT_UUID
```

Failed checks are retried by the scheduler (up to 100 eligible attempts per run, oldest check first). Unresolved attempts and their encrypted snapshots are retained; there is no automatic retention purge in this release. The quote/delivery details saved for a checkout are used on recovery; do not alter the cart/address while resuming it and assume those edits change an already initiated payment.

## Supported methods and boundaries

Direct Paytm and PhonePe V2 are also implemented; see [the multi-gateway guide](customer-payment-gateways.md) for their setup and limitations.

- Razorpay is the supported online integration. Availability of individual UPI apps, cards, wallets and other methods depends on the merchant's Razorpay account and provider checkout. A PhonePe or Paytm UPI app does not itself require building a direct integration.
- Stripe has been removed from payment settings and checkout. Its legacy database credentials remain encrypted. Paytm and PhonePe V2 have their own credential fields and verified checkout flows.
- No native app SDK flow, automated refund execution, split payouts or courier booking was added.
- Local automated tests use isolated databases and mocked provider calls; no real charge or production merchant configuration is verified by them.

## Provider references

- [Razorpay Standard Checkout](https://razorpay.com/docs/payments/payment-gateway/web-integration/standard/)
- [Official Orders API reference](https://github.com/razorpay/razorpay-php/blob/master/documents/order.md)
- [Official payment webhook reference](https://github.com/razorpay/markdown-docs/blob/master/webhooks/payments.md)
