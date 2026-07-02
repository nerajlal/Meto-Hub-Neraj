# Grocery Storefront Landing Page Wireframe & Theme Structure

This document outlines the complete section-by-section UI wireframe, CSS variables structure, and component hierarchy of **`template_1`** to serve as a reference template for building future storefront themes.

---

## 1. Core Layout Structure (HTML Grid & Flex Layout)

```
+-----------------------------------------------------------+
| [Header] Logo | Search (Green Accent) | Acc / Login | Cart |
+-----------------------------------------------------------+
|                                                           |
|  [Sidebar Menu]           [Main Content Area]             |
|  - All Products           +----------------------------+  |
|  - Weekly Combos          |  [Hero Banner]             |  |
|  - Category 1             |  Green gradient            |  |
|  - Category 2             |  Text & "Shop Now" button  |  |
|  - Category 3             +----------------------------+  |
|                           |  [USP trust bar]           |  |
|                           |  4 Items (Farm, Express)   |  |
|                           +----------------------------+  |
|                           |  [Category Section 1]      |  |
|                           |  Grid of Product Cards     |  |
|                           +----------------------------+  |
|                           |  [Weekly Combos Grid]      |  |
|                           |  Grid of Combo Cards       |  |
|                           +----------------------------+  |
|                           |  [Newsletter Banner]       |  |
|                           |  Dark bg, Email & Sub btn  |  |
|                           +----------------------------+  |
|  [Floating WhatsApp Btn]                                  |
+-----------------------------------------------------------+
| [Footer] Brand Info / Trust | Categories | Care | News    |
+-----------------------------------------------------------+
```

### Configurable Responsive Grid Columns (Mobile View)
To match merchant preferences, the product lists support dynamic mobile columns (compiled dynamically based on merchant configuration from the admin dashboard):
- **Class Helper**: `.grid-cols-mobile-{{ cols }}` appended to `.product-grid`.
- **1 Product per Row** (`.grid-cols-mobile-1`): Full-width cards for high-detail product catalogs.
- **2 Products per Row** (`.grid-cols-mobile-2` / Default): Balanced layout.
- **3 Products per Row** (`.grid-cols-mobile-3`): High-density layout with reduced padding/gaps (`gap: 0.5rem`) for compact view.

---

## 2. Design Tokens & Styling Constants (CSS Variables)

When creating a new theme matching this structure, define these core styling tokens in your primary stylesheet:

```css
:root {
    --primary-color: #0f172a;       /* Slate Dark for primary typography */
    --accent-color: #10b981;        /* Vibrant Emerald Green for primary brand focus */
    --accent-hover: #059669;        /* Hover state for primary buttons */
    --bg-light: #f8fafc;            /* Soft background gray for main content container */
    --border-color: #e2e8f0;        /* Light grey dividers and card outlines */
    --text-muted: #64748b;          /* Secondary typography */
}
```

---

## 3. UI Component Specs

### A. Sticky Header Component
- **Left**: Mobile Toggle Button (hidden on desktop) + Store Logo (`<i class="fa-solid fa-basket-shopping"></i>` + Tenant Name).
- **Middle**: Enhanced Search bar with:
  - Light background, matching accent border on focus.
  - Search dropdown appearing on focus/input with:
    - **Recent Searches** section (displayed first) with items having a clickable text area and a clickable "X" icon on the right to remove individual history entries.
    - **Product Suggestions** section (displayed after recent searches) showing matching product titles.
  - Search history stored in `localStorage` under keys like `t1_search_history` or `t2_search_history` (per theme). Max 8 entries, newest first.
  - When clicking on a recent search or suggestion, the form auto-fills and submits.
  - JS logic for handling search interactions and history management.
- **Right**: User profile action button + Shopping Cart Action Trigger displaying the dynamically calculated item quantity badge.

### Search Dropdown Component (Header)
- **Structure**: Two sections: "Recent Searches" (optional, visible only if history exists) followed by "Search Suggestions" (optional, visible only if input has text and matches products).
- **Recent Search Item Styling**:
  - `display: flex; flex-direction: row; justify-content: space-between;`
  - Left: History text (flex-grow:1) with clock icon (`fa-clock-rotate-left`).
  - Right: Remove button with "X" icon (`fa-xmark`).
