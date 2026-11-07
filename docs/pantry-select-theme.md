# Pantry Select (template_31)

A new specialty-store theme inspired by the layout of https://alfaquilon.com/, built with MetoHub's own storefront components. It appears in merchant Settings > Theme and the public /themes directory. It does not change the theme of an existing store automatically.

## Merchant content and colors

- Primary color controls buttons, card selections and search highlights.
- Dark color controls the announcement strip and footer. Text contrast is calculated from the saved colors.
- Accent controls subtle surfaces; the navigation uses a light tint of Primary.
- Store name, logo, navigation collections, contact links and cart counts come from the active tenant.
- Hero text uses the existing editable hero fields in Settings > Storefront.
- Hero slides use the existing desktop/mobile banner uploads. Recommended desktop artwork: 2040 × 600 px. Mobile: 1200 × 750 px. Slides autoplay with clickable indicators and pause for reduced motion, hover and keyboard focus.
- Delivery text appears only when the merchant has saved delivery information.
- The reference site's certifications, shipping promises, reviews, logo and images are not included.

## Commerce

The homepage has a wide banner, horizontal collection tiles and existing dynamic product shelves, bundles/packs, reels and published reviews. Catalog filtering and checkout use the existing application flow.

Product cards show up to three variant choices, an additional-options link when needed, selected prices and images, stock availability, wishlist and the existing basket actions. The product page has a dedicated two-column gallery and purchase layout, a mobile fixed purchase bar and product/delivery information below.

## Validation

Theme tests cover selection and previews plus real home, catalog, product and checkout rendering. Browser fixtures use an isolated in-memory database and verify mobile/desktop overflow, the fixed header, the mobile purchase bar and card variant switching. No real customer orders or payment requests are made by these checks.

## Homepage highlight strip

Settings > Storefront > Homepage highlight strip controls the scrolling icon labels. Enter up to eight lines (80 characters per line); an empty field hides the strip. Existing stores start with neutral shopping labels. Merchant-supplied text is escaped and stored per tenant; icons are locally hosted SVG artwork from the reference section supplied by the user (alfaquilon.com CDN), with matching 40px artwork, 8px circle padding and 15-second scrolling. Animation pauses on hover and focus and stops for reduced-motion preferences.

Deployments must run `php artisan migrate --force` inside the application container to add the nullable `tenants.trust_badges` JSON column. This migration has been applied to the local development database.

## Per-offer promotional backgrounds

Bundle Create/Edit and Pack Edit include a Promotional card background panel. Choose Theme default, Solid color, or Gradient; gradients have two colors and a 0–360 degree direction. Color pickers and six-digit hex inputs stay synchronized, and the panel includes a live preview. Theme default clears the override.

The saved JSON is in `bundles.card_background`. Only validated modes, hex colors and bounded angles are accepted; rendering constructs CSS from these values rather than accepting arbitrary CSS. Text contrast follows the selected solid color or gradient midpoint. Older requests that omit the panel preserve the existing override on edit, and pack editing preserves variant quantities.

These settings affect Pantry Select's wide promotional cards on its homepage. Other themes retain their own card layouts. Deploy migration `2026_11_07_160000_add_bundle_card_background` with `php artisan migrate --force` in the app container; it has been applied locally.

## Story section below reviews

Pantry Select displays a two-column story section below customer reviews, using the saved About title and description, a soft brand-tinted background and a link to the About page. On mobile, the image sits above the text. The saved story image is shared with the About page; the store logo or initial is the fallback.

Upload or remove the image under Settings > Storefront > About Us Page Content > Store story image. Supported formats are JPG, PNG and WebP, up to 5 MB. This uses the new `tenants.about_image` field: run `php artisan migrate --force` in the app container after deployment. Migration `2026_11_07_180000_add_about_image_to_tenants` has been applied locally.
