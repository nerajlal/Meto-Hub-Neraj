<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Scope users only after middleware has validated the tenant context.
        // Authentication resolves the user before middleware sets this attribute.
        if ($model instanceof \App\Models\User) {
            if ($tenantId = request()->attributes->get('resolved_tenant_id')) {
                $builder->where($model->getTable().'.tenant_id', $tenantId);
            }
            return;
        }

        $tenantId = request()->attributes->get('resolved_tenant_id');
        if (!$tenantId && auth()->check()) {
            if (auth()->user()->type === 'super_admin') {
                return;
            }
            $tenantId = auth()->user()->tenant_id;
        }
        $tenantId ??= session('active_tenant_id') ?? session('demo_tenant_id');

        // Never turn a missing tenant into an unrestricted query.
        if ($tenantId) {
            $builder->where($model->getTable().'.tenant_id', $tenantId);
        } else {
            $builder->whereRaw('1 = 0');
        }
    }
}
