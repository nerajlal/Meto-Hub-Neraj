<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantPaymentSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Show domain settings view.
     */
    public function domainIndex()
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $tenant = Tenant::findOrFail($tenantId);

        return view('admin.settings.domain', compact('tenant'));
    }

    /**
     * Update domain settings.
     */
    public function domainUpdate(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $tenant = Tenant::findOrFail($tenantId);

        $request->validate([
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $tenantId,
        ]);

        $tenant->update([
            'domain' => $request->domain,
        ]);

        return redirect()->back()->with('success', 'Domain settings updated successfully.');
    }

    /**
     * Show payment settings view.
     */
    public function paymentIndex()
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $paymentSetting = TenantPaymentSetting::firstOrCreate(['tenant_id' => $tenantId]);

        return view('admin.settings.payment', compact('paymentSetting'));
    }

    /**
     * Update payment settings.
     */
    public function paymentUpdate(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $paymentSetting = TenantPaymentSetting::firstOrCreate(['tenant_id' => $tenantId]);

        $request->validate([
            'cod_enabled' => 'nullable|boolean',
            'stripe_enabled' => 'nullable|boolean',
            'stripe_key' => 'nullable|required_if:stripe_enabled,1|string|max:255',
            'stripe_secret' => 'nullable|required_if:stripe_enabled,1|string|max:255',
            'razorpay_enabled' => 'nullable|boolean',
            'razorpay_key' => 'nullable|required_if:razorpay_enabled,1|string|max:255',
            'razorpay_secret' => 'nullable|required_if:razorpay_enabled,1|string|max:255',
            'phonepe_enabled' => 'nullable|boolean',
            'phonepe_merchant_id' => 'nullable|required_if:phonepe_enabled,1|string|max:255',
            'phonepe_salt_key' => 'nullable|required_if:phonepe_enabled,1|string|max:255',
            'phonepe_salt_index' => 'nullable|required_if:phonepe_enabled,1|string|max:255',
        ]);

        $paymentSetting->update([
            'cod_enabled' => $request->has('cod_enabled'),
            'stripe_enabled' => $request->has('stripe_enabled'),
            'stripe_key' => $request->stripe_key,
            'stripe_secret' => $request->stripe_secret,
            'razorpay_enabled' => $request->has('razorpay_enabled'),
            'razorpay_key' => $request->razorpay_key,
            'razorpay_secret' => $request->razorpay_secret,
            'phonepe_enabled' => $request->has('phonepe_enabled'),
            'phonepe_merchant_id' => $request->phonepe_merchant_id,
            'phonepe_salt_key' => $request->phonepe_salt_key,
            'phonepe_salt_index' => $request->phonepe_salt_index,
        ]);

        return redirect()->back()->with('success', 'Payment gateway settings updated successfully.');
    }

    /**
     * Show storefront pages settings view.
     */
    public function storefrontIndex()
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $tenant = Tenant::findOrFail($tenantId);

        return view('admin.settings.storefront', compact('tenant'));
    }

    /**
     * Update storefront pages settings.
     */
    public function storefrontUpdate(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $tenant = Tenant::findOrFail($tenantId);

        $request->validate([
            'about_title' => 'required|string|max:255',
            'about_text' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'contact_address' => 'nullable|string',
            'shipping_policy' => 'nullable|string',
            'return_policy' => 'nullable|string',
            'terms_of_service' => 'nullable|string',
            'mobile_grid_cols' => 'required|integer|in:1,2,3',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'dark_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'currency' => 'required|string|in:INR,USD,EUR,GBP,AED,CAD,AUD',
            'tax_name' => 'nullable|string|max:50',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'delivery_days' => 'nullable|integer|min:0|max:30',
            'delivery_info' => 'nullable|string',
            'min_order_value' => 'nullable|numeric|min:0',
        ]);

        $updateData = [
            'about_title' => $request->about_title,
            'about_text' => $request->about_text,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'whatsapp_number' => $request->whatsapp_number,
            'contact_address' => $request->contact_address,
            'shipping_policy' => $request->shipping_policy,
            'return_policy' => $request->return_policy,
            'terms_of_service' => $request->terms_of_service,
            'mobile_grid_cols' => $request->mobile_grid_cols,
            'primary_color' => $request->primary_color,
            'dark_color' => $request->dark_color,
            'accent_color' => $request->accent_color,
            'currency' => $request->currency,
            'tax_name' => $request->tax_name,
            'tax_rate' => $request->tax_rate,
            'delivery_days' => $request->delivery_days ?? 2,
            'delivery_info' => $request->delivery_info,
            'min_order_value' => $request->min_order_value ?? 0,
        ];

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($tenant->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($tenant->logo);
            }
            $updateData['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $tenant->update($updateData);

        return redirect()->back()->with('success', 'Storefront pages settings updated successfully.');
    }

    /**
     * Show the checkout fields settings page.
     */
    public function checkoutFields()
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $tenant = Tenant::findOrFail($tenantId);
        
        $fields = $tenant->checkout_fields ?? [];
        
        return view('admin.settings.checkout_fields', compact('tenant', 'fields'));
    }

    /**
     * Update the checkout fields configuration.
     */
    public function checkoutFieldsUpdate(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $tenant = Tenant::findOrFail($tenantId);

        $fieldsData = $request->input('fields', []);
        
        // Ensure standard structure
        $checkoutFields = [
            // Standard Fields
            'name' => [
                'enabled' => isset($fieldsData['name']['enabled']),
                'required' => isset($fieldsData['name']['required']),
            ],
            'email' => [
                'enabled' => isset($fieldsData['email']['enabled']),
                'required' => isset($fieldsData['email']['required']),
            ],
            'phone' => [
                'enabled' => isset($fieldsData['phone']['enabled']),
                'required' => isset($fieldsData['phone']['required']),
            ],
            'use_current_location' => [
                'enabled' => isset($fieldsData['use_current_location']['enabled']),
                'required' => false,
            ],
            'delivery_date' => [
                'enabled' => isset($fieldsData['delivery_date']['enabled']),
                'required' => true,
            ],
            'delivery_time_slot' => [
                'enabled' => isset($fieldsData['delivery_time_slot']['enabled']),
                'required' => true,
            ],
            'cash_on_delivery' => [
                'enabled' => isset($fieldsData['cash_on_delivery']['enabled']),
                'required' => false,
            ],
            'pay_online' => [
                'enabled' => isset($fieldsData['pay_online']['enabled']),
                'required' => false,
            ],
            'address' => [
                'enabled' => isset($fieldsData['address']['enabled']),
                'required' => isset($fieldsData['address']['required']),
            ],
            'city' => [
                'enabled' => isset($fieldsData['city']['enabled']),
                'required' => isset($fieldsData['city']['required']),
            ],
            'state' => [
                'enabled' => isset($fieldsData['state']['enabled']),
                'required' => isset($fieldsData['state']['required']),
            ],
            'pincode' => [
                'enabled' => isset($fieldsData['pincode']['enabled']),
                'required' => isset($fieldsData['pincode']['required']),
            ],
            
            // Custom Extended Fields
            'company_name' => [
                'enabled' => isset($fieldsData['company_name']['enabled']),
                'required' => isset($fieldsData['company_name']['required']),
            ],
            'gst_number' => [
                'enabled' => isset($fieldsData['gst_number']['enabled']),
                'required' => isset($fieldsData['gst_number']['required']),
            ],
            'alternate_phone' => [
                'enabled' => isset($fieldsData['alternate_phone']['enabled']),
                'required' => isset($fieldsData['alternate_phone']['required']),
            ],
            'landmark' => [
                'enabled' => isset($fieldsData['landmark']['enabled']),
                'required' => isset($fieldsData['landmark']['required']),
            ],
            'order_notes' => [
                'enabled' => isset($fieldsData['order_notes']['enabled']),
                'required' => isset($fieldsData['order_notes']['required']),
            ]
        ];

        $tenant->update(['checkout_fields' => $checkoutFields]);

        return redirect()->back()->with('success', 'Checkout fields configuration updated successfully.');
    }
}
