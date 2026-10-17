<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreIntegration;
use App\Models\Tenant;
use App\Services\IntegrationDomainVerification;
use App\Services\StorefrontSeo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IntegrationSettingsController extends Controller
{
    private function tenant(): Tenant
    {
        return Tenant::findOrFail(request()->attributes->get('resolved_tenant_id'));
    }

    private function settings(Tenant $tenant): StoreIntegration
    {
        return StoreIntegration::firstOrCreate(['tenant_id' => $tenant->id]);
    }

    public function index(IntegrationDomainVerification $verification, StorefrontSeo $seo)
    {
        $tenant = $this->tenant();
        // Reading settings must not create or enable an integration.
        $settings = StoreIntegration::where('tenant_id', $tenant->id)->first() ?? new StoreIntegration;
        $gtmEligible = $verification->eligible($tenant, $settings);

        return view('admin.settings.integrations', compact('tenant', 'settings', 'gtmEligible', 'seo'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'mode' => ['required', Rule::in(['direct', 'gtm'])],
            'ga4_enabled' => 'required|boolean', 'gtm_enabled' => 'required|boolean', 'meta_enabled' => 'required|boolean', 'gsc_enabled' => 'required|boolean',
            'ga4_id' => ['nullable', 'required_if:ga4_enabled,1', 'string', 'regex:/^G-[A-Z0-9]{4,20}$/'],
            'gtm_id' => ['nullable', 'required_if:gtm_enabled,1', 'string', 'regex:/^GTM-[A-Z0-9]{4,20}$/'],
            'meta_pixel_id' => ['nullable', 'required_if:meta_enabled,1', 'string', 'regex:/^[0-9]{5,25}$/'],
            'gsc_token' => ['nullable', 'required_if:gsc_enabled,1', 'string', 'regex:/^[A-Za-z0-9_-]{10,200}$/'],
        ]);
        $settings = $this->settings($this->tenant());
        $settings->fill($data)->save();

        return back()->with('success', 'Integration settings saved. Complete the provider test steps below to confirm delivery.');
    }

    public function challenge(IntegrationDomainVerification $verification)
    {
        $tenant = $this->tenant();
        $verification->challenge($tenant, $this->settings($tenant));

        return back()->with('success', 'Add the displayed DNS TXT record, then verify ownership.');
    }

    public function verify(IntegrationDomainVerification $verification)
    {
        $tenant = $this->tenant();
        $verification->verify($tenant, $this->settings($tenant));

        return back()->with('success', 'Domain ownership verified for 30 days. Keep the TXT record and recheck before expiry.');
    }
}
