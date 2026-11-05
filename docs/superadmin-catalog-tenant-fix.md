# Catalog records missing after superadmin creation

Affected flow: superadmin → Open store admin → create collection/product. Reported production store: tenant 11.

## Cause

TenantScope reads records using the validated request `resolved_tenant_id`. Previously, the BelongsToTenant creating hook filled missing tenant IDs only from the logged-in user's tenant ID. A superadmin normally has no tenant ID. Collection/Product create requests do not supply tenant_id themselves, so inserts could succeed with tenant_id NULL and immediately disappear from scoped store lists.

## Fix

BelongsToTenant now uses the validated request tenant before the authenticated account tenant when assigning a missing tenant_id. Explicit tenant IDs supplied by trusted services/seeders remain unchanged. Session-only state is not used for new-record ownership. No schema change is required.

Regression coverage creates collections and products both as merchant and superadmin, with another store deliberately present in the session and an injected tenant_id in submitted data. Records must belong to the selected store, appear in its admin and storefront, and remain absent from the other store's scoped lists.

## Deploy and recover

Deploy the corrected application image using the existing test→production procedure. The fix applies to new records; it does not automatically reassign historical records. Do not assign all NULL-tenant rows to store 11: they can belong to other stores or historical imports.

First inspect orphan candidates through the production app container (read-only):

```bash
cd /srv/apps/metohub-next/runtime

docker compose --project-name metohub-next-test exec -T -u www-data app php artisan tinker --execute="dump(App\Models\Collection::withoutGlobalScopes()->whereNull('tenant_id')->latest('id')->limit(30)->get(['id','name','created_at'])->toArray()); dump(App\Models\Product::withoutGlobalScopes()->whereNull('tenant_id')->latest('id')->limit(30)->get(['id','title','collection_id','created_at'])->toArray());"
```

Identify exact product/collection IDs with the merchant using titles and creation times. Take a database backup, then reassign only confirmed rows in a transaction. Verify each product's collection and any related tenant-owned bundles/discounts/featured records. Product variants/images follow product_id and do not need an invented tenant field. Check linked records individually before recovery. No live reassignment has been performed by this code change.
