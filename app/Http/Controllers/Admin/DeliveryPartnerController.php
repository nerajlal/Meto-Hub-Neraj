<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPartner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DeliveryPartner::latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $partners = $query->get();

        if ($request->ajax()) {
            return view('admin.settings.delivery_partners.partials.table', compact('partners'));
        }

        return view('admin.settings.delivery_partners.index', compact('partners'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['preset' => ['nullable', Rule::in(array_keys(config('delivery_partners')))]]);
        $tenantId = $request->attributes->get('resolved_tenant_id');
        if ($request->filled('preset')) {
            $preset = config('delivery_partners.'.$request->preset);
            DeliveryPartner::firstOrCreate(['tenant_id' => $tenantId, 'name' => $preset['name']], $preset + ['status' => true, 'is_default' => false]);
        } else {
            DeliveryPartner::create($this->validated($request) + ['tenant_id' => $tenantId]);
        }

        return redirect()->back()->with('success', 'Delivery Partner added successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $partner = DeliveryPartner::findOrFail($id);
        
        $partner->update($this->validated($request));

        return redirect()->back()->with('success', 'Delivery Partner updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $partner = DeliveryPartner::findOrFail($id);
        $partner->delete();

        return redirect()->back()->with('success', 'Delivery Partner deleted successfully!');
    }

    public function setDefault($id)
    {
        $partner = DeliveryPartner::findOrFail($id);
        // Unset any existing default
        DeliveryPartner::where('is_default', true)->update(['is_default' => false]);
        
        // Set the new default
        $partner->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Default Delivery Partner updated!');
    }
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'site_url' => 'nullable|url:http,https|max:2048',
            'tracking_url_template' => ['nullable', 'string', 'max:2048', 'url:http,https'],
            'status' => 'required|boolean',
        ]);
    }
}
