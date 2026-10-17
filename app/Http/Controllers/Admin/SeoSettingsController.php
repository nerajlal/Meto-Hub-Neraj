<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tenant;
use App\Services\StorefrontSeo;
use Illuminate\Http\Request;

class SeoSettingsController extends Controller
{
    public function index(StorefrontSeo $seo)
    {
        $tenant = Tenant::findOrFail(request()->attributes->get('resolved_tenant_id'));
        $checks = [
            ['Store description', filled($tenant->seo_description), 'Add a useful description of your store.'],
            ['Social sharing image', filled($tenant->seo_image) || filled($tenant->logo), 'Set a social image or upload your store logo.'],
            ['Indexing preference', $seo->indexable($tenant), 'Both the server and store settings must allow indexing.'],
            ['About your store', filled($tenant->about_text), 'Write original information about your business.'],
            ['Contact information', filled($tenant->contact_email) || filled($tenant->contact_phone), 'Provide a way for shoppers to contact you.'],
        ];
        $missingDescriptions = Product::where('status', 'active')->where(function ($q) {
            $q->whereNull('description')->orWhere('description', '');
        })->count();

        return view('admin.settings.seo', compact('tenant', 'checks', 'missingDescriptions', 'seo'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'seo_title' => 'nullable|string|max:120', 'seo_description' => 'nullable|string|max:320',
            'seo_image' => 'nullable|url:http,https|max:2048', 'search_indexable' => 'required|boolean',
        ]);
        Tenant::findOrFail($request->attributes->get('resolved_tenant_id'))->update($data);

        return back()->with('success', 'Search settings saved.');
    }
}