- **Suggestion Item Styling**: Search icon (`fa-magnifying-glass`) + matching text with highlighted query.
- **LocalStorage Keys**: Theme‑specific keys (e.g., `t1_search_history` for template_1, `t2_search_history` for template_2).

### B. Hero Banner Component
- **Aesthetic**: Dynamic Banner Image Only (carousel layout using pure HTML/CSS/JS scroll snap).
- **Responsive Images**: Uses `<picture>` with desktop and mobile media query formats to dynamically load configured slider images:
  - *Desktop*: Renders `image_desktop` uploaded via the Admin Slider settings.
  - *Mobile*: Renders `image_mobile` uploaded via the Admin Slider settings.
- **Autoplay Carousel**: Seamless auto-scrolling with dots navigation indicator below the active slide.

### C. USP Trust Bar Component
- **Layout**: Grid (minmax 200px) containing 4 key trust cards.
- **Items**:
  1. *100% Farm Fresh* - Sourced directly from local farms.
  2. *2-Hour Delivery* - Express delivery to doorstep.
  3. *Hygienically Packed* - Handled with strict safety protocols.
  4. *No Questions Return* - Instant returns at delivery window.

### D. Product Card Component
- **Image Section**: Square ratio `100% padding-top` wrapper. Renders product thumbnail with fallback support.
- **Badges**:
  - *Popular / Trend Badge*: Bottom-left overlaid label.
  - *Pack Deal Badge*: Top-left purple/blue badge (`<i class="fa-solid fa-boxes-stacked"></i>` + "Pack Deal") visible only if volume/bundle discounts exist.
- **Details Section**:
  - Price: Bold, colored in green accent.
  - Title: 2-line truncated text clamp.
  - Meta: Product type + unit weight/size (e.g. `Vegetables • 500g`).
- **Action Button**: Floating circular absolute button (`+` icon) to add directly to cart.

### E. Combo / Bundle Card Component
- **Image Section**: Square product grouping thumbnail with a "Save Bundle" ribbon.
- **Details Section**: Price, title, and quantity of products included.
- **Action Button**: Add to cart with type indicator set to `'bundle'`.

---

## 4. DOM Hierarchy Reference

```html
<!-- Layout Wrapper -->
<div class="main-wrapper">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">...</aside>
    
    <!-- Main Content Container -->
    <main class="main-content">
        <div class="content-container">
            <!-- 1. Hero Section -->
            <div class="hero-banner">...</div>
            
            <!-- 2. USP Row -->
            <div class="usp-bar">...</div>
            
            <!-- 3. Category Product Rows -->
            <div class="department-section">
                <div class="section-header">...</div>
                <div class="product-grid">
                    <!-- Product Cards -->
                </div>
            </div>
            
            <!-- 4. Combos Rows -->
            <div class="department-section">...</div>
            
            <!-- 5. Footer -->
            <footer class="store-footer">...</footer>
        </div>
    </main>
</div>
```

---

## 5. Collection / Products List Page Layout

This page is loaded for specific categories (e.g. Vegetables, Dairy) or the global product catalog.

```
+-----------------------------------------------------------+
| [Header] Logo | Search (Green Accent) | Acc / Login | Cart |
+-----------------------------------------------------------+
|                                                           |
|  [Sidebar Menu]           [Main Content Area]             |
|  - All Products           +----------------------------+  |
|  - Weekly Combos          |  [Collection Header]       |  |
|  - Category 1             |  Collection Name           |  |
|  - Category 2             |  Total items count badge   |  |
|  - Category 3             +----------------------------+  |
|                           |  [Products Grid]           |  |
|                           |  Responsive grid of grocery|  |
|                           |  product cards             |  |
|                           +----------------------------+  |
|                           |  [Weekly Combos Grid]      |  |
|                           |  (Optional section)        |  |
|                           +----------------------------+  |
+-----------------------------------------------------------+
| [Footer] Brand Info / Trust | Categories | Care | News    |
+-----------------------------------------------------------+
```

### Components specific to this page:
- **Collection Header**: Renders the dynamic `$title` of the active category with a subtitle describing the items. Includes a card on the right displaying the total number of items found.
- **Empty State Display**: Shown if no products match the category. Features a shopping bag icon, a helpful text instruction, and a "Return Home / Shop All" primary action button.

---

## 5a. All Products Page Layout (Search Results)

