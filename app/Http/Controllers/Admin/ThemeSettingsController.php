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

        // List of all supported themes in the system
        $themes = [
            [
                'id' => 'aura_luxe',
                'name' => 'Aura Luxe',
                'version' => 'Modern Luxury',
                'description' => 'Modern grocery store layout with a clean premium design, responsive category sidebar, weekly combos grid, and farm-fresh typography.',
                'preview_img' => '/Images/landing/v3-template.png',
                'preview_route' => 'v3.home',
            ],
            [
                'id' => 'template_2',
                'name' => 'Top Menu Premium',
                'version' => 'Top Menu Layout',
                'description' => 'Modern grocery store layout with a horizontal category top navigation menu, no sidebars, full-width content grids, and clean fresh fonts.',
                'preview_img' => '/Images/landing/v3-template.png',
                'preview_route' => 'v3.home',
            ],
            [
                'id' => 'template_3',
                'name' => 'Mobile App Interface',
                'version' => 'Compact Category Grids',
                'description' => 'Premium, mobile-first design featuring tight 3-column product grids, glassmorphism, category-based homepage, and a beautiful app-like feel.',
                'preview_img' => '/Images/landing/v3-template.png',
                'preview_route' => 'v3.home',
            ],
        ];

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
                Rule::in(['aura_luxe', 'template_2', 'template_3'])
            ]
        ]);

        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $tenant = Tenant::findOrFail($tenantId);
        
        $tenant->theme = $request->theme;
        $tenant->save();

        return redirect()->back()->with('success', 'Storefront theme updated successfully.');
    }
}
