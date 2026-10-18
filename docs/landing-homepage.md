# MetoHub published-reference homepage

The `/` marketing homepage follows the owner's published design at [brightpath-onboarding-studio.lovable.app](https://brightpath-onboarding-studio.lovable.app/). This replaces the earlier phone-preview prototype. The page preserves the reference's light surfaces, Manrope and Instrument Serif typography, seven image-led demo cards, steps, feature grid, pricing, FAQs and footer. The refined hero uses a clear brand-focused headline, a prominent configuration-driven monthly price, trial and demo actions, and an illustrative storefront inside a CSS mobile device frame, with a status bar, address bar, shop navigation and product presentation. Its layout stacks on mobile and uses two columns on desktop.

## Presentation and data

- The approved visual stylesheet is self-hosted in `public/css/metohub-reference.css`; application-specific onboarding and mobile-menu styles live in `public/css/metohub-home.css`.
- The provided reference images are self-hosted in `public/images/landing/reference/`. No Lovable runtime scripts, receipt tokens or external preview iframes are included.
- The page stays light-only; the reference's dark-mode control and persistence are omitted as requested earlier.
- Hero product details and demo artwork are illustrative. The demo cards are labeled accordingly; they are not represented as screenshots of the seeded catalog.
- Names, available demo stores and selected theme names come from the application. `config/demo_storefronts.php` selects the seven public store slugs. Missing stores are omitted, with a theme-gallery fallback when none are available. No private tenant data or owner credentials are exposed.
- Plan prices and the trial duration come from `config/saas.php`, including hero, pricing, FAQ, CTAs and signup. Each demo opens the correct local/deployed storefront through the existing URL helper.
- Demo stores appear in a continuous right-to-left carousel. Hovering or focusing a card pauses movement; swipe, horizontal scroll and arrow controls switch to manual browsing. A Pause/Play button controls automatic movement. Reduced-motion preferences disable automatic scrolling. Duplicate loop cards are excluded from keyboard and screen-reader navigation.
- Native FAQ controls and server-rendered demo links work without an additional JavaScript framework. Mobile navigation includes merchant login.

## Registration

`public/js/metohub-home.js` connects every signup action to the native registration dialog and the existing `saas.register` endpoint. This is an application integration, not the preview's simulated-success form.

The wizard collects the existing name, email, WhatsApp number, business name, country, password, confirmation and theme fields. All configured themes are available. Plan selection is preserved in the UI and submitted. The backend still provisions the existing Base trial; paid subscriptions are selected in Billing after verification. This behavior is explained in the wizard.

The dialog validates fields, maps server validation errors to the right step, preserves input, blocks duplicate submissions while pending, and handles session expiry, throttling and uncertain network outcomes. It clears password inputs after a confirmed successful response.

The completion screen uses the server's actual `email_sent` flag and message. An account created during a mail failure is not described as having received an email. Resending uses the existing verification-resend endpoint with CSRF protection and a cooldown. The screen never exposes verification tokens or fabricates an authenticated session.

Customer checkout remains cash on delivery. Razorpay subscription collection is separate. Homepage work does not add online customer payments, courier bookings or statutory tax-invoice configuration.

## Verification

Run:

```sh
php artisan test --compact
python3 tests/browser/run_landing_page.py
```

The browser runner accepts `--browser /path/to/chrome` and optional `--screenshots /output/folder`. It renders the actual Blade page against an explicitly guarded, isolated in-memory SQLite fixture. It does not reset or query the real local merchant database.

Chrome checks cover widths 320, 375, 768 and 1440; seven demo links; carousel direction, pause, navigation, seamless looping and reduced-motion behavior; all 15 theme choices; plan selection; three signup steps; required fields; duplicate-email validation; network and rate-limit errors; both mail-success and mail-failure completion screens; resend; and mobile navigation. Signup and mail responses are mocked, so the browser checks create no real accounts, orders or emails. Backend registration and verification remain covered by the existing feature tests.

The complete PHP suite passed with 126 tests after this integration. Blade and asset changes require no new Composer packages, npm build or database migration. Deploy the changed PHP/views and the new local CSS, JavaScript and image assets together, then clear the compiled views. Local verification does not establish production SMTP or production delivery.

The hero speaks directly to creators with “Out of the DMs. Into a real storefront.” and a “Start my store today” CTA. Its launch prompt is aspirational, without artificial deadlines or scarcity. A compact strip links to the available curated demos, with its count derived from `$demos`; it disappears when no demos are available. These are demo examples, not customer endorsements or adoption statistics. Add seller testimonials only with verified attribution and permission.

Creator-focused refinements:

