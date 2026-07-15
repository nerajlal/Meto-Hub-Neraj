<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    /**
     * Handle customer login via AJAX.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $tenantId = session('active_tenant_id') ?? session('demo_tenant_id') ?? 2;

        $customer = User::where('email', $request->email)
            ->where('tenant_id', $tenantId)
            ->first();

        // Allow super_admin without tenant check if they try logging in here
        if (!$customer) {
            $customer = User::where('email', $request->email)->where('type', 'super_admin')->first();
        }

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.'
            ], 401);
        }

        Auth::guard('web')->login($customer, $request->boolean('remember'));

        // Handle Admin Redirects
        if ($customer->type === 'super_admin') {
            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully.',
                'redirect' => route('super_admin.dashboard')
            ]);
        }

        if ($customer->type === 'admin') {
            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully.',
                'redirect' => route('admin.dashboard', ['tenant' => $customer->tenant_id ?? $tenantId])
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully.',
            'redirect' => url()->previous()
        ]);
    }

    /**
     * Handle customer registration via AJAX.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $tenantId = session('active_tenant_id') ?? session('demo_tenant_id') ?? 2;

        // Check if customer already exists for this tenant
        if (User::where('email', $request->email)->where('tenant_id', $tenantId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'An account with this email already exists for this store.'
            ], 422);
        }

        $customer = User::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'user'
        ]);

        Auth::guard('web')->login($customer);

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'redirect' => url()->previous()
        ]);
    }

    /**
     * Handle customer logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => url()->previous()
            ]);
        }

        return redirect()->back();
    }
}
