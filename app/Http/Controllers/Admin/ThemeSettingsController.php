<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThemeSettingsController extends Controller
{
    /**
     * Show the theme selection settings page.
     */
    public function index()
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $tenant = Tenant::findOrFail($tenantId);

        $themes = collect(app(\App\Services\StorefrontTheme::class)->all())
            ->map(fn ($theme, $id) => ['id' => $id, 'preview_route' => 'v1.home',
                'preview_img' => '/images/themes/'.$id.'.svg'] + $theme)->values()->all();

        return view('admin.settings.theme', compact('tenant', 'themes'));
    }

    /**
     * Update the storefront theme.
     */
    public function update(Request $request)
    {
        $request->validate([
            'theme' => [
                'required',
                Rule::in(array_keys(app(\App\Services\StorefrontTheme::class)->all()))
            ]
        ]);

        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $tenant = Tenant::findOrFail($tenantId);
        
        $tenant->theme = $request->theme;
        $tenant->save();

        return redirect()->back()->with('success', 'Storefront theme updated successfully.');
    }
}
