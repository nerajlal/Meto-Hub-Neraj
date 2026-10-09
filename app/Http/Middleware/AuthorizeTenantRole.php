<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeTenantRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        $tenantId = $request->attributes->get('resolved_tenant_id');
        abort_unless($user && $tenantId, 403);
        if ($user->type !== 'super_admin') {
            abort_unless($user->type === $role && (int) $user->tenant_id === (int) $tenantId, 403);
        }

        return $next($request);
    }
}
