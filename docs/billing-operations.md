# Subscription billing operations

Updated 15 October 2026. Application changes are implemented locally; this document is the production activation runbook. Platform subscriptions and customer store payments are separate. Storefront payments remain COD-only; seller split settlements remain the proposal in `split_pay.md` until provider approval and a separate integration.

## Production activation

1. Deploy the complete release, including `config/saas.php`, billing models/services/controllers, views, routes and all subscription-tracking migrations. Back up the existing database and uploads first; preserve `APP_KEY`.
2. Set `RAZORPAY_KEY`, `RAZORPAY_SECRET` and a separate `RAZORPAY_WEBHOOK_SECRET` in the server environment. Do not share secrets in chat or commit them. Confirm the provider account is authorized for subscriptions and the required mandate methods. Test and live credentials must not be mixed.
3. In the Razorpay dashboard, register `https://betametohub.metora.in/billing/razorpay/webhook`. Configure the same webhook secret and subscribe to `subscription.authenticated`, `subscription.activated`, `subscription.charged`, `subscription.updated`, `subscription.pending`, `subscription.halted`, `subscription.cancelled`, `subscription.completed`, `subscription.paused`, and `subscription.resumed`. Reconciliation also imports provider state `expired`.
4. Run the deployment commands from the actual project directory:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan billing:reconcile
php artisan app:preflight
```

5. Configure the hosting scheduler to run `php artisan schedule:run` every minute. Laravel schedules `billing:reconcile` hourly with overlap protection. Use an absolute PHP binary and project path appropriate to the host. Monitor command exit codes and output. A command registered in code does not prove the host scheduler is running.
6. Import existing subscriptions with reconciliation before relying on complete payment history. Import uses provider subscription IDs, not a guessed price from an old plan name. Existing active legacy records retain their previously recorded access until their first provider reconciliation; the verified provider invoice period then controls access. Review these legacy records during rollout.
7. Complete a sandbox test of initial authorization, successful payment, failed renewal, duplicate events, cancellation and reconciliation. Confirm the configured live account separately with a controlled transaction authorized by the business owner. No real transaction has been executed by the coding agent.

## Record meanings

- Prices are in INR; ledger amounts are integer paise. Current offers are Base 99/month, Pro 249/month and Enterprise 749/month, with eleven monthly payments for an annual subscription.
- Choosing a plan starts checkout; it does not immediately grant the selected paid tier. Authorization alone does not establish a paid period. Paid invoice billing-start and billing-end timestamps determine paid access. A failed renewal can advance the provider’s current period without paying it; that unpaid interval never extends access.
- The plan, agreed amount, billing cycle, provider subscription/customer references, period end, next charge, payments, refunded amounts and invoices are recorded. Historical fields remain unknown until imported.
- Outstanding amounts come from issued/partially paid invoices, grouped by currency. A failed checkout is not automatically treated as a debt. The invoice synchronization timestamp indicates freshness.
- The printable receipt acknowledges a recorded captured payment and includes refunds. It is not a substitute for a tax invoice. Configure tax invoicing with the accountant/provider as required.
- Store order value in superadmin remains separate from SaaS subscription payments.

## Duplicate and uncertain requests

A shared cache lock and a database-unique checkout reservation protect subscription creation. Existing pending checkout for the same plan/cycle is reused after provider verification. An active, paused, halted or authorized subscription must be managed before starting another. A remaining paid period is preserved after cancellation.

A network failure after a subscription request can leave the result uncertain. Do not manually retry creation or delete the record to bypass the guard. Run:

```bash
php artisan billing:reconcile --tenant=9
```

The command searches provider subscriptions for the original checkout-attempt reference and reconnects the record if found. If no match is confirmed, it exits unsuccessfully and leaves the attempt blocked for provider review. Only an operator who has verified the provider result should repair an unresolved record; never create a replacement blindly.

Webhook signatures are checked against the exact raw request body. Only this endpoint is exempt from CSRF checks. Event IDs deduplicate successfully processed events. The handler fetches the provider’s current subscription state, so delayed events do not directly restore an old state. Failed processing is retried by the provider; scheduled reconciliation provides recovery when delivery is missed. Payment/card/contact payloads are not stored wholesale.

## Cancellation, retries and access

Cancellation is explicitly requested by the owner in Billing. Cancellation of a paid subscription is requested at the end of its period; an unpaid pending subscription can be cancelled immediately. A refund does not itself define a subscription cancellation policy: support must review and cancel ongoing billing separately where appropriate.

Payment retry execution remains with Razorpay’s subscription system. MetoHub does not create additional collection requests in response to a failed webhook. The application records pending/halted states and offers refresh/status management.

After a trial or verified paid period ends, subscription management, receipts and historical records remain accessible. Store mutations and new customer checkout are blocked until access is restored. Pro/Enterprise entitlement checks protect advanced analytics, custom domain configuration/routing and removal of storefront branding. Trials can preview these features. Unbuilt warehouse, abandoned-cart recovery and general gateway integration promises have been removed from the current plan offers.

## Provider references

- [Subscription integration](https://razorpay.com/docs/payments/subscriptions/integration-guide/)
- [Subscription webhook events](https://razorpay.com/docs/webhooks/subscriptions/)
- [Webhook verification and duplicate handling](https://razorpay.com/docs/webhooks/validate-test/)
- [Official PHP invoice SDK reference](https://github.com/razorpay/razorpay-php/blob/master/documents/invoice.md)

Recheck provider capabilities and account eligibility during deployment; documentation is not account activation.

## Email, backups and monitoring

The existing production runbook covers SMTP for `info@metora.in`, SPF/DKIM/DMARC, actual inbox delivery, encrypted off-server backups, restoration rehearsals, monitoring and representative staging load tests. These need the production environment. The application preflight checks configuration but does not prove SMTP delivery, gateway activation, scheduler operation, restore success or server capacity.

Before public launch, retain evidence of:

- A new merchant receiving and completing email verification; an expired/reused verification link being rejected; password reset delivery succeeding.
- Successful sandbox subscription and failure/cancellation cases, plus the owner-approved live transaction and webhook delivery.
- Recent successful backup and an isolated restoration rehearsal.
- Scheduler execution and successful reconciliation timestamps.
- External HTTPS/uptime monitoring and application error alerts.
- A representative staging checkout/load test and a controlled real COD order with stock and totals verified.

Until these are performed, describe the release as locally tested and ready for production validation, not fully verified live.
