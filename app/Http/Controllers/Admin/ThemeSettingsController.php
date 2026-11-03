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
        $tenantId = request()->attributes->get('resolved_tenant_id');
        $tenant = Tenant::findOrFail($tenantId);

        $themes = collect(app(\App\Services\StorefrontTheme::class)->all())
            ->map(fn ($theme, $id) => ['id' => $id, 'preview_route' => 'v1.home',
                'preview_img' => '/images/themes/'.$id.'.svg'] + $theme)->values()->all();

        return view('admin.settings.theme', compact('tenant', 'themes'));
    }

    public function updateColors(Request $request)
    {
        $colors = $request->validate(array_fill_keys(
            ['primary_color', 'dark_color', 'accent_color'],
            ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/']
        ));
        $tenant = Tenant::findOrFail($request->attributes->get('resolved_tenant_id'));
        $tenant->update($colors);
        return back()->with('success', 'Theme colors saved. These are the same colors used in Storefront settings and all theme previews.');
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

        $tenantId = request()->attributes->get('resolved_tenant_id');
        $tenant = Tenant::findOrFail($tenantId);
        
        $tenant->theme = $request->theme;
        $tenant->save();

        return redirect()->back()->with('success', 'Storefront theme updated successfully.');
    }
}
