# Fresh local demo database

The default DatabaseSeeder now runs FreshDemoSeeder. It refuses non-local/non-testing environments and nonempty databases. The reset on 17 October 2026 affected only the local `metohub` database.

Seven stores are created:

| ID | Store | Slug | Billing example |
|---|---|---|---|
| 1 | Thread & Form | mens-clothing | Trial |
| 2 | Fresh Basket | grocery | Paid Pro |
| 3 | Circuit Cart | gadgets | Pending payment |
| 4 | Aurelia Jewels | jewelry | Paid Pro |
| 5 | Hour House | watches | Expired |
| 6 | Loom & Grace | womens-clothing | Trial |
| 7 | Maison Mist | perfume | Halted |

Each store has three collections, nine products with two variants each, locally generated illustrative catalog images, a bundle, a coupon, a delivery partner, five customers, and five orders covering fulfillment statuses. Stock includes low-stock variants. Images are sample illustrations rather than product photographs. Old uploaded files are not deleted by a database reset.

Owner emails: `demo1@gmail.com` through `demo7@gmail.com`, in the table order above. Owner password: `password`. Super admin: `superadmin@metohub.test`. Generated credentials, account URLs, and the shared random demo password are stored only in the ignored private file `storage/app/private/demo-credentials.json` (permissions 0600). The super admin password is regenerated on every seed and must not be used for real stores. Log out of previous sessions before signing in.

Subscription invoices and captured payments are explicitly sample records. Local/testing billing reconciliation skips `is_demo` subscriptions, so sample records are not reconciled with Razorpay. No payment was made and no email or push was sent. Notification history uses simulated already-delivered entries, with one unread order per store and no jobs queued. `.test` email domains are examples. Trial dates and paid periods are relative to the seed date. Pending/expired/halted stores intentionally have restricted write access; the trial and paid stores support active admin testing.

To intentionally replace the entire local database again:

```sh
php artisan migrate:fresh --seed --force
```

This destroys all database records. Never run that command against a real customer database. The seeder guard runs after migrations; it does not protect a remote database from the destructive migration command.

Verification: FreshDemoSeederTest checks counts, all storefronts, all owner dashboards, super admin access, and sample billing reconciliation. The local reset also verified that all 63 catalog image files exist.

## Revised demo presentation

The seven stores now use seven different templates: men's clothing → Atelier Edit, grocery → Fresh Market, gadgets → Vista Shop, jewelry → Maison Luxe, watches → Monochrome Edit, women's clothing → Bloom Avenue, perfume → Noir Collection. `DemoStoreDesign` supplies separate palettes, logo monograms, category-specific homepage copy, and desktop/mobile banners built from the local demo illustrations. It runs during fresh seeding and can also be applied individually without resetting store records or credentials. Store owners can change these settings normally in the admin.

All seven rendered homepages were checked at 390px and 1440px widths for horizontal overflow. Sample assets are illustrations, not product photography.
