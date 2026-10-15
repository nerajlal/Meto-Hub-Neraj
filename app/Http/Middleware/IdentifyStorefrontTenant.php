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
        $hostTenant = Tenant::where('domain', $request->getHost())->first();
        if ($hostTenant) abort_unless(app(\App\Services\PlanAccess::class)->allows($hostTenant, 'custom_domain'), 403, 'This store’s custom domain is not available on its current plan.');
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
