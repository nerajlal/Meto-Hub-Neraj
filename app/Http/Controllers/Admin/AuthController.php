<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showCommonLogin(Request $request)
    {
        if (Auth::check()) {
            if (Auth::user()->type === 'super_admin') {
                return redirect()->route('super_admin.dashboard');
            }
            if (Auth::user()->type === 'admin' && Auth::user()->tenant_id) {
                return redirect()->route('admin.dashboard', ['tenant' => Auth::user()->tenant_id]);
            }
            if (Auth::user()->type === 'delivery_boy' && Auth::user()->tenant_id) {
                return redirect()->route('delivery.dashboard', ['tenant' => Auth::user()->tenant_id]);
            }
            Auth::logout();
        }
        return view('admin.auth.common-login');
    }

    public function commonLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->type === 'super_admin') {
                return redirect()->route('super_admin.dashboard');
            }

            if ($user->type === 'admin' && $user->tenant_id) {
                // Set the session active tenant ID
                session(['active_tenant_id' => $user->tenant_id]);
                return redirect()->route('admin.dashboard', ['tenant' => $user->tenant_id]);
            }

            if ($user->type === 'delivery_boy' && $user->tenant_id) {
                session(['active_tenant_id' => $user->tenant_id]);
                return redirect()->route('delivery.dashboard', ['tenant' => $user->tenant_id]);
            }

            // Fallback if user doesn't have a tenant or super_admin type
            Auth::logout();
            return back()->withErrors([
                'email' => 'This account does not have admin or delivery privileges, or is not associated with any store.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showLogin(Request $request, $tenant = null)
    {
        $tenant = $tenant ?? session('active_tenant_id') ?? 1;
        if (Auth::check()) {
            if (Auth::user()->type === 'super_admin' || Auth::user()->type === 'admin') {
                return redirect()->route('admin.dashboard', ['tenant' => $tenant]);
            }
            if (Auth::user()->type === 'delivery_boy') {
                return redirect()->route('delivery.dashboard', ['tenant' => $tenant]);
            }
            Auth::logout();
        }
        return view('admin.auth.login');
    }

    public function login(Request $request, $tenant = null)
    {
        $tenant = $tenant ?? session('active_tenant_id') ?? 1;
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->type === 'super_admin') {
                return redirect()->route('super_admin.dashboard');
            }
            
            if (Auth::user()->type === 'delivery_boy') {
                // Enforce strict tenant boundary checks
                $resolvedTenantId = session('active_tenant_id') ?? $tenant;
                if (Auth::user()->tenant_id != $resolvedTenantId) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'This account does not have access to this store.',
                    ])->onlyInput('email');
                }
                session(['active_tenant_id' => Auth::user()->tenant_id]);
                return redirect()->route('delivery.dashboard', ['tenant' => Auth::user()->tenant_id]);
            }
            
            if (Auth::user()->type !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account does not have access to this store.',
                ])->onlyInput('email');
            }

            // Enforce strict tenant boundary checks
            $resolvedTenantId = session('active_tenant_id') ?? $tenant;
            if (Auth::user()->tenant_id != $resolvedTenantId) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account does not have access to this store.',
                ])->onlyInput('email');
            }

            return redirect()->intended(route('admin.dashboard', ['tenant' => $tenant]));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request, $tenant = null)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.common.login');
    }
}
