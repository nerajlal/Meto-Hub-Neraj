# Store homepage sharing previews

All store homepages, across all themes, now advertise a compact 200 × 200 PNG thumbnail through Open Graph metadata, with actual matching width/height and `twitter:card=summary`. The title and description continue to come from the existing SEO/store settings. Product and collection links retain their entity images and existing larger-image metadata.

The public `/social/stores/{slug}/thumbnail.png` endpoint fits the store's SEO image (or logo) inside a white square without stretching/cropping it. If no supported local image is available, it generates a brand-colour store initial. It never downloads arbitrary remote image URLs. Files must resolve inside public image/upload directories and are subject to file-size and pixel-count limits.

PNG bytes are base64-encoded before caching so MySQL UTF-8 text cache columns can safely store them. Thumbnails are cached for a day, return an ETag, and their metadata URLs change when the store/image version changes. Missing and unavailable stores return 404. No schema migration or new composer dependency is required.

## Deployment

Deploy the app and route/view changes using the normal release process. Both Dockerfiles now install libwebp and compile GD with WebP support: rebuild and redeploy the application image so uploaded WebP logos can be decoded. A code-only pull does not add this PHP capability to an existing container. PNG/JPEG rendering uses existing GD support. Without a supported decoder, that source image falls back to the store initial.

## WhatsApp limitation

The small source image and summary metadata encourage a compact thumbnail card, but WhatsApp controls layout and caching. There is no supported metadata flag that guarantees the exact left-thumbnail presentation on every WhatsApp client. Already-sent messages may retain their existing previews; test a newly composed message after deployment. Do not interpret an old message as a live preview test.

## Verification

`php artisan test --filter=StoreShareThumbnailTest`

Tests cover all theme homepage metadata, preserved product image metadata, a real 200 × 200 PNG containing a resized logo, ETag responses, version changes, unavailable stores, and no external network fetches.

## Missing preview image / HTTP 500 fix

The public Crunchiko page advertised the thumbnail correctly, but the thumbnail endpoint returned HTTP 500 on 9 November 2026. The original implementation put raw PNG bytes in the default cache; Laravel's MySQL database cache serializes strings into a UTF-8 text column without binary encoding. This can cause an invalid-character SQL error. The endpoint now caches base64 text and decodes it before returning PNG bytes. A new cache namespace and image version bypass old entries and update `og:image` URLs.

Deploy this patch and check the exact `og:image` URL from the live page: it should return HTTP 200 and `Content-Type: image/png`. No database migration or cache-wide flush is needed. If the endpoint still returns 500, inspect the application logs for the actual exception rather than waiting for WhatsApp. Test a newly composed link after the endpoint works; the final small-card arrangement remains controlled by WhatsApp.
