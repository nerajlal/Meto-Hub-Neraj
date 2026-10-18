# MetoHub landing page — master design prompt and implementation brief

> Use this entire document as the brief for a designer, frontend developer, or coding assistant. Design and refine the existing MetoHub marketing homepage using the requirements below. This is a target design specification, not a claim that every detail is already implemented or production-verified.

Prepared for the Task19-Grocery project, 17 October 2026.

## 1. The assignment

Create a beautiful, premium **light-theme landing page** for MetoHub, a SaaS platform that helps small businesses and Instagram sellers create and manage their own online stores.

The page must help a visitor understand the product, explore a relevant store example, understand the cost, and confidently start onboarding. It should feel like a carefully designed product from an established brand, while remaining welcoming to someone launching their first website.

Work on the existing application and registration flow. The requested experience is the public homepage at `/`, currently viewed locally at `http://127.0.0.1:8000/`.

### Requirements explicitly requested by the owner

These take priority over optional visual experiments:

1. **Use a light theme with a premium feel.** The owner explicitly rejected a dark landing page. This applies to the header, hero, pricing, final CTA, and footer—not only the first screen.
2. Make the page attractive enough that a small seller can imagine their own brand on MetoHub.
3. Present **“your own website from ₹99/month”** clearly and honestly. The offer must be easy to find.
4. Include **all seven existing demo stores** so visitors can explore clothing, grocery, gadgets, jewelry, watches, and perfume.
5. The demos must show different templates, colors, and identities. They must not look like the same store with a changed name.
6. Make getting started easy: strong CTAs, clear steps, working registration, and understandable pricing.
7. The whole page must feel polished on mobile as well as desktop.
8. Preserve the actual product capabilities and admin-driven behavior. Do not advertise unfinished features as available.

Do not treat “premium” as permission to switch to dark backgrounds, conceal the price, or make the page difficult to read.

## 2. Product context and audience

### What MetoHub does

MetoHub gives a merchant a branded storefront and a dashboard for products, collections, variants, inventory, orders, and store settings. Merchants can select a theme and personalize their store rather than building a website from scratch.

A common customer starts with an Instagram profile or WhatsApp business and handles orders manually. They want a store link they can share in their bio and a more organized way to receive and manage orders.

### Primary audiences

- Independent clothing and fashion sellers.
- Small grocery and fresh-food businesses.
- Gadget and accessory shops.
- Jewelry sellers and boutique brands.
- Watch sellers.
- Perfume and fragrance sellers.
- Other small catalog-based businesses with similar needs.

### Questions the page should answer quickly

- What is this product?
- Can I use it for my type of business?
- Will my store look professional and different from others?
- Do I need coding or a developer?
- What does ₹99 cover, and is that a monthly price?
- Can I try it before subscribing?
- What happens after I click Get Started?

Use plain language. Lead with the merchant’s outcome rather than terms such as multi-tenancy, architecture, or backend infrastructure.

## 3. Positioning, voice, and message

### Main promise

**Give your small business a beautiful online home, with the tools to run it.**

Supporting ideas:

- Your products deserve more than a scattered message thread.
- Give customers a store link they can browse and share.
- Make the storefront feel like your own brand.
- Manage the catalog and orders from one dashboard.
- Start affordably, without coding.

### Tone

Confident, warm, clear, and considered. Speak to an independent business owner with respect. Do not sound like an enterprise sales deck or a generic website-builder advertisement.

Avoid excessive superlatives, fake urgency, guaranteed results, invented merchant counts, and unverified claims such as “the fastest,” “instant success,” or “trusted by thousands.”

### Suggested primary headline

> A small brand.  
> A big vision.  
> A beautiful store.

The final line can use a restrained italic serif treatment. Alternative copy is acceptable if it communicates the product equally clearly.

### Suggested supporting copy

> You put everything into your products. Give them a home that feels just as considered. Your own online store, ready for the link in your bio.

### Price and trial wording

> Your own website from ₹99/month.  
> Start with a 7-day free trial.

Keep “/month” immediately adjacent to the price. Do not present ₹99 as a lifetime, one-time, annual, or domain-registration price.

### CTA language

Use a consistent family of actions:

- Primary: **Create your store** or **Start my free trial**.
- Secondary: **Explore demo stores**.
- Demo card: **Explore the store**.
- Pricing: **Start 7-day free trial**.
- Returning merchants: **Log in**.

