# Razorpay subscription billing setup

This integration collects MetoHub's recurring merchant subscription fees from Admin → Billing. Customer storefront checkout remains cash on delivery. Seller payouts and split settlements are outside this integration.

## 1. Configure the account and server

Enable Razorpay Subscriptions for the platform account. Start with Test mode and use matching credentials and plan IDs from that mode. Store secrets only in the server's `.env`:

```dotenv
RAZORPAY_KEY=your_key_id
RAZORPAY_SECRET=your_api_secret
RAZORPAY_WEBHOOK_SECRET=your_separate_webhook_secret
```

The webhook secret must also match the secret entered in Razorpay's webhook configuration. It is separate from the API secret. Checkout is disabled until all three settings exist; this checks configuration presence, not whether provider activation or webhook delivery has succeeded.

Optional existing plan mappings are available in `.env.production.example`:

```dotenv
RAZORPAY_PLAN_BASE_MONTHLY=
RAZORPAY_PLAN_BASE_YEARLY=
RAZORPAY_PLAN_PRO_MONTHLY=
RAZORPAY_PLAN_PRO_YEARLY=
RAZORPAY_PLAN_ENTERPRISE_MONTHLY=
RAZORPAY_PLAN_ENTERPRISE_YEARLY=
```

If blank, the server searches paginated provider plans and creates a matching plan when necessary. Mapped plans are checked against their amount, currency, period and interval before creating a subscription. Prices come from `config/saas.php`, never the browser.

| Plan | Monthly (INR) | Yearly (INR) |
| --- | ---: | ---: |
| Base | 99 | 1,089 |
| Pro | 249 | 2,739 |
| Enterprise | 749 | 8,239 |

Yearly billing charges eleven monthly prices once per year. Amounts are sent in paise. These figures do not establish GST treatment; confirm the business's tax policy separately. Subscriptions currently have 120 monthly cycles or 10 yearly cycles.

## 2. Register the webhook

Production endpoint:

```text
https://metohub.cloud/billing/razorpay/webhook
```

Subscribe to applicable subscription lifecycle events, including `subscription.authenticated`, `subscription.activated`, `subscription.charged`, `subscription.pending`, `subscription.halted`, `subscription.paused`, `subscription.resumed`, `subscription.cancelled`, and `subscription.completed`.

Use a publicly reachable HTTPS staging endpoint for Test mode; Razorpay cannot deliver directly to `127.0.0.1`. The endpoint validates the signature against the original request body, handles duplicate events and fetches current provider state instead of trusting event order. Unsupported event types are ignored. Do not add login protection or a CSRF requirement to this endpoint.

## 3. Deploy and reconcile

This change adds no Composer packages or database migrations. Keep dependencies and existing migrations current using the normal deployment process. After changing environment settings:

```sh
php artisan config:cache
php artisan view:clear
php artisan route:cache
php artisan billing:reconcile
```

Run Laravel's scheduler every minute using the correct PHP binary and the server's absolute project path:

```cron
* * * * * cd /absolute/path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Reconciliation is scheduled hourly. Use a working shared cache with atomic locks when running multiple application servers. Monitor scheduler failures and provider webhook delivery errors. Reconcile existing subscriptions before applying the stricter paid-access policy to existing paying merchants.

## 4. Verify in Test mode before switching live

1. Sign in as a merchant and open Billing. Confirm the Test mode badge, correct plan and billing cycle.
2. Test each monthly/yearly price. Close checkout and resume it: the same subscription must be reused. Try a failed payment and a duplicate click.
3. Complete a provider Test mode payment and verify its webhook delivery. Check the paid period, receipt and plan access against the provider dashboard. Checkout authorization alone must not unlock paid access.
4. Test delayed confirmation, refresh, duplicate webhooks, renewal failure and cancellation. Cancellation retains verified paid time. Expired merchants remain restricted to Billing; their storefront remains available for the configured 30-day grace period.
5. Confirm scheduled reconciliation runs successfully and imports invoices/payments. For an uncertain creation, refresh/reconcile before retrying; investigate unresolved attempts instead of deleting records and risking duplicate subscriptions.
6. Switch to the Live mode keys, Live mode plan mappings (if used), and the matching live webhook secret. Rebuild config cache. Perform an authorized live smoke test through the provider's normal process.

Cancellation is not a refund. Existing paid subscriptions cannot be replaced until their verified paid period ends; in-place plan upgrades are not implemented. An existing authorized subscription must be managed or reconciled rather than charged again through a new checkout.

## Code and verification scope

- Server: `SubscriptionBilling`, `Admin/BillingController`, `BillingWebhookController`.
- Browser: `public/js/metohub-billing.js`; billing view includes scripts through the admin layout's scripts stack.
- Tests: `SubscriptionLifecycleTest`, `PlanPricingTest`, `SubscriptionExpiryTest`, and `tests/browser/run_billing_checkout.py`.

Automated tests use isolated databases and mocked provider responses. They do not prove account eligibility, mandate approval, bank behavior, live credentials or actual webhook delivery. No real payments or dashboard configuration are performed by these tests.

Provider references: [Subscriptions integration](https://razorpay.com/docs/payments/subscriptions/integration-guide/), [subscription creation API](https://razorpay.com/docs/api/payments/subscriptions/create-subscription/), [webhook validation](https://razorpay.com/docs/webhooks/validate-test/), and [subscription states](https://razorpay.com/docs/payments/subscriptions/states/).
