# Automatic Bundles & Offers collection

Each store gets one system-managed collection when its first combo bundle, pack or pool is created. The collection appears under Admin > Products > Collections. Its name, description, cover image, SEO settings and status can be edited normally. New offers continue to use the same collection even after renaming it.

The collection uses an internal `system_key=offers`, unique per tenant, rather than identifying it by its editable name or slug. Bundles link through `bundles.collection_id`. Creation is serialized using a tenant row lock and also protected by a database unique constraint. All bundle creation entry points share the model hook, including packs created from product editing and superadmin store access.

## Storefront

The normal collection URL renders an offer-specific page with the selected theme's header and footer, the uploaded cover, active combos and packs, and active mix-and-match pools. Pools show qualifying products rather than a bundle Add button, preserving the existing basket discount calculation. Draft offers and other tenants' offers are excluded. A Draft collection returns 404 and disappears from navigation. Its products remain in their original product collections.

Pantry Select uses the collection's cover in the category row instead of its old static Bundles & packs tile. Fixed bundle homepage shelves continue to use their existing placement. Automatic collections are excluded from normal product category selectors. The admin list and detail page show offer counts and links. To remove it from the storefront, set it to Draft; deleting the automatic collection through admin is prevented to protect its links.

## Deployment

Run `php artisan migrate --force` inside the application container after deploying. Migration `2026_11_07_140000_add_automatic_offer_collections` adds the marker and relationship, creates one collection per existing offer-owning tenant and assigns that tenant's existing bundles, packs and pools. Offers without an identifiable tenant are not reassigned. Existing products and their collection IDs remain untouched. This migration has been applied locally.

No backfill command or manual category assignment is needed. Subsequent creations are assigned automatically, including when the owner customizes the collection cover or name.

## Validation

Feature tests cover all offer types, the per-store singleton, renamed collections, uploaded images, draft visibility, protection from deletion, storefront rendering in three theme families, superadmin tenant resolution and migration backfill. Isolated browser fixtures check cover images, pool product cards and responsive layouts in Classic Grocery, Fresh Market and Pantry Select.