Favor clear action labels over vague labels such as “Discover more” for registration.

## 4. Visual direction: light, premium, and usable

### Overall impression

Think of a refined editorial website combined with a useful SaaS product. Use warm light surfaces, strong typography, accurate store previews, carefully selected photography, and generous but purposeful spacing.

The page should feel crafted and cohesive from the header to the footer. Avoid a sequence of unrelated component-library cards.

### Palette

Use these as starting values rather than rigid requirements:

| Purpose | Suggested color |
|---|---|
| Main canvas | `#FCFBF8` — near-white ivory |
| Hero surface | `#F9F7F1` — warm off-white |
| Alternating section | `#F2EFE8` — pale stone |
| Pricing section | `#EFEBE2` — light champagne stone |
| Main text | `#292B2B` — charcoal |
| Body text | `#66645F` — warm gray |
| Accent text | `#8C6840` — bronze |
| Pale accent fill | `#F2E9DC` — champagne |
| Fine borders | `#E4DFD5` |
| Primary button | `#2C302E` with warm white text |

Dark text, buttons, and details within photographs are appropriate. Large dark page sections are not part of this direction.

Use color to create hierarchy, not decoration alone. Keep bronze labels readable on pale backgrounds; a faint gold-on-cream combination is not sufficient for important information.

### Typography

- Use a clean, well-rendered sans-serif for navigation, paragraphs, controls, and most headings.
- Add an elegant serif selectively to an emphasized headline line or editorial statement.
- Keep the same hierarchy across sections.
- Favor medium-weight headings with carefully controlled letter spacing over heavy rounded display text everywhere.
- Keep body copy comfortably readable, generally around 15–17px on desktop and at least 14–16px on mobile where practical.
- Important labels and pricing conditions must remain readable; do not shrink them into decorative microtext.
- Use responsive heading sizes with deliberate line breaks. Prevent a single word from overflowing on a narrow phone.
- Limit font families and weights. Provide reliable fallback fonts.

### Spacing and structure

- Use a centered maximum content width around 1200–1280px.
- Desktop gutters should feel generous, generally 40–80px depending on the viewport.
- Mobile gutters should usually remain around 20–24px.
- Use a consistent spacing scale for section headings, content blocks, and controls.
- Separate major sections with quiet changes in surface color or fine borders.
- Preserve a clear rhythm without excessive empty space or compressed content.

### Borders, shadows, and shape

- Use fine warm borders and restrained shadows.
- Give buttons and cards modest corner radii; avoid making every element a pill.
- A slight perspective or rotation on a hero preview is acceptable if it remains legible and does not cause overflow.
- Do not make essential content depend on hover or animation.
- Avoid excessive glass effects, glowing borders, animated blobs, or decorative badges with no meaning.

## 5. Page structure and detailed requirements

### A. Header and navigation

Use a light, sticky header that stays visually quiet while scrolling.

Include:

- MetoHub logo linking to `/`.
- Explore stores → `#demos`.
- How it works → `#how-it-works`.
- Pricing → `#pricing`.
- Themes → the existing theme page.
- Contact → the existing contact page.
- Log in → the existing merchant login page.
- A prominent Get Started button.

On mobile:

- Keep the logo, Get Started action, and menu control visible without crowding.
- Put secondary links in an accessible menu.
- Close the menu after navigation.
- Ensure that Log in remains reachable even if it moves into the mobile menu.
- Use readable touch targets and visible expanded/collapsed states.
- Do not let the header cover anchor destinations or registration controls.

### B. Hero: product, identity, offer, action

The hero must communicate what MetoHub offers without requiring the visitor to scroll.

Desktop composition:

- Copy and action area on the left.
- A premium photographic storefront presentation on the right.
- Balanced visual weight and comfortable negative space.

Include:

1. A short audience or positioning label.
2. One clear H1.
3. A short explanation of the product.
4. Primary signup CTA.
5. Secondary demo CTA.
6. The ₹99/month starting price.
7. The configured free-trial duration.
8. A short reassurance such as “No coding needed.”

The price is a real selling point. Do not reduce it to an unreadable footnote simply to make the page look expensive.

The hero preview should show how a merchant’s products can be presented. Use an existing suitable local photograph, an actual store screenshot, or a clearly labeled design illustration. Avoid oversized generic dashboard revenue numbers, stock graphs, fake order alerts, or social-proof claims.

