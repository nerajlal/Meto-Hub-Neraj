# Merchant order documents and payment recording

Open an order under Orders to access Shipping Label, Invoice / Save PDF, and Mark as paid.

- Shipping Label prints the store's configured contact address and phone, delivery address, item quantities and sizes, tracking number, and outstanding COD collection amount. Configure the sender address under Storefront → Contact Page Details before dispatch. This is a packing label; it does not book a courier or purchase postage.
- Invoice prints saved order item prices, subtotal, shipping, discounts, tax amount and total. Print → Save as PDF downloads through the browser. The order number identifies the document. This is an order invoice, with no statutory tax-invoice numbering or tax-registration setup implemented.
- Mark as paid records receipt of the full payment. It never charges or transfers money, and preserves the existing fulfillment status. Staff must confirm receipt first. A database transaction and row lock make repeated submissions preserve the original recording time and staff ID. Cancelled orders cannot be marked paid. Already-paid legacy orders remain paid without an invented audit timestamp.
- Documents and payment actions require the authenticated store administrator and existing tenant scope. Document responses use private, no-store caching. Only valid fulfillment states and delivery partners from the same tenant are accepted.

Deployment: run `php artisan migrate --force` after deploying. The migration adds nullable `payment_recorded_at` and `payment_recorded_by` fields to orders. No Composer dependency was added for these changes.

Verification: `php artisan test --compact --filter=OrderDocumentsTest` covers payment idempotency/auditing, cancellation, tenant isolation, document contents/totals, and status validation.
