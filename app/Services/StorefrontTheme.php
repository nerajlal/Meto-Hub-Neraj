<?php

namespace App\Services;

use App\Models\Tenant;

class StorefrontTheme
{
    public function all(): array
    {
        return config('storefront_themes');
    }

    public function normalize(?string $theme): string
    {
        $aliases = ['v1' => 'template_1', 'nurah' => 'template_1', 'modern_minimal' => 'template_1',
            'v2' => 'template_2', 'velvet' => 'template_2', 'velvet_dark' => 'template_2',
            'v3' => 'template_3', 'app' => 'template_3', 'aura_luxe' => 'template_3',
            'v4' => 'template_4', 'ajmal' => 'template_4', 'editorial_cream' => 'template_4',
            'v5' => 'template_5', 'afnan' => 'template_5'];
        $theme = $aliases[$theme ?? ''] ?? $theme;

        return isset($this->all()[$theme ?? '']) ? $theme : 'template_1';
    }

    public function current(): array
    {
        if ($theme = request()->attributes->get('storefront_theme')) {
            return $theme;
        }
        $tenantId = request()->attributes->get('resolved_tenant_id') ?? session('active_tenant_id');
        $tenant = $tenantId ? Tenant::find($tenantId) : null;
        $id = $this->normalize($tenant?->theme);
        if (request()->query('preview') == 1 && request()->filled('preview_theme')) {
            $preview = request()->query('preview_theme');
            abort_unless(is_string($preview) && isset($this->all()[$preview]), 404);
            $id = $preview;
        }
        $theme = ['id' => $id] + $this->all()[$id];
        request()->attributes->set('storefront_theme', $theme);

        return $theme;
    }

    public function view(string $page): string
    {
        if ($page === 'home' && $this->current()['id'] !== 'template_1') {
            return 'storefront.themes.home';
        }
        if ($this->current()['id'] !== 'template_1' && in_array($page, ['product-main', 'all-products', 'collection'], true)) {
            return $page === 'product-main' ? 'storefront.themes.product' : 'storefront.themes.catalog';
        }

        return 'template_1.'.$page;
    }

    public function url(string $name, array $parameters = []): string
    {
        // Canonical routes keep navigation on the selected theme on every page.
        $name = preg_replace('/^(v[1-5]|velvet)\./', 'v1.', $name);
        if (request()->query('preview') == 1 && request()->filled('preview_theme')) {
            $parameters += ['preview' => 1, 'preview_theme' => $this->current()['id']];
        }
        $tenantId = request()->attributes->get('resolved_tenant_id') ?? session('active_tenant_id');
        if ($tenantId) {
            $tenant = request()->attributes->get('resolved_storefront_tenant') ?? Tenant::find($tenantId);
            if ($tenant?->slug) {
                unset($parameters['tenant_id'], $parameters['tenant'], $parameters['store']);
                if (str_starts_with($name, 'v1.')) {
                    $name = 'storefront.'.substr($name, 3);
                    $parameters['store_slug'] = $tenant->slug;
                } else {
                    $parameters['store'] = $tenant->slug;
                }
            } else {
                $parameters += ['tenant_id' => $tenantId];
            }
        }

        return route($name, $parameters);
    }
}