This page displays all active products and combos, with special handling for search queries.

```
+-----------------------------------------------------------+
| [Header] Logo | Search (Green Accent) | Acc / Login | Cart |
+-----------------------------------------------------------+
|                                                           |
|  [Sidebar Menu]           [Main Content Area]             |
|  - All Products           +----------------------------+  |
|  - Weekly Combos          |  [All Products Header]     |  |
|  - Category 1             |  "All Products" or Search  |  |
|  - Category 2             |  Results (with count)      |  |
|  - Category 3             +----------------------------+  |
|                           |  [Products Grid]           |  |
|                           |  (Groceries FIRST!)       |  |
|                           |  Responsive grid of grocery|  |
|                           |  product cards             |  |
|                           +----------------------------+  |
|                           |  [Weekly Combos Grid]      |  |
|                           |  (Combos SECOND!)         |  |
|                           |  If searching: ONLY combos|  |
|                           |  containing search result  |  |
|                           |  products!                 |  |
+-----------------------------------------------------------+
| [Footer] Brand Info / Trust | Categories | Care | News    |
+-----------------------------------------------------------+
```

### Key Rules for All Products Page:
1. **Order**: Groceries/products are always shown **BEFORE** combos/bundles!
2. **Search Filtering**: When a search query is present:
   - Products filtered by `title` or `description` containing query.
   - Combos filtered to show **only** bundles that include at least one product from the search results!
