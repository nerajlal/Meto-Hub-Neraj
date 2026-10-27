<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\StoreDomains;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveStoreSubdomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $domains = app(StoreDomains::class);
        $host = strtolower($request->getHost());
        // www is a platform alias. Other reserved labels must never resolve to tenants.
        if ($domains->withinNamespace($host) && $host !== 'www.'.$domains->base()) {
            if (! $domains->availableHostname($host) || ! Tenant::where('domain', $host)->exists()) {
                return response()->view('storefront.not-found', [], 404, [
                    'Cache-Control' => 'no-store',
                    'X-Robots-Tag' => 'noindex, nofollow',
                ]);
            }
        }

        return $next($request);
    }
}
