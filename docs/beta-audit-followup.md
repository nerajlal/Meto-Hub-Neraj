# Beta audit follow-up — 18 October 2026

This compares the owner's earlier page-fetcher audit with the current repository. “Fixed in code” is not confirmation that the latest code or production environment has been deployed. No production order, payment, login or contact email was submitted during this review.

## Reported launch blockers

| Finding | Current status | Evidence / remaining action |
|---|---|---|
| Old two-URL sitemap for www.metohub.com | Fixed in code | The tracked static `public/sitemap.xml` was removed. `SearchController` generates the index, platform URLs, dynamic blog article URLs and eligible tenant sitemaps. Deploy the deletion too. |
| Store canonical points to localhost | Correct code; server configuration required | `StorefrontSeo` uses `APP_URL` for platform-hosted store canonicals. A new test renders Thread & Form with `APP_URL=https://metohub.cloud` and verifies canonical/og:url. Set the server value and rebuild its configuration cache. |
| Beta indexable | Owner/environment task | `SEARCH_INDEXABLE=false` supplies staging noindex rules. Production must use its actual domain and intended indexability. Redirect or separately protect the retired beta host at the hosting layer. Do not disable indexing on the production deployment by mistake. |
| Refund policy does not explain the trial | Fixed in this pass | Owner confirmed seven days. The policy distinguishes the free trial from a seven-day full-refund request window for the first paid subscription. It explains cancellation versus refunds, preserves verified paid access, and removes an unimplemented automatic 30-day deletion promise. Requests still need operator handling; no automated refund feature is implied. |
| Unsupported About metrics | Fixed in prior code pass | Removed customer/order counts, uptime SLA and round-the-clock support claims. The page uses configured theme count, trial duration, price and branding capabilities. |
| Thin Privacy Policy | Still needs business/policy work | Current text is not a completed review of cookies, processors, retention, customer-data roles, rights or grievance handling. Business facts and a review of the actual operations are needed; this audit does not establish legal compliance. |
| Missing identity and operational Terms | Partially addressed; not complete | Removed the false automatic-proration promise and linked the corrected refund policy. Legal entity, full business address, tax treatment, governing terms, termination and prohibited-use decisions still require the owner. |

The old 14-day refund promise and a seven-day trial were not intrinsically contradictory: one concerned a paid purchase, the other a free trial. The new seven-day paid window reflects the owner's explicit answer in this conversation.

## Other audit items

