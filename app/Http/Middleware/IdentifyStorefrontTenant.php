<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyStorefrontTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $hostTenant = app(\App\Services\IntegrationDomainVerification::class)->isPlatformHost($request->getHost()) ? null : Tenant::where('domain', $request->getHost())->first();
        $slug = $request->route('store_slug') ?? $request->query('store');
        $slugTenant = null;
        if ($slug !== null) {
            abort_unless(is_string($slug) && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug), 404);
            $slugTenant = Tenant::where('slug', $slug)->firstOrFail();
            abort_if($hostTenant && $hostTenant->id !== $slugTenant->id, 404);
        }
        $tenantId = $hostTenant?->id
            ?? $slugTenant?->id
            ?? $request->query('tenant_id')
            ?? $request->query('tenant')
            ?? session('active_tenant_id')
            ?? $request->user()?->tenant_id
            ?? session('demo_tenant_id')
            ?? 2;
        abort_unless(filter_var($tenantId, FILTER_VALIDATE_INT) && (int) $tenantId > 0, 404);
        $tenant = $hostTenant ?? $slugTenant ?? Tenant::findOrFail($tenantId);

        $access = app(\App\Services\PlanAccess::class);
        if (! $access->storefrontAvailable($tenant)) {
            $headers = ['Cache-Control' => 'private, no-store', 'Retry-After' => '3600', 'X-Robots-Tag' => 'noindex, nofollow'];
            if ($request->expectsJson() || ! $request->isMethodSafe()) {
                return response()->json(['success' => false, 'message' => 'This store is temporarily unavailable because its subscription needs renewal.'], 503, $headers);
            }
            return response()->view('storefront.unavailable', [], 503, $headers);
        }
        if ($hostTenant) abort_unless($access->storefrontDomainAvailable($tenant), 403, 'This store’s custom domain is not available on its current plan.');

        $user = $request->user();
        if ($user && $user->type !== 'super_admin') {
            abort_unless((int) $user->tenant_id === (int) $tenant->id, 403);
        }
        if (session('active_tenant_id') && (int) session('active_tenant_id') !== (int) $tenant->id) {
            session()->forget('cart');
        }
        $request->attributes->set('resolved_tenant_id', $tenant->id);
        $request->attributes->set('resolved_storefront_tenant', $tenant);
        if ($request->route('store_slug') !== null) {
            $request->route()->forgetParameter('store_slug');
        }
        session(['active_tenant_id' => $tenant->id]);
        view()->share('currentTenant', $tenant);

        return $next($request);
    }
}