The current fragrance photograph at `public/images/landing/hero.png` is available. If used inside a sample storefront frame, label the composition as an illustrative store preview. Do not imply that a decorative preview is a separate working website or native app.

### C. Category strip

A compact strip can reinforce the breadth of businesses supported:

Clothing · Grocery · Gadgets · Jewelry · Watches · Perfume

Keep it elegant and readable. It should support the demo gallery rather than occupy a large amount of space. Animation is optional; a static strip is acceptable and often preferable.

### D. Demo store gallery — a central conversion section

This is a core requirement, not an optional decoration. Visitors should see a relevant example before committing to registration.

Suggested heading:

> A world of possibilities.  
> Find your kind of store.

Suggested explanation:

> Open a demo, browse the products, and imagine your brand here. Every store has its own theme, colors, and personality.

#### Include these seven existing stores

| Category | Store | Theme | Public storefront path |
|---|---|---|---|
| Men’s clothing | Thread & Form | Atelier Edit — `template_7` | `/store/mens-clothing` |
| Grocery | Fresh Basket | Fresh Market — `template_2` | `/store/grocery` |
| Gadgets | Circuit Cart | Vista Shop — `template_8` | `/store/gadgets` |
| Jewelry | Aurelia Jewels | Maison Luxe — `template_13` | `/store/jewelry` |
| Watches | Hour House | Monochrome Edit — `template_11` | `/store/watches` |
| Women’s clothing | Loom & Grace | Bloom Avenue — `template_12` | `/store/womens-clothing` |
| Perfume | Maison Mist | Noir Collection — `template_9` | `/store/perfume` |

Local preview links use the same paths on `http://127.0.0.1:8000`. Generate deployed links from the application rather than hardcoding the local host.

#### Gallery presentation

- Prefer actual storefront screenshots that show different layouts, typography, and colors.
- Use two generously sized columns on a wide screen if that makes previews easier to understand.
- Adapt to one column on narrow phones; do not compress seven stores into tiny unreadable tiles.
- Include category, store name, theme name, and a clear Explore action.
- Make the whole card clickable without nesting conflicting links or buttons.
- Add a subtle hover treatment on desktop and a clear focus state for keyboard users.
- Tell visitors when a store opens in another tab.
- Preserve each store’s individual identity instead of forcing all previews into the same generic mini store layout.

Static WebP previews currently live in `public/images/landing/demos/`. They must be refreshed when the corresponding demo designs change. Do not call a static screenshot a live preview.

The gallery should use real available demo tenants. If one is missing, omit its broken card. If none exist, show a useful theme-gallery fallback and a working registration action.

Add a short, unobtrusive note explaining that these are sample stores with demo products. Do not expose store-owner email addresses, passwords, private dashboards, customer records, or internal billing statuses.

The seeded demo catalog has three collections and nine products per store. These counts are for demonstrations, not merchant plan limits.

### E. How it works

Explain the setup in three clear steps.

**1. Create your store**  
Enter your name, email, business details, and credentials. Verify your email to access the merchant dashboard.

**2. Make it yours**  
Choose a theme, add your logo and colors, and upload your products with prices, variants, and stock.

**3. Share your store link**  
Place the link in your Instagram bio or share it on WhatsApp. Receive and manage customer orders in the dashboard.

Use small numbered markers, concise headings, and readable explanations. Do not promise a guaranteed completion time. Avoid saying every store is immediately live before required verification and configuration.

### F. Benefits and product features

Present capabilities in terms of merchant value. Group related tools rather than producing an overwhelming feature checklist.

Recommended groups:

| Benefit | Supporting capabilities |
|---|---|
| Make it feel like your brand | Themes, logo, colors, editable homepage content |
| Keep the catalog organized | Products, collections, variants, sizes, stock |
| Create appealing offers | Bundles and discount codes |
| Follow every order | Order details, fulfillment status, payment recording |
| Prepare an order for dispatch | Order invoices and store-generated packing labels |
| Stay informed | Owner order emails, notification bell, optional supported-device alerts |
| Help customers find you | Editable SEO metadata and supported integrations |
| Grow into your own domain | Custom-domain connection on an eligible plan |

Use accurate language:

