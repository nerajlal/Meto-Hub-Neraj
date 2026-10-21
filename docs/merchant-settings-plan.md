# Merchant Settings overview — design and implementation plan

Date: 21 October 2026

## Purpose

Create one clear starting point for merchants managing their store. Use the supplied screenshot’s grouped settings rows, restrained colors, store identity and subscription summary. Adapt it to MetoHub’s actual capabilities and existing admin navigation.

Implemented on 21 October 2026. The overview follows this specification; no database changes or new integrations are required.

## Page and navigation

- New URL: `/{tenant}/admin/settings`, named `admin.settings.index`.
- Use one Settings sidebar entry linking to the overview.
- Keep current settings URLs working. Detailed settings shortcuts live in the overview rather than the sidebar.
- Use the existing admin layout, header, mobile navigation and authenticated merchant session.
- Resolve the tenant from the middleware’s `resolved_tenant_id`; do not fall back to tenant 1.
- Keep the overview readable after trial expiration so the merchant can reach Billing. Existing subscription middleware continues controlling writes on destination pages.

## Visual direction

- Use the admin’s light lavender background with white grouped cards, subtle borders and minimal shadows.
- Use the existing admin purple tokens for primary actions, icons, badges and focus states; muted text and pale lavender backgrounds.
- Centered store identity above the groups: actual logo or initials, store name, signed-in merchant’s email.
- Desktop content centered with a maximum width around 960px. One-column grouped rows also work on mobile.
- Each row: icon, clear label, short description, optional status badge and chevron/action.
- A full row is one link; avoid nested buttons inside links. Use visible focus states, semantic headings and comfortable touch targets.
- Wrap long store names, email addresses and domains. No horizontal scrolling at 320px.
- Scope CSS to the overview so existing admin screens keep their appearance.

## Proposed page order

### 1. Store identity and subscription

Show the store name and merchant email, followed by a status card with:

- Current configured plan name, when recognized; do not invent a plan name for historical records.
- Trial end date and remaining time while in trial, calculated using the same rules as `PlanAccess`.
- Paid access independently from provider subscription status. A cancelled subscription can still have a valid paid-through period.
- Read-only provider status from local subscription records, labelled as last recorded status where appropriate.
- Explicit “Payment pending” or “Subscription required” messaging when supported by those records.
- “Manage billing” linking to `admin.billing`; trial/expired accounts may use “Choose a plan”. This is navigation, not a direct payment action.

Do not show “site not activated” solely because a custom domain is absent. A store using its MetoHub URL is a valid storefront. Do not promise 24/7 availability or use trial/plan status as proof the website is publicly reachable.

Read local state only. Opening Settings must not call Razorpay, refresh subscriptions, create billing records or trigger checkout. Reuse `PlanAccess` for access decisions; Billing remains the reconciliation and payment screen.

### 2. Store address and domain

| Row | Destination | Notes |
| --- | --- | --- |
| Visit store | `Tenant::storefrontUrl()` | Actual canonical store link; open in a new tab with a clear label. |
| Custom domain | `admin.settings.domain` | Show configured domain or “Use your own domain”. Pro/Enterprise requirement follows existing entitlement rules. |

Domain configuration is not proof of DNS resolution or HTTPS provisioning. If verified-domain information is displayed, identify it specifically as integration domain verification, not a general domain-health check.

### 3. Store setup

| Row | Existing destination | Description |
| --- | --- | --- |
| Store details & branding | `admin.settings.storefront` | Store name, logo, colors, contact details and homepage content. |
| Storefront themes | `admin.settings.theme` | All configured themes; show the actual current theme name. |
| Payment methods | `admin.settings.payment` | Customer cash-on-delivery settings, separate from SaaS subscription billing. |
| Checkout fields | `admin.settings.checkout-fields` | Configure information collected at checkout. |
| Discounts & coupons | `admin.discounts` | Existing discount tools. |
| Collections | `admin.collections` | Organize store products. |
| Delivery partners | `admin.settings.delivery-partners.index` | Store-managed delivery partner records, not courier booking. |
| Delivery staff | `admin.delivery-staff.index` | Existing delivery staff management. |

### 4. Homepage content

Link to the existing Hero Slider, Instagram Reels, Home Products and Videos settings. Use their registered route names. Keep these separate from basic store details so the first group stays easy to scan.

### 5. Search, ads and analytics

| Row | Destination | Status language |
| --- | --- | --- |
| Search readiness | `admin.settings.seo` | Manage search titles, descriptions and indexing preferences. |
| Google Analytics 4 | `admin.settings.integrations` | “Not configured”, “Configured” or “Disabled”, derived from the saved ID and enabled flag. |
| Google Tag Manager | Same integrations page | Explain the verified custom-domain requirement in existing integration settings. |
| Google Search Console | Same integrations page | A saved verification token does not establish Google verification. |
| Meta Pixel / Ads | Same integrations page | Configures tracking; does not create advertising campaigns. |