- The hero describes price enquiries and screenshot-based order tracking, and explicitly identifies cash on delivery as the supported customer checkout method. It does not promise automated transfers or online customer payments.
- Forest-green primary actions provide stronger contrast on the light palette. The phone remains an explicitly illustrative store, not a claim of live customer activity.
- The category strip gives examples such as handmade jewelry, streetwear, decor, skincare and fragrance. These are possible catalog use cases; the seven linked demos retain their actual store identities.
- Pro is visually highlighted as “Recommended for growing brands”, with custom-domain benefits and separate domain-purchase costs. This is an editorial recommendation, not a “most popular” usage claim. All prices and trial lengths still come from configuration.
- Demo scrolling provides motion with existing pause and reduced-motion support. No simulated launch or purchase notifications, fabricated seller counts, avatars or testimonials were added. Real testimonials need attributable seller quotes and permission to publish their names, images or store screenshots.

Review section and design examples:

- `config/landing_reviews.php` supplies the cards. At the owner’s request, it includes six fictional Kerala-style seller personas. Each card is labelled “Illustrative example”, and the section explicitly says the names and quotes are fictional, not client reviews. No ratings, verification badges, purchase activity or success metrics are invented.
- `sample => true` displays a labelled design example. For an actual client testimonial, replace the quote/name/store with approved public content, set `sample => false` and `approved => true`. Unapproved non-sample entries and entries missing a quote or name are hidden. An empty list hides the section entirely.
- Keep `sample => true` on fictional content; changing that flag does not turn a fictional story into genuine feedback. All text is escaped by Blade.
- Clear cached configuration after changing review content on a deployed installation. No database migration is needed. The configuration is code-managed, not an admin review editor.
- Demo cards now include category-specific workflow captions. The feature grid adds checkmarks and compact capability cues, and redundant hero taglines have been removed. The Pro recommendation remains editorial; no popularity, cancellation-policy or shipping-cost comparison is claimed.
- Targeted validation: six homepage tests (65 assertions), plus Chrome responsive, carousel and mocked signup checks. Screenshots cover review and pricing layouts on mobile and desktop.

Shared public navigation:

- `landing.partials.header` and `landing.partials.footer` are the single source for homepage and public marketing, blog and legal-page navigation. Both `landing.new-landing1` and `layouts.landing` include these components. Merchant storefronts and admin interfaces retain their own navigation.
- Inner-page navigation links use homepage URLs with section anchors, and Get started opens the homepage signup URL. The homepage retains its native onboarding dialog. The legal links point to the existing privacy, terms and refund/cancellation routes.
- `metohub-navigation.js` handles mobile navigation on inner pages. Existing legacy content styles are scoped to `.page-content` with CSS `@scope`, so they do not change the shared header/footer. The modern reference and home styles are loaded on both layouts. This requires a browser with CSS `@scope` support.
- Route verification covers identical shared components on 21 public routes, including a mocked blog article. `python3 tests/browser/run_marketing_chrome.py` compares rendered header/footer dimensions, colors and fonts at 375 and 1440 pixels on eight pages, checks mobile menu open/Escape close, horizontal overflow, script errors and sticky positioning. Its fixtures use only in-memory SQLite and a mocked blog feed.
- Deploy both shared partials, the layout, homepage and navigation asset together, and clear compiled views. No database migration is needed.

Public-page visual theme:

- `public/css/metohub-pages.css` applies the landing palette and type hierarchy within `.page-content`: warm ivory backgrounds, soft cream cards, forest-green actions, bronze accents and Manrope typography, with Instrument Serif for selected highlights.
- Legacy violet/pink accents and cool gray surfaces in active public-page styles have been aligned, including About, Contact, legal pages, Blogs, pricing, guides and analytics illustrations. Existing page text and behavior are preserved; this styling pass is not a content or capability review.
- The page stylesheet is loaded by `layouts.landing` only and does not alter merchant storefront or admin themes. It also refines legal content spacing, card borders, shadows and contact controls for the lighter design.
- Browser checks additionally verify the shared forest token, warm background and Manrope content/heading fonts on seven inner pages at mobile and desktop widths. Targeted homepage/blog tests: 14 passed, 172 assertions.

Review scrolling and section headings:

- The six illustrative seller-story cards use the same carousel controller as the demo stores, with independent pause/play and arrow controls. They scroll right to left, pause on hover/focus or manual browsing, respect reduced motion, and pause while signup is open. Duplicated loop cards are excluded from screen readers and keyboard navigation. Fictional-example labels remain on every card.
- The “Everything you need” and “Good to know” left columns stick 96px below the viewport top at desktop widths (1024px and above). Their grid container bounds the sticky position, so each heading leaves with its own section. On narrower screens both headings remain in the normal document flow.
- Browser checks verify six original review cards, movement direction, pause, arrow navigation, reduced motion and both sticky headings at their pinned position and section boundary, alongside the existing signup/demo checks. Homepage feature tests: seven passed, 129 assertions.