- A packing label is not a courier booking or paid shipping label.
- Recording an order as paid does not charge or transfer money.
- An order invoice is not automatically a configured statutory tax invoice.
- Device alerts depend on browser support, user permission, and working server setup.
- Analytics and domain entitlements depend on the subscription plan.

Show a small number of strong feature groups with clear descriptions. A visual of the actual product may support the section, but it must not reveal private customer data or imply unbuilt functionality.

### G. Pricing

Make the affordable starting point clear while keeping the visual treatment refined.

Current configuration:

| Plan | Monthly price | Main positioning |
|---|---:|---|
| Base | ₹99/month | Start your own online store |
| Pro | ₹249/month | Add your own domain, advanced analytics, and remove footer branding |
| Enterprise | ₹749/month | Pro features plus the configured additional support offering |

Current trial: **7 days**.

Use `config/saas.php` as the source of truth. Prices, trial duration, plan names, and entitlements must not drift between the hero, cards, FAQs, onboarding, and billing.

Base can be highlighted as the natural place to begin. Use a pale champagne surface, fine accent border, or small label. All pricing surfaces must remain light.

Each card should include:

- Plan name.
- Clear monthly price.
- Short explanation of whom it suits.
- Meaningful differences from the previous plan.
- A working free-trial CTA.

Key pricing conditions:

- Explain that a paid plan is required after the trial.
- Explain that custom-domain registration is separate from connecting a domain.
- Prefer “MetoHub store link” unless actual subdomain provisioning has been verified. The current demo URLs are slug-based store paths.
- Do not advertise lifetime access or a one-time ₹99 purchase.
- Do not add a percentage commission, zero transaction-fee promise, or tax-inclusive claim without confirming the commercial setup.
- Only promise service levels or human support arrangements that the business can actually provide.

If annual billing is introduced into the design, derive it from the current billing configuration and verify that the selected billing cycle reaches checkout correctly. Do not add a decorative monthly/yearly toggle that does not work.

### H. Frequently asked questions

Use accessible expandable rows with clear questions and useful answers.

Cover at least:

1. Is it really ₹99 per month?
2. Do I need coding skills?
3. Can I sell products other than clothing?
4. Can I personalize the theme and store content?
5. How do customers pay for orders?
6. Can I use my own domain?
7. What happens after the free trial?
8. What do I need to create a store?

Answers must match the actual product and billing flow. Keep them short enough to scan but complete enough to remove confusion.

### I. Final call to action

Use a spacious **light** closing section, not a dark full-width slab.

Suggested copy:

> You built the brand.  
> Now give it a home.

Supporting line:

> Your own online store from ₹99/month. Start with a 7-day free trial.

Finish with one clear Create your store action. A pale ivory or champagne background, strong typography, and a charcoal button are sufficient.

### J. Footer

Include:

- MetoHub brand and a short statement of purpose.
- Merchant login.
- Contact.
- Privacy policy.
- Terms.
- Refund/cancellation policy.
- Current year.

Use working destinations. Do not include empty social links, an unconnected newsletter form, fabricated office details, or decorative app-store badges for unavailable apps.

## 6. Onboarding and conversion behavior

The marketing page must connect to the existing merchant registration flow rather than creating a separate, incompatible form.

Required behavior:

1. A Get Started or trial CTA opens the existing onboarding wizard.
2. A CTA associated with Base, Pro, or Enterprise preserves the selected plan in the wizard.
3. Prevent accidental link navigation from resetting the selected plan.
4. The first step collects the existing required identity fields.
5. Subsequent steps collect the existing business details, credentials, and theme selection.
6. Show clear validation without losing entered information.
7. Explain email verification and the next step after a successful registration.
8. Keep the merchant login route easy to find.

Do not change account provisioning, verification, subscription activation, or payment behavior purely to match a new visual design. Trial access and the final paid subscription must remain governed by the backend.

Avoid unnecessary new questions, upsells, popups, or detours before the merchant can start. Provide visible close and back controls, readable progress, proper focus handling, and usable form fields on mobile.

Never prefill or publish demo-owner passwords in the public marketing page.

## 7. Payment and integration truth boundaries

This section is essential for writing accurate marketing copy.

### Customer orders versus SaaS subscriptions

- Customer storefront checkout currently supports **cash on delivery**.
- Razorpay is used for the merchant’s **MetoHub subscription**.
- Do not imply that the merchant automatically accepts online customer payments through that subscription integration.
- Automatic split payments or settlement to a merchant’s UPI account are not available merely because they were previously discussed.