Prefer stable fragment IDs on integration sections so each row opens the right section of the editor. Add those IDs without changing how integration forms save.

Do not label an integration “Connected” merely because an ID is saved. Provider verification, consent, domain eligibility and event delivery remain distinct. No credentials or tokens should appear in overview HTML.

### 6. Notifications and support

- Notifications → `admin.notifications.index`, including existing device notification setup.
- Contact support → the existing public Contact page or configured support mail link. Do not promise a response time that has not been supplied.
- Account email → display only. Do not invent a My Profile editor if a merchant profile editor is not implemented.

## Explicitly outside this implementation

The reference screenshot contains features that must not become nonfunctional rows or accidental new scope:

- Tax/GST settings and statutory tax invoices.
- Shiprocket integration or automated courier pickups.
- Microsoft Clarity, Meta Commerce Manager, Google Merchant Center and Tawk.to.
- Store maintenance toggle, Delete Site and Account Deletion.
- A new profile editor, policy editor, subscription checkout or payment gateway.

The owner previously confirmed the existing order/notification/packing-label/invoice scope is sufficient. This overview does not expand it. Unsupported controls are omitted rather than presented with misleading Connect buttons.

## Implementation structure

1. Add `Admin/SettingsOverviewController` with a read-only index action.
2. Load only the current tenant, current local subscription and that tenant’s integration settings. Reuse existing plan/theme configuration and access services.
3. Construct explicit grouped row data: label, description, icon, route and optional status. Use named routes with the tenant parameter supplied.
4. Create `resources/views/admin/settings/index.blade.php`, with reusable grouped-row markup if useful.
5. Add a scoped stylesheet under `public/css/` and load it through the admin layout’s existing styles section.
6. Register the overview route inside the existing authenticated admin/tenant/subscription middleware group.
7. Add the sidebar entry and integration section fragment IDs.
8. Keep every existing settings form and save action intact. No migration, package installation or production configuration change is needed.

## Verification and acceptance

- Test an authenticated merchant can see only their store identity, plan and integration state.
- Test a merchant cannot open another tenant’s overview. Customer/delivery users cannot use merchant settings.
- Test trial, expired trial, paid access, pending payment and cancelled-but-paid-through presentation against existing access rules.
- Test missing subscription, domain, logo and integration records have accurate empty states.
- Ensure reading Settings makes no provider API requests or database writes.
- Resolve every row destination and every integration fragment; do not introduce placeholder links.
- Keep private-page noindex response headers and existing authorization middleware.
- Browser-check 320px, 375px, 768px and desktop widths for overflow, wrapped identity details, sidebar navigation, focus visibility and usable row targets.
- Verify the overview links to working pages; do not submit real payments, change live store settings, delete accounts or send external messages during testing.

Completion means the overview is accessible from the sidebar, accurately represents the merchant’s recorded state, and provides a clear path to every included existing settings page.

## Implementation record

- Added `SettingsOverviewController`, the `admin.settings.index` route and Settings sidebar entry.
- Added `resources/views/admin/settings/index.blade.php` with isolated styles in `public/css/metohub-settings.css`.
- Existing integration sections now have stable fragment IDs for the four overview shortcuts.
- Access state uses PlanAccess; subscription records are read locally without provider synchronization or creation. Saved tracking IDs and verification tokens are not displayed.
- Existing editors, routes and subscription write restrictions remain in place.
- PHP tests cover tenant/role isolation, read-only access, trial expiry, pending payments, cancelled subscriptions with remaining paid access, integration states and fragment destinations.
- Chrome checks passed at 320, 375, 768 and 1440px using an isolated SQLite in-memory fixture, including long identity details, all 21 settings rows and visible keyboard focus. No live stores or billing records were modified.
- Deployment: include the new controller, view, CSS, sidebar, integration view and route changes; run normal Laravel cache refresh commands. No migration, Composer install or npm build is needed for this change.

### Sidebar simplification

Following the owner’s feedback about navigation overload, the merchant sidebar now has nine primary entries: Home, Orders, Products, Customers, Carts, Discounts, Analytics, Billing and Settings. Products expands to All products, Collections and Bundles, and opens automatically on catalog pages. Billing has a direct sidebar entry as requested, and is also reachable from Settings. Detailed store configuration lives in Settings. A Settings overview return link appears on settings, delivery-staff, billing and notification pages. Delivery staff retain their My Deliveries navigation. Existing URLs and authorization remain unchanged.
