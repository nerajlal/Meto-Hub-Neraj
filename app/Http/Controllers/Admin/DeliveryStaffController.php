<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DeliveryStaffController extends Controller
{
    public function index()
    {
        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;
        $staff = User::where('tenant_id', $tenantId)
                    ->where('type', 'delivery_boy')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('admin.delivery_staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.delivery_staff.create');
    }

    public function store(Request $request)
    {
        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })
            ],
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'type' => 'delivery_boy',
            'tenant_id' => $tenantId,
        ]);

        return redirect()->route('admin.delivery-staff.index')->with('success', 'Delivery staff created successfully.');
    }

    public function edit($id)
    {
        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;
        $staff = User::where('tenant_id', $tenantId)
                    ->where('type', 'delivery_boy')
                    ->findOrFail($id);

        return view('admin.delivery_staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;
        $staff = User::where('tenant_id', $tenantId)
                    ->where('type', 'delivery_boy')
                    ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($staff->id)
            ],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $staff->update($data);

        return redirect()->route('admin.delivery-staff.index')->with('success', 'Delivery staff updated successfully.');
    }

    public function destroy($id)
    {
        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;
        $staff = User::where('tenant_id', $tenantId)
                    ->where('type', 'delivery_boy')
                    ->findOrFail($id);

        $staff->delete();

        return redirect()->route('admin.delivery-staff.index')->with('success', 'Delivery staff deleted successfully.');
    }
}
