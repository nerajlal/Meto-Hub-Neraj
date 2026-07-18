<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Prevent infinite recursion when loading the authenticated user
        if ($model instanceof \App\Models\User) {
            // Prevent infinite recursion by resolving tenant ID without calling auth()
            $tenantId = null;
            if (request()->query('preview') == 1) {
                $tenantId = request()->query('tenant_id') ?? 2;
            } elseif (request()->has('tenant_id')) {
                $tenantId = request()->query('tenant_id');
            } elseif (request()->route('tenant')) {
                $tenantId = request()->route('tenant');
            } elseif (session()->has('active_tenant_id')) {
                $tenantId = session('active_tenant_id');
            } elseif (session()->has('demo_tenant_id')) {
                $tenantId = session('demo_tenant_id');
            }

            if ($tenantId) {
                $builder->where($model->getTable() . '.tenant_id', $tenantId);
            }
            return;
        }

        // Determine tenant ID based on preview flag, query parameter, session, or auth state
        $tenantId = null;

        if (request()->query('preview') == 1) {
            $tenantId = request()->query('tenant_id') ?? 2;
            session(['active_tenant_id' => $tenantId]);
        } elseif (request()->has('tenant_id')) {
            $tenantId = request()->query('tenant_id');
            session(['active_tenant_id' => $tenantId]);
        } elseif (request()->has('tenant')) {
            $tenantId = request()->query('tenant');
            session(['active_tenant_id' => $tenantId]);
        } elseif (session()->has('active_tenant_id')) {
            $tenantId = session('active_tenant_id');
        } elseif (session()->has('demo_tenant_id')) {
            $tenantId = session('demo_tenant_id');
        } elseif (auth()->check()) {
            $user = auth()->user();
            
            // Super admins see everything
            if ($user->type === 'super_admin') {
                return;
            }

            $tenantId = $user->tenant_id ?? 1;
        } else {
            $tenantId = 1;
        }
        
        if ($tenantId) {
            $builder->where($model->getTable() . '.tenant_id', $tenantId);
        }
    }
}
