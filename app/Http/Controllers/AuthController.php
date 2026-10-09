<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private function currentTenantId()
    {
        return session('active_tenant_id') 
            ?? (Auth::check() ? Auth::user()->tenant_id : null) 
            ?? session('demo_tenant_id') 
            ?? 1;
    }

    private function redirectToStorefrontHome($tenantId)
    {
        $tenant = \App\Models\Tenant::find($tenantId);
        if ($tenant) {
            return redirect()->route('v1.home', ['tenant_id' => $tenantId]);
        }
        return redirect('/');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('register_error', 'Please resolve the errors.')
                ->with('open_register', true); // Flag to repoen modal
        }

        $tenantId = $this->currentTenantId();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenantId,
        ]);

        Auth::login($user);
        
        // Sync session cart to DB
        \App\Http\Controllers\CartController::syncSession($user->id);

        return $this->redirectToStorefrontHome($tenantId)->with('success', 'Registration successful! Welcome to VESPR Perfumes.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $tenantId = $this->currentTenantId();

        if (Auth::attempt($credentials + ['tenant_id' => $tenantId])) {
            $request->session()->regenerate();
            
            // Sync session cart to DB
            \App\Http\Controllers\CartController::syncSession(Auth::id());

            return $this->redirectToStorefrontHome($tenantId);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->with('open_login', true); // Flag to reopen modal
    }

    public function logout(Request $request)
    {
        $tenantId = $this->currentTenantId();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session(['active_tenant_id' => $tenantId]);

        return $this->redirectToStorefrontHome($tenantId);
    }

    public function redirectToGoogle()
    {
        return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $tenantId = $this->currentTenantId();
        try {
            \Illuminate\Support\Facades\Log::info('Google Callback Started');
            
            // Bypass SSL verification for local development (cURL error 77 fix)
            $driver = \Laravel\Socialite\Facades\Socialite::driver('google');
            // Use the default HTTP client with certificate verification enabled.
            $googleUser = $driver->user();
            
            \Illuminate\Support\Facades\Log::info('Google User retrieved', ['email' => $googleUser->getEmail(), 'id' => $googleUser->getId()]);
        
            $user = User::where('tenant_id', $tenantId)->where('google_id', $googleUser->getId())->first();
        
            if (!$user) {
                \Illuminate\Support\Facades\Log::info('User by Google ID not found. Checking by Email.');
                
                // Check if user exists with same email, link account if so
                $user = User::where('tenant_id', $tenantId)->where('email', $googleUser->getEmail())->first();
        
                if ($user) {
                    \Illuminate\Support\Facades\Log::info('User found by email. Updating Google ID.');
                    $user->update(['google_id' => $googleUser->getId()]);
                } else {
                    \Illuminate\Support\Facades\Log::info('Creating new user.');
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'password' => Hash::make(\Illuminate\Support\Str::random(16)), // Random password
                        'email_verified_at' => now(), // Auto-verify Google users
                        'tenant_id' => $tenantId,
                    ]);
                    \Illuminate\Support\Facades\Log::info('New user created.');
                }
            }
        
            Auth::login($user);
            \Illuminate\Support\Facades\Log::info('User logged in.');

            // Smart redirect: Admin and Super Admin go to backend dashboards, normal users to storefront
            if ($user->type === 'super_admin') {
                return redirect()->route('super_admin.dashboard')->with('success', 'Logged in as Super Admin with Google successfully!');
            }

            if ($user->type === 'admin' && $user->tenant_id) {
                session(['active_tenant_id' => $user->tenant_id]);
                return redirect()->route('admin.dashboard', ['tenant' => $user->tenant_id])->with('success', 'Logged in with Google successfully!');
            }
            
            // Sync session cart to DB for customers
            \App\Http\Controllers\CartController::syncSession(Auth::id());
        
            return $this->redirectToStorefrontHome($tenantId)->with('success', 'Logged in with Google successfully!');
        
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Login Error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return $this->redirectToStorefrontHome($tenantId)->with('error', 'Google login failed. Please try again.');
        }
    }
}
