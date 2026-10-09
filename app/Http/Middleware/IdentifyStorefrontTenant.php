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
        $tenantId = $hostTenant?->id
            ?? $request->query('tenant_id')
            ?? $request->query('tenant')
            ?? session('active_tenant_id')
            ?? $request->user()?->tenant_id
            ?? session('demo_tenant_id')
            ?? 2;
        abort_unless(filter_var($tenantId, FILTER_VALIDATE_INT) && (int) $tenantId > 0, 404);
        $tenant = $hostTenant ?? Tenant::findOrFail($tenantId);

        $user = $request->user();
        if ($user && $user->type !== 'super_admin') {
            abort_unless((int) $user->tenant_id === (int) $tenant->id, 403);
        }
        if (session('active_tenant_id') && (int) session('active_tenant_id') !== (int) $tenant->id) {
            session()->forget('cart');
        }
        $request->attributes->set('resolved_tenant_id', $tenant->id);
        session(['active_tenant_id' => $tenant->id]);
        view()->share('currentTenant', $tenant);

        return $next($request);
    }
}
