# Customer shipment tracking and email

When an admin marks an order Shipped and saves a tracking number:

- The customer's My Orders page shows the courier, tracking number and Track shipment link. The shared account page supports all 15 storefront themes.
- An email alert is recorded in the same transaction as the shipment update, then queued after commit. Guest buyers can receive this email using the order's recorded customer email; My Orders requires the account that owns the order.
- Saving identical tracking details again does not create another alert. Changing the tracking number, courier or recipient records a new alert. The Edit shipment button is available while an order is Shipped.
- Missing/invalid customer email does not block shipping; no email alert is created for that recipient. No email is sent to the merchant as a fallback.
- Before sending, the worker checks the current shipment. Superseded, cancelled or already delivered shipments are skipped. Customer account pages show current saved details; they do not fetch live courier events.
- Tracking links accept HTTP/HTTPS only, encode the tracking number and reject another tenant's partner record. India Post opens its official portal for manual consignment entry.

## Deploy

```sh
php artisan migrate --force
php artisan view:clear
php artisan queue:restart
```

Configure working mail delivery in the server environment. This feature uses the existing `ORDER_NOTIFICATIONS_QUEUE_CONNECTION` (database by default) and `order-notifications` queue. No new provider keys are needed for courier links. Keep the existing worker supervised:

```sh
php artisan queue:work database --queue=order-notifications,default --sleep=1 --tries=1 --timeout=240
```

Use the actual configured connection if it differs from database. Its queue retry interval must exceed the job timeout. The server cron must invoke `php artisan schedule:run` every minute. The new scheduled command `notifications:deliver-shipments` enqueues due shipment alerts and recovers stale queue claims; it does not replace the worker.

The outbox (`customer_shipment_alerts`) records attempts, queue time, retry time, sent time and skipped time. SMTP failures retry up to six delivery attempts, with increasing delays. Inspect application logs and unsent alerts when attempts are exhausted. `sent_at` means the mail transport accepted the message, not that the buyer received or read it. As with SMTP generally, an uncertain process crash after transport acceptance can cause a duplicate email on retry.

No historical shipments are emailed automatically by the migration. Re-saving a shipped order records its current shipment notification. Automated tests use fake mail and queues; no real emails are sent by verification.