3. **Search Database Columns**: Product search uses `title` (not `name`) and `description` fields!
4. **Removed Invalid Relationship**: Avoid using `"category"` relationship with `Product` model (it doesn't exist)!

---

## 6. General Content & Policy Page Layout

For simple text pages such as Shipping Policy, Return Policy, and Terms of Service.

```
+-----------------------------------------------------------+
| [Header] Logo | Search (Green Accent) | Acc / Login | Cart |
+-----------------------------------------------------------+
|                                                           |
|             [Centered Main Content Container]             |
|             Max-width: 800px                              |
|             Card background (#fff) with border            |
|             +---------------------------------------+     |
|             |  [Page Title (H1)]                    |     |
|             |  [Introductory Paragraph]             |     |
|             |  [Policy Sections (H2 + body text)]   |     |
|             +---------------------------------------+     |
|                                                           |
+-----------------------------------------------------------+
| [Footer] Brand Info / Trust | Categories | Care | News    |
+-----------------------------------------------------------+
```

---

## 7. Product Detail Page Layout

For individual product specifications, sizing options, and bulk pack offers.

```
+-----------------------------------------------------------+
| [Header] Logo | Search (Green Accent) | Acc / Login | Cart |
+-----------------------------------------------------------+
|                                                           |
|             [Breadcrumb Navigation]                       |
|             Home > Shop > Product Name                    |
|             +---------------------------------------+     |
|             |  [Product Gallery] | [Product Info]   |     |
|             |  Main Image        | Category / Badge |     |
|             |  Thumbnails Grid   | Product Title    |     |
|             |                    | Current/Compare  |     |
|             |                    | Price            |     |
|             |                    |------------------|     |
|             |                    | Variant Select   |     |
|             |                    | (Weight Options) |     |
|             |                    |------------------|     |
|             |                    | Volume Pack Deals|     |
|             |                    | (dashed borders) |     |
|             |                    |------------------|     |
|             |                    | Quantity Selector|     |
|             |                    | & Add to Bag btn |     |
|             |                    |------------------|     |
|             |                    | Description Tabs |     |
|             +---------------------------------------+     |
|                                                           |
|             [Related Products / Recommendations]          |
|             Grid of 4 Recommended Product Cards           |
|                                                           |
+-----------------------------------------------------------+
| [Footer] Brand Info / Trust | Categories | Care | News    |
+-----------------------------------------------------------+
```

### Components specific to this page:
- **Variant Selector**: Dynamic pill elements allowing selection of weight options (e.g. `500g`, `1kg`) which update the displayed price dynamically.
- **Volume Pack Deals**: Highlighted DAShed card rows displaying special bulk pack offers (e.g. `Pack of 3 - Save ₹60 instantly`) linked to the bundle cart controller.
- **Related Products**: Underneath the main detail grid, loops through similar items in the same collection.

---

## 8. Combos & Weekly Deals Page Layout

This page lists all special mix & match combos, bundle deals, and volume package offers.

```
+-----------------------------------------------------------+
| [Header] Logo | Search (Green Accent) | Acc / Login | Cart |
+-----------------------------------------------------------+
|                                                           |
|  [Sidebar Menu]           [Main Content Area]             |
|  - All Products           +----------------------------+  |
|  - Weekly Combos          |  [Collection Header]       |  |
|  - Category 1             |  "Weekly Grocery Combos"   |  |
|  - Category 2             |  Total combos count badge  |  |
|  - Category 3             +----------------------------+  |
|                           |  [Combos Grid]             |  |
|                           |  Grid of Combo Cards       |  |
|                           |  showing savings value     |  |
|                           +----------------------------+  |
+-----------------------------------------------------------+
| [Footer] Brand Info / Trust | Categories | Care | News    |
+-----------------------------------------------------------+
```

### Components specific to this page:
- **Combo Card**: Rendered with a square thumbnail layout, including a "Save Bundle" or "Volume Deal" top-right badge, original retail price vs discount price display, and an add-to-bag action specifying type as `'bundle'`.
- **Empty State Display**: Displays a layer-group icon, description, and redirect button back to the main catalog if no active bundles exist in the tenant's store.

---

## 9. Dynamic Storefront Content Management

All static text pages load their copy dynamically from the tenant's settings columns in the database. If no custom values are defined by the administrator, the storefront reverts to standard premium grocery defaults.

### Content Sources:
- **About Page**:
  - Hero Title: `{{ $currentTenant->about_title }}` (Defaults to: *Cultivating Health & Happiness*)
  - Main Description Body: `{{ $currentTenant->about_text }}`
- **Contact Page**:
  - Support Email: `{{ $currentTenant->contact_email }}`
  - Support Phone: `{{ $currentTenant->contact_phone }}`
  - WhatsApp Number: `{{ $currentTenant->whatsapp_number }}`
  - Shop Address: `{{ $currentTenant->contact_address }}`
- **Policy Pages**:
  - Shipping Policy: `{{ $currentTenant->shipping_policy }}`
  - Return Policy: `{{ $currentTenant->return_policy }}`
  - Terms of Service: `{{ $currentTenant->terms_of_service }}`

---

## 10. Dynamic Layout-Independent Checkout Page

To ensure checkout logic and structure is reusable across all storefront themes, the checkout template is hosted centrally in the `Checkout` namespace.

### Architecture:
- View Path: [checkout.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/Github/Grocery/resources/views/Checkout/checkout.blade.php)
- Layout Inheritance: Extends `$layout` dynamically passed from the controller (`@extends($layout ?? 'template_1.layouts.app')`). This allows the same checkout to render seamlessly inside any active theme's wrapper header/footer.
- Fields: Contact details (Auto-filled for authenticated users), Delivery address (Street, City, State, PIN code), and Payment Selection (Cash on Delivery / Razorpay Online options).

---

## 11. Backend Pricing Rules Engine (Theme-Agnostic)

When building or integrating new storefront themes, price rendering (such as on product cards, catalog lists, cart totals, and checkout templates) should **always** reference `$product->starting_price` or `$variant->price` to ensure that custom pricing is correctly rendered.

### Pricing Override Priorities:
1. **Individual Custom Price**: Checked first. If a record in `custom_prices` exists for the logged-in customer and product, that price is returned.
2. **Group Custom Price**: Checked second. If the logged-in customer belongs to a `customer_group` that has a custom price override defined for the product in `group_custom_prices`, that price is returned.
3. **Standard Price**: Default value stored in the `product_variants` database table.

### Guidelines for Theme Creators:
- Do **NOT** read product prices directly from raw query builder calls (e.g. database selections bypassing Eloquent).
- **Always** retrieve prices via the Eloquent models (`$variant->price` or `$product->starting_price`) so that the model accessors correctly intercept and apply custom overrides.

---

## 12. Storefront Branding, Color Theming & Currency Configuration

All storefront themes support fully dynamic branding, colors, and currency symbols configured from the admin dashboard under **Settings → Storefront → Branding & Colors**.

### Database Fields (`tenants` table):
| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `logo` | `string (nullable)` | `null` | Path to uploaded store logo file (stored in `public/logos/`) |
| `primary_color` | `string(7)` | `#10b981` | Primary accent color (buttons, badges, links, prices) |
| `dark_color` | `string(7)` | `#0f172a` | Dark/navy color (headers, typography, navbars) |
| `accent_color` | `string(7)` | `#ecfdf5` | Light accent color (backgrounds, highlights) |
| `currency` | `string(3)` | `INR` | Default currency code (INR, USD, EUR, GBP, AED, CAD, AUD) |

### CSS Variable Mapping (in `layouts/app.blade.php`):
```css
:root {
    --primary-color: {{ $currentTenant->dark_color ?? '#0f172a' }};
    --accent-color: {{ $currentTenant->primary_color ?? '#10b981' }};
    --accent-hover: {{ $currentTenant->primary_color ?? '#10b981' }};
    --bg-light: {{ $currentTenant->accent_color ?? '#f8fafc' }};
}
```

All theme components that use `var(--accent-color)`, `var(--primary-color)`, and `var(--bg-light)` will automatically pick up the merchant's custom palette without any template modifications.

### Color Presets (Admin UI):
| Preset Name | Primary | Dark | Accent |
|-------------|---------|------|--------|
| Emerald (Default) | `#10B981` | `#064E3B` | `#ECFDF5` |
| Indigo Def | `#4F46E5` | `#0F172A` | `#E0E7FF` |
| Edu Orange | `#FF6B35` | `#2B2D42` | `#F7FFF7` |
| Royal Blue | `#1D4ED8` | `#1E3A8A` | `#EFF6FF` |

### Dynamic Logo Rendering:
In both theme headers, the logo is rendered conditionally:
```blade
@if($currentTenant->logo)
    <img src="{{ Storage::url($currentTenant->logo) }}" alt="{{ $currentTenant->name }}" style="max-height: 45px;">
@else
    <i class="fa-solid fa-basket-shopping"></i>{{ $currentTenant->name }}
@endif
```

### Currency Symbol System:
- **Blade Directive**: `@currency` outputs the correct symbol (e.g. `₹`, `$`, `€`, `£`, `د.إ`) based on `$currentTenant->currency`.
- **JS Global**: `window.storeCurrencySymbol` is set in the layout `<head>` for use in dynamic JS price calculations (cart updates, variant switching).
- **Response Middleware**: `FormatCurrencyResponse` middleware globally replaces hardcoded `₹` symbols with the tenant's configured currency symbol in the final HTML response.

### Supported Currencies:
| Code | Symbol | Name |
|------|--------|------|
| INR | ₹ | Indian Rupee |
| USD | $ | US Dollar |
| EUR | € | Euro |
| GBP | £ | British Pound |
| AED | د.إ | UAE Dirham |
| CAD | $ | Canadian Dollar |
| AUD | $ | Australian Dollar |

### Guidelines for Theme Creators:
- **Always** use CSS variables (`var(--accent-color)`, etc.) for colors instead of hardcoding hex values.
- **Always** use `@currency` or `window.storeCurrencySymbol` for currency symbols instead of hardcoding `₹`.
- When rendering the store logo, always include the `@if($currentTenant->logo)` conditional with a text/icon fallback.

---

## 13. Wishlist System

Allows authenticated users to save products they want to buy later. Supports listing, adding, and removing items from the wishlist.

### Database Schema (`wishlists` table):
- `user_id` (foreignId to `users`, constrained, cascade on delete)
- `product_id` (foreignId to `products`, constrained, cascade on delete)
- `tenant_id` (foreignId to `tenants`, constrained, cascade on delete)
- Unique composite index on `[user_id, product_id, tenant_id]` to prevent duplicate wishlist items.

### Frontend Component Requirements:
- **Product Cards**: A heart icon button overlaid on the image. Clicking toggles the wishlist status via AJAX.
- **Header Action**: A heart icon link displaying the dynamic count of wishlisted items (styled as a small circular counter badge). If not logged in, clicking the link or the heart icon redirects the user to the login page.
- **Wishlist Page (`/wishlist`)**: Displays all wishlisted items in a standard product grid. Users can quickly remove items or add them to the cart directly from this page.

---

## 14. Geolocation Address Autofill at Checkout

Provides two options to set the delivery address dynamically during checkout, reducing typing effort and improving conversion rates.

### Interactive Options on Checkout Address Section:
1. **Get Current Location (Phone GPS / HTML5 Geolocation API)**:
   - Uses browser coordinates (`navigator.geolocation.getCurrentPosition`).
   - Translates coordinates to a human-readable address via Google Maps Geocoding API or a fallback open-source reverse-geocoding endpoint.
   - Automatically fills in the address input field, city, state, and postal code fields.
2. **Google Maps Place Autocomplete**:
   - Integrated with the delivery address input field.
   - As the user types, it suggests complete addresses.
   - Selecting a suggestion automatically parses and autofills the address fields.

---

## 15. Storefront Tax Configuration & Calculation (Cart & Checkout)

Merchants can configure country‑specific tax rules from the admin settings page to be calculated and displayed **both in the cart and at checkout**.

### Tax Configuration Fields (`tenants` and `orders` tables):
- `tax_name` (`string(50)`): Name of the tax (e.g., `GST`, `VAT`, `Sales Tax`).
- `tax_rate` (`decimal(5,2)`): Percentage rate (e.g., `18.00`, `5.00`).

### Tax Calculation Logic:
- **Basis**: Calculated on the **cart subtotal before tax** (after discounts/savings are applied).
- Formula:
  $$\text{Tax Amount} = \text{Cart Subtotal (Before Tax)} \times \left( \frac{\text{Tax Rate}}{100} \right)$$
- **Rounding**: Tax amount is rounded to 2 decimal places.
- **Total Calculation**: Final order total = Cart subtotal before tax + Tax amount.

### Tax Display Locations:
1. **Checkout Page**: Already configured (as original spec).
2. **Cart Page (template_1/cart.blade.php, template_2/cart.blade.php)**:
   - Shows subtotal (before tax), then tax (if configured), then grand total.
   - Tax line labeled with `tax_name` and `tax_rate` (e.g., "GST (18%)").
3. **Cart Drawer Items Partials (template_1/partials/cart_drawer_items.blade.php, template_2/partials/cart_drawer_items.blade.php)**:
   - Shows subtotal, tax (if configured), and grand total in the drawer summary.
4. **CartController Methods**:
   - All cart methods (`index`, `v3Index`, `ajmalCart`, `afnanCart`, `fetch`, `update`, `remove`) must calculate and pass:
     - `$taxAmount`: Calculated tax value.
     - `$taxRate`: Configured tax percentage (or null).
     - `$taxName`: Configured tax name (or null).
     - `$cartTotalBeforeTax`: Cart total without tax (from CartService).
     - `$total`: Final total including tax.

### Checkout Logic (Already implemented):
- If a tax rate is configured, the system computes the tax amount dynamically at checkout.
- The tax is displayed as a separate line item (e.g., `GST (18%): ₹180.00`) in the order summary card.
- The order total includes the subtotal, delivery charge, and the computed tax amount.
- The configured `tax_name`, `tax_rate`, and calculated `tax_amount` are saved in the `orders` table upon successful order placement.

---

## 16. My Account (Profile & Orders) Page Layout

Both themes must have account pages for profile management and order tracking!

### Components:
- **Account Header**: Title, subtitle, and sign-out button.
- **Account Sidebar Nav**: Sticky sidebar with links to Profile Info and My Orders, with active state styling.
- **Profile Info Panel**: Displays user's name, email, and form for editing default address.
- **Orders List Panel**: Shows order history with order number, date, status, total, and item details.
- **Account Views Location**:
  - `resources/views/template_1/account/index.blade.php` (profile page)
  - `resources/views/template_1/account/orders.blade.php` (orders page)
  - `resources/views/template_2/account/index.blade.php` (copy of template_1, using template_2 layout)
  - `resources/views/template_2/account/orders.blade.php` (copy of template_1, using template_2 layout)

### AccountController:
- `account.index`: Redirects to `account.orders`
- `account.profile`: Shows user profile and address edit form
- `account.orders`: Shows order history
- `account.orders.reorder`: Adds all items from a previous order back to the cart (supports both products and bundles, and both guest and authenticated user carts)
  - Keeps existing cart items (does NOT clear cart)
  - If an order item already exists in the cart, the quantity is increased
  - After adding items, redirects directly to checkout

### Order History Page:
- Each order card has a "Reorder" button (shown regardless of status)
- When reorder button is clicked, all items (products + bundles) are added back to the cart
- After reorder, user is redirected to the checkout page with a success message