### Search and integrations

Search readiness includes editable metadata and existing search-related tools. It does not guarantee rankings, search-engine inclusion, or AI-search recommendations.

Analytics and marketing references must match the implemented integrations, consent requirements, verification rules, and eligible domain context. Do not turn support for a Meta Pixel into a claim that MetoHub automatically creates or manages advertising campaigns.

Do not promise native mobile app generation, a global edge network, specific uptime, guaranteed conversion increases, automatic courier booking, or platform-wide payment features without implementation and verification.

## 8. Responsive behavior

Check the page at approximately 375px, 390px, 768px, 1024px, and 1440px, as well as a narrow 320px sanity check.

### Mobile

- Keep the core offer, a clear CTA, and an explanation of the product easy to reach.
- Stack hero content and preview naturally.
- Keep the price and trial information readable.
- Avoid oversized images that delay the useful content by several screens.
- Use one-column demo cards when two columns would make previews unreadable.
- Stack pricing cards with clear separation and no overlapping featured-plan label.
- Let FAQ questions wrap without covering the expand control.
- Keep buttons reachable without horizontal scrolling.
- Confirm the mobile menu and onboarding form are usable with the keyboard open.

### Desktop

- Use a balanced two-column hero.
- Show large enough demo previews to demonstrate real theme differences.
- Keep line lengths controlled and avoid excessive stretching on wide screens.
- Align pricing cards and feature rows carefully.

### All sizes

No horizontal overflow, clipped shadows, truncated prices, accidental blank bands, overlapping labels, or content hidden behind the sticky header.

## 9. Accessibility and usability

- Use semantic headings and one main H1.
- Use real buttons for actions and real links for navigation.
- Keep keyboard focus visible.
- Ensure the menu and FAQ controls expose their expanded state appropriately.
- Give the registration dialog an accessible name and appropriate focus behavior.
- Provide meaningful alternative text for informative images and empty alternative text for purely decorative images.
- Keep body copy and secondary information readable on the chosen light surfaces.
- Do not rely on bronze color alone to indicate a selected plan or interactive element.
- Respect reduced-motion preferences.
- Do not make screenshots the only way a visitor can understand the product; pair them with text and links.

Premium presentation must improve usability, not reduce it through tiny labels, faint text, or hidden controls.

## 10. Imagery, screenshots, and performance

### Asset direction

Use realistic product imagery and actual screenshots where useful. Prefer a small number of well-chosen assets to a collection of unrelated stock illustrations.

Do not use toy-like repeated product icons as the primary proof of the platform’s design quality. Existing demo products may remain illustrative; the landing page should clearly distinguish sample catalog content from the actual storefront design.

### Loading behavior

- Keep the hero image available early and provide its dimensions.
- Lazy-load below-the-fold demo screenshots.
- Use appropriately sized modern image files where supported.
- Reserve image space to avoid layout shifts.
- Keep typography and buttons usable if an image is delayed or unavailable.
- Avoid loading an embedded live storefront for every demo card on initial page load.
- Avoid unnecessary animation libraries or heavyweight effects for simple transitions.

### Preview accuracy

Captured demo screenshots are static marketing assets. Refresh them after significant changes to the demos. Do not quietly replace them with screenshots of unavailable functionality.

## 11. Technical context and scope

This is the existing Laravel project:

`/Applications/XAMPP/xamppfiles/htdocs/Github/MetoHub/Task19-Grocery`

Relevant files:

| File | Role |
|---|---|
| `routes/web.php` | Homepage routing and existing tenant/custom-domain behavior |
| `app/Http/Controllers/LandingController.php` | Marketing homepage data and available demo tenants |
| `resources/views/landing/new-landing1.blade.php` | Current homepage content |
| `resources/views/layouts/landing.blade.php` | Shared layout, header, registration flow, and footer support |
| `public/css/metohub-home.css` | Homepage presentation |
| `config/saas.php` | Plan names, prices, trial duration, and entitlements |
| `config/storefront_themes.php` | Available themes and names |
| `config/demo_storefronts.php` | Curated demo categories and slugs |
| `public/images/landing/demos/` | Captured public demo previews |
| `public/images/landing/hero.png` | Existing fragrance photograph |
| `tests/Feature/LandingHomepageTest.php` | Homepage behavior checks |
| `docs/landing-homepage.md` | Current implementation notes |

