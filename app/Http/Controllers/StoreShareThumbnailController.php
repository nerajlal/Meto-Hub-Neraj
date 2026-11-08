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
        $png = Cache::remember('store-share:'.$tenant->id.':'.$version, 86400, fn () => $thumbnails->render($tenant));
        $response = response($png, 200, ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=86400', 'X-Content-Type-Options' => 'nosniff']);
        $response->setEtag(hash('sha256', $png));
        $response->isNotModified($request);
        return $response;
    }
}
