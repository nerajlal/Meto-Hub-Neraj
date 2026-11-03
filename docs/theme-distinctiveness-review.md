# Theme distinctiveness review

## Scope

The prior theme count overstated design variety. Different palettes, borders and fonts do not establish different shopping layouts. This review compares template structure and CSS, not merely configuration names. Only the five themes identified below are rebuilt in this pass. The remaining findings are candidates for further visual review, not a claim that all 30 themes are now distinct.

## Rebuilt themes

| Theme | Homepage | Product cards / catalog | Product detail |
| --- | --- | --- | --- |
| Urban Drop (16) | Oversized title, full-width runway imagery, separate caption | Large portrait grid, flat captions, horizontal desktop filters | Full-width product identity above a large gallery and narrow purchase/story column |
| Circuit Lab (19) | Three-zone console with copy, image and department directory | Factual option-count/SKU cards, sidebar filters | Gallery, actual product-information table and purchase column; description below |
| Daily Harvest (21) | Department sidebar with compact shopping welcome | Horizontal quick-order cards; full-width mobile rows; sidebar catalog | Compact gallery and purchase area with separate delivery/details row |
| Timepiece House (22) | Symmetrical masthead, centered salon stage and collection links | Brand labels, square display images, discovery link and centered captions | Centered title, large display stage, framed selection and information panel |
| Linen & Form (28) | Asymmetric lookbook, captioned imagery, optional merchant story | Staggered two-column portraits with real description excerpts | Narrative-first story column beside gallery, followed by selection |

These have dedicated home, card and product templates under `resources/views/storefront/themes/rebuilt`, dedicated headers and the final override stylesheet `public/css/storefront-rebuilt-themes.css`. Commerce actions remain shared intentionally. No specifications, product claims or delivery guarantees are fabricated. Circuit/Timepiece details only display existing vendor, collection, variant labels, option counts and SKU where present.

All retain merchant color fields, hero text, real catalog data, variant pricing/stock, cart, theme previews and checkout. No selected theme is silently changed, no IDs are removed, and no separate color palette is created. Mobile purchase bars and fixed brand/action headers remain supported.

## Remaining catalog review

| Group | Structural observation | Follow-up |
| --- | --- | --- |
| Classic / Market / Quick / Pantry / Corner / Studio (1–6) | Existing dedicated home/card/product branches, including sidebar and compact-shopping differences | Retain; visual review still needed for overall quality |
| Atelier / Vista / Noir / Terra / Mono / Bloom (7–12) | Shared two-part hero and common card-body skeleton, with varying product compositions | Review pairwise with identical branding; background or typography alone should not qualify |
| Maison / Journal / Riviera (13–15) | Shared premium hero/card family; Journal horizontal card and product-story columns differ | Inspect Maison/Riviera alongside other gallery styles |
| Petal / Golden / Scent / Crafted / Coastal / District (17–18,20,23–25) | Generic collection hero/card/product skeleton, mostly CSS-driven variation | Candidate redesigns; Petal vs Bloom and Golden vs Maison deserve attention |
| Velvet / Forma / Prism / Orchard (26–27,29–30) | Generic collection family; some reversed/stacked compositions | Candidate review: Velvet vs Petal, Forma vs Mono, Prism vs District, Orchard vs Fresh Market |

These are code-review findings, not evidence from merchant research. Do not delete or merge themes without deciding how existing stores will migrate. Prioritize unique structures over increasing the count.

## Verification and limits

Application tests exercise the five distinct composition markers with identical merchant colors and actual product data; existing theme tests cover rendering, previews, activation and commerce pages for all configured themes. Browser fixtures use the same store/branding across the five rebuilt themes and check home/catalog/product at 375 and 1440 pixels, overflow, fixed header and bottom mobile purchase bar. Harvest additionally checks full-width mobile cards. These are layout/runtime checks, not proof of conversion performance or exhaustive real-device coverage.

## Second rebuild: Petal, District, Velvet and Prism

Templates 17, 25, 26 and 29 now have dedicated home/card/product partials and headers, with `storefront-rebuilt-boutiques.css` providing their structural layouts. Pop District uses an alternating two-column-span accessories grid, Velvet Room uses a photographic cover with an opaque readable copy panel and tall portrait cards, Prism Studio uses a modular collection board and contiguous metadata-led tiles, and Petal Studio uses compact square cards and centered product identity. Catalog filter placement and product information/purchase compositions differ too.

The candidate concerns above for these four are addressed by this pass; other candidates remain for future review. No synthetic cosmetic benefits, routines, fashion specifications or merchant claims are added. Collections and copy remain merchant-provided. Existing IDs, saved colors and commerce behavior remain intact.
