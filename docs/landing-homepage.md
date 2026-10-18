# MetoHub homepage

The `/` marketing homepage now targets independent sellers and Instagram businesses. The hero explains the store link, advertises Base as a monthly price, and offers the configured trial. It includes a demo gallery, setup steps, supported features, three subscription plans, FAQs and a final registration action.

Prices and trial length come from `config/saas.php`; theme count comes from `config/storefront_themes.php`. No fabricated testimonials, merchant counts, revenue, native-app generation, or guaranteed-performance claims are shown. Customer COD checkout is distinguished from Razorpay SaaS subscription billing. Custom-domain registration is separate from Pro domain connection.

`config/demo_storefronts.php` is the curated gallery list. The controller only links stores present in the database and displays their actual names, theme names, homepage text, logos and product images. It shows one product per available collection, up to three. Missing stores are omitted; if none exist, visitors can still browse themes and register. No owner credentials are exposed. Production must contain the corresponding demo stores before their links appear; the local-only fresh seeder must not be run against a customer database.

All CTAs use the existing merchant onboarding wizard. Button handlers prevent anchor navigation so selected plans stay selected. Registration and email-verification backend behavior are unchanged. The actual registration request is not submitted during browser QA.

The stylesheet is isolated to this homepage. The shared layout supports a page-specific footer; other marketing pages retain their footer. Navigation links to the live demos and setup instructions on the homepage.

Validation: homepage feature tests cover configured pricing, missing demo stores, curated links, and registration triggers. Browser checks cover 375px/768px/1440px layouts, all three plan selections, first-step validation, progression through three onboarding steps, and mobile navigation. The complete suite passed with 124 tests.

## Light premium presentation

The homepage uses an ivory and champagne palette, charcoal typography, fine warm borders and a photographic storefront illustration built from the existing local fragrance asset. The header, hero, pricing, invitation and closing sections all use light surfaces. Dark tones are reserved for text, buttons and imagery.

The demo gallery uses WebP screenshots of the seven seeded public storefronts from `public/images/landing/demos/`. These are static previews; store links still open the live storefronts. Re-capture the screenshots when demo theme/content changes. The fallback card still works if a preview image is missing. The hero is explicitly labeled as an illustrative store preview. No registration, order, mail or payment was submitted during browser QA.
