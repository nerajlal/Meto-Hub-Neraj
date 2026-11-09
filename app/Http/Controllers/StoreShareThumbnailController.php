<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\StoreShareThumbnail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StoreShareThumbnailController extends Controller
{
    public function __invoke(Request $request, string $slug, StoreShareThumbnail $thumbnails)
    {
        $tenant = Tenant::where('slug', $slug)->firstOrFail();
        abort_unless(app(\App\Services\PlanAccess::class)->storefrontAvailable($tenant), 404);
        abort_unless(function_exists('imagecreatetruecolor') && function_exists('imagepng'), 503, 'Image rendering unavailable.');
        $version = $thumbnails->version($tenant);
        // Database caches use a UTF-8 text column: raw PNG bytes cannot be stored safely in MySQL.
        $encoded = Cache::remember('store-share:png-base64:'.$tenant->id.':'.$version, 86400, fn () => base64_encode($thumbnails->render($tenant)));
        $png = base64_decode($encoded, true);
        abort_if($png === false, 503, 'Thumbnail cache unavailable.');
        $response = response($png, 200, ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=86400', 'X-Content-Type-Options' => 'nosniff']);
        $response->setEtag(hash('sha256', $png));
        $response->isNotModified($request);
        return $response;
    }
}