| Finding | Assessment |
|---|---|
| Duplicate public titles/descriptions and inconsistent social titles | Fixed through shared `MarketingSeo` metadata. |
| Login indexing | A noindex response header was already present. This pass also gives the active `/admin/login` page a specific title/description and matching noindex metadata. The older standalone login view has explicit noindex metadata too. |
| Store title renders literal `&amp;` | Not reproduced for a normal stored name. The title is escaped once in source; decoding it once gives “Thread & Form”. A regression test checks it is not `&amp;amp;`. HTML entities in fetched source are not proof of visible double encoding. Existing database names containing pre-encoded entities would need separate data inspection. |
| Empty category-image alt | The image and visible category name share the same link. Empty alt can correctly avoid repeating that name. Keep useful alt for informative product/banner images. [W3C guidance](https://www.w3.org/WAI/tutorials/images/decorative/). |
| Generic demo hero alt | `DemoStoreDesign` still uses a generic slider title. Improving seeded sample presentation is useful, but was not silently applied to existing live tenant data. |
| Blogs absent from sitemap | Fixed: `/sitemap-blogs.xml` uses the public feed/cache and handles additions/removals, duplicate IDs and outages. |
| Numeric blog URL | Still numeric and supported. Slugs would improve readability, but changing paths is not required to make the articles discoverable. A future slug change should preserve ID resolution and redirect old URLs; do not break published links. Leading-zero ID aliases already redirect. |
| Grocery-only blog topic | Content task: publish original posts for clothing, jewelry, fragrance and other target sellers. No invented client case studies were added. |
| Small social logo and summary card | Fixed for platform pages: local 1200×630 sharing image, large-image card and consistent social metadata. Merchant social images remain store-specific. |
| Signup says Starter | The main homepage wizard already uses configured Base/Pro/Enterprise names. This pass replaced a leftover “Starter” label in the legacy inner-page modal with the configured Base name. |
| “No card” beneath paid-plan cards | The buttons explicitly start a free trial; no card is required for that action. Paid activation happens separately in Billing. The caption does not say that paid subscriptions are free. |
| GST ambiguity | Pending owner confirmation. The code currently constructs gateway plan amounts from configured prices; that alone does not establish the business's GST obligations or invoice tax treatment. No inclusive/exclusive/GSTIN claim was invented. |
| Enterprise support too vague | Requires an actual support offering from the business, including scope. Do not invent response times or account-manager availability. |
| Contact form lacks protection/consent | Fixed in this pass. It previously did not submit at all. It now posts to `/contact` with CSRF, validation, required enquiry consent, a honeypot and a three-per-minute limit. Mail uses the configured sender and the visitor's address only as Reply-To. Failure shows an error and preserves the form instead of claiming success. |
| Contact email is on metora.in | The owner supplied `metohub@metora.in`. This is not inherently a code bug; mailbox delivery and sender authorization must be verified by the owner. |
| Full address, hours, response time and GSTIN | Still require real business details. Only the supplied Infopark, Kochi location is displayed. |
| Fake demo contacts | The seeder intentionally creates test data. Do not replace real tenant contacts or rerun a destructive seeder on production. Improve demo labels/contact presentation in a deliberate demo-data update. |

## Demo-store behavior: source evidence, not production browser proof

- `storefront/themes/product/purchase.blade.php` has actual variant buttons and a selected variant field. `FreshDemoSeeder` creates two variants per sample product. A static fetcher can display the first size while missing the selector behavior.
- The shared cart drawer loads its body through AJAX. `template_1/partials/cart_drawer_items.blade.php` has an empty state and a checkout action, with a minimum-order gate. Their absence from a summarized initial HTML response is not enough to conclude they do not exist.
- The seeder gives a bundle an image and attaches two products. The bundle view has fallback images and an included-product list. Verify actual deployed seed data and browser rendering before declaring this fixed or broken in production.
- “Signature” and “Edition Two” are actual seeded sample names. They have price/stock changes but similar copy; richer, differentiated demo content remains a useful improvement.
- Cart/bag/basket terminology intentionally varies across storefront designs, though inconsistent wording within one purchase flow should be checked.
- This pass adds a clearly labelled **Platform privacy** link to the classic and shared storefront footers. This is not a substitute for an individual merchant's own privacy notice; merchant-specific privacy settings/pages remain a separate product/policy task.
- Demo tenants are seeded with `search_indexable=false`; they are intentionally excluded from search sitemaps. The audit suggestion to index every demo was not applied blindly to fictional shops, contacts and products.

## External checks and follow-up work

1. Deploy the code, update the production environment, and inspect the actual homepage/store canonical and sitemap responses.
2. Verify SMTP with an authorized controlled contact submission. Automated tests used fake mail and simulated failures only.
3. Check hosting-level security headers, HTTPS/domain redirects, real 404 behavior, mobile performance and production cart/checkout with a controlled test. These were not established by the original audit or local layout checks.
4. Finish the legal/business information and GST/support decisions. The policy changes here are not a legal sign-off.
5. Add real merchant screenshots and approved testimonials when available. Keep fictional seller stories labelled.
6. Add verified social profile links if desired. No unprovided Instagram profile or WhatsApp availability was assumed.
7. Consider a status page, security.txt and an account-wide data export workflow as separate, accurately scoped additions.

## Validation

- Full PHP suite: 147 tests, 3592 assertions passed.
- Contact tests cover delivery to the configured address, Reply-To, consent, honeypot rejection, rate limiting, escaped message content and transport failure without false success.
- New checks cover the seven-day refund policy, login metadata/headers and correctly escaped storefront titles using the configured production host.
- Chrome checked nine public pages at mobile and desktop sizes, with consistent shared navigation and no horizontal overflow or script errors.
- No real emails, payments or customer orders were created. No local or production database reset/seeding was run.
