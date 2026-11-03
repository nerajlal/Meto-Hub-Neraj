# Storefront theme collection: templates 16–30

The catalog now contains 30 themes. Existing stores retain their selected theme; new options appear in Admin → Settings → Theme and the config-driven public theme gallery/signup picker. No migration, reseeding or dependency installation is required for this change.

| ID | Name | Presentation |
| --- | --- | --- |
| template_16 | Urban Drop | Oversized uppercase cover, hard rules and portrait product tiles |
| template_17 | Petal Studio | Curved hero, rounded imagery and soft purchase surfaces |
| template_18 | Golden Hour | Centered salon composition, double rules and tall photography |
| template_19 | Circuit Lab | Search-first header, technical labels and numbered shopping tiles |
| template_20 | Scent Atelier | Reversed cover, portrait arches and separate product story |
| template_21 | Daily Harvest | Compact welcome, horizontal desktop cards and practical shopping panel |
| template_22 | Timepiece House | Framed square photography and measured typographic details |
| template_23 | Crafted Stories | Paper-inspired framing, offset imagery and story-first details |
| template_24 | Coastal Living | Panoramic welcome, open product cards and wide product gallery |
| template_25 | Pop District | Graphic borders, offset shadows and playful framed surfaces |
| template_26 | Velvet Room | Portrait salon cover, inset cards and reversed product stage |
| template_27 | Forma Objects | Ruled editorial cover, landscape cards and structured gallery |
| template_28 | Linen & Form | Offset linen-inspired cover and inset portrait tiles |
| template_29 | Prism Studio | Stacked graphic cover and geometric product panels |
| template_30 | Orchard Lane | Country-market split cover and soft landscape cards |

All use existing merchant products, variants, price/stock rules, discounts, images, collections, reviews, hero text, branding, cart and COD checkout. Sizes remain in the title row and product tags overlay the image. The mobile purchase bar remains fixed; the brand/action header stays fixed while secondary navigation scrolls. Theme names describe a style, not a restriction on which products merchants may sell.

Shared commerce components intentionally retain the same data and behavior. Visual differences are defined in `public/css/storefront-collection-themes.css`, dedicated header templates, and the collection hero/product compositions. Preview SVGs are schematic illustrations rather than screenshots of live customer stores. Missing merchant banner images fall back to the store monogram.

Validation: full application suite covers all configured themes, theme switching, preview isolation, hero customization, catalogs, products, tags, account pages and checkout. `tests/browser/run_collection_themes.py` renders isolated fixture pages and checks all fifteen new home/catalog/product pages at 375 and 1440 pixels, fixed header position, horizontal overflow and the mobile purchase bar. It creates no customer accounts, orders, emails or payments. Screenshots are saved to the temporary directory for visual inspection.

Deploy the changed Blade/config/public files together. Rebuild cached configuration if enabled and clear compiled views using the normal deployment process. Do not reset the database or change a merchant's theme automatically.
