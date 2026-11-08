# Superadmin reporting

The `/superadmin` dashboard reports platform subscriptions separately from merchant order value. `/superadmin/payments` provides paginated payment history, including failed attempts, searchable by store, payment/invoice reference, status, currency and attempted date. Both routes require superadmin access.

## Revenue

- Selected dates are inclusive calendar dates in `config('app.timezone')`. Default: current month through today. Current/previous month cards use calendar months.
- Only captured/refunded subscription payments with a paid date up to now contribute. Authorized/failed payments never count as revenue. Amounts are stored in minor units and displayed divided by 100, separately per currency.
- Net collections = gross captured amounts minus cumulative refunds recorded on those payments. Refund dates and gateway fees are not stored, so this is neither profit nor a refund-date cash-flow report.
- Confirmed payments without a paid date are flagged and excluded from date reports; reconcile the affected store to recover authoritative provider data.

## Stores

Current paid coverage follows `PlanAccess::hasPaidAccess`, including verified billing dates, paid count and the tenant's current gateway subscription. An active status alone is insufficient. A cancelled subscription may retain access until its paid coverage ends.

Plan rows distinguish paid coverage, trial stores without paid history, and other records. Paid rows use the current subscription's plan; remaining stores use the selected tenant plan. Monthly/yearly counts include only current paid stores. Unknown billing cycles are shown separately.

Signup cohort conversion means stores created in the selected dates that have paid at least once as of now. It is not a period-on-period conversion rate or a projection of trials that have not finished.

Renewal follow-ups show the earliest 12 expired paid-coverage records and automatic renewals scheduled within seven days. Cancelled/cancel-at-cycle-end subscriptions are not assumed to renew. Admin grace and storefront grace use the configured durations and may overlap. Coverage expiry does not prove debt: overdue invoice amounts require an issued/partially paid invoice with a past due date and positive balance.

Gateway status counts remain separately labeled because they represent synchronized provider state rather than entitlement. All reporting depends on saved webhook/reconciliation records; no provider API is called by these pages.

## Deployment and checks

No migration, credentials or new package installation is needed. Deploy the changed application/views with the normal release process and refresh cached routes/views if enabled.

`php artisan test --filter='SuperAdmin|SubscriptionLifecycleTest'`

For isolated layout previews: create an output directory, then run `php tests/browser/render_superadmin_fixture.php /path/to/output`. The fixture uses SQLite in memory and never modifies the configured application database.