Implementation rules:

- Reuse the existing application, routes, and onboarding behavior.
- Keep styles scoped so unrelated marketing pages and merchant storefronts are not accidentally redesigned.
- Preserve custom-domain storefront routing at `/`.
- Build public demo URLs through the existing tenant storefront URL helpers.
- Show only curated public demo stores, not every tenant in the database.
- Do not reset databases, alter accounts, or replace store catalogs as part of a landing-page design change.
- Keep real credentials and private customer information out of screenshots and page source.
- Keep the UI content editable or configuration-driven where the product already supports it.
- Changing the public homepage must not modify a merchant’s selected storefront theme.

Do not run the local fresh-database seeder against a production customer database simply to populate the demo gallery.

## 12. What to avoid

Reject a design if it depends on any of the following:

- Dark hero, dark pricing, or a dark closing section as the page’s main visual direction.
- A generic template with only the logo and colors changed.
- Seven repeated demo cards that hide the actual differences between themes.
- A beautiful image but no understandable product explanation.
- A premium aesthetic that makes the ₹99/month offer hard to find.
- Large amounts of empty space between short blocks of useful information.
- Tiny uppercase labels replacing readable explanations.
- Fake revenue counters, customer counts, reviews, or sales notifications.
- Unsupported payment, shipping, app, SEO, or performance claims.
- Dead CTA links, decorative video buttons, or empty social links.
- A redesigned registration form that is disconnected from the backend.
- A desktop-only layout that becomes cramped on phones.

## 13. Delivery and verification checklist

Before calling the work complete, confirm:

### Design

- [ ] The page is clearly light and premium from header to footer.
- [ ] Typography, palette, spacing, and imagery feel coherent.
- [ ] The product and primary action are understandable on the first screen.
- [ ] ₹99/month and the configured trial are visible and accurate.
- [ ] Text contrast and important labels remain readable.

### Demos

- [ ] All seven available curated stores appear.
- [ ] Their identities and templates are visibly different.
- [ ] Every card opens the correct public storefront.
- [ ] Screenshots load and match the intended store.
- [ ] Missing stores and missing images have useful fallbacks.
- [ ] No private account or customer data is exposed.

### Onboarding

- [ ] Header, hero, pricing, gallery, and final CTA actions work.
- [ ] Each pricing action preserves the chosen plan in the wizard.
- [ ] Validation and back/close controls work.
- [ ] Email-verification messaging matches the actual flow.
- [ ] Merchant login is reachable on mobile.

### Responsive and technical

- [ ] Mobile, tablet, and desktop layouts have been inspected visually.
- [ ] No horizontal overflow, clipped prices, overlapping labels, or blank sections remain.
- [ ] Navigation and FAQ controls work with keyboard interaction.
- [ ] Reduced-motion behavior is considered.
- [ ] Homepage and relevant regression checks pass.
- [ ] The Blade template compiles and assets resolve.
- [ ] No real registration, email, purchase, or payment is submitted merely for a visual check.

Provide a concise delivery note explaining what changed, which flows were checked, and whether the work is local or deployed. Do not label the entire SaaS product production-ready based only on a landing-page review.

## 14. Short handoff prompt

> Redesign the existing MetoHub landing page as a polished, light premium website for small businesses and Instagram sellers. Use ivory and champagne surfaces, charcoal text, restrained bronze accents, editorial typography, fine borders, realistic imagery, and generous but practical spacing. Keep every major section light. Make “your own online store from ₹99/month” and the 7-day trial clear, using the live plan configuration as the source of truth. Showcase all seven curated demo stores—men’s clothing, grocery, gadgets, jewelry, watches, women’s clothing, and perfume—with accurate previews, distinct identities, theme names, and working storefront links. Include a clear hero, demo gallery, three setup steps, truthful feature groups, Base/Pro/Enterprise pricing, FAQs, a final signup action, and a useful footer. Reuse the existing merchant registration and email-verification flow, preserve plan selection, and make the experience excellent on mobile. Clearly distinguish customer cash-on-delivery orders from MetoHub subscription payments. Do not invent functionality, testimonials, performance claims, or business results. Preserve the existing Laravel routing, tenant isolation, and merchant data. Follow this complete brief for the detailed requirements and acceptance criteria.
