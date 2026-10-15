<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MerchantVerification;
use App\Services\TenantCatalogSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', 'business' => 'required|string|max:255',
            'country' => 'required|string|max:255', 'email' => 'required|email|unique:users,email',
            'whatsapp' => 'required|string|max:30', 'password' => 'required|string|min:8',
            'plan' => 'nullable|in:base,pro,enterprise,sprout,blossom,tree',
            'theme' => ['required', 'string', Rule::in(array_keys(config('storefront_themes')))],
        ]);
        $user = DB::transaction(function () use ($validated) {
            $tenant = Tenant::create([
                'name' => $validated['business'], 'plan' => 'base', 'theme' => $validated['theme'],
                'contact_email' => $validated['email'], 'contact_phone' => $validated['whatsapp'],
                'whatsapp_number' => preg_replace('/\D/', '', $validated['whatsapp']),
            ]);
            $user = User::create(['name' => $validated['name'], 'email' => $validated['email'],
                'password' => Hash::make($validated['password']), 'type' => 'admin',
                'site_name' => $validated['business'], 'tenant_id' => $tenant->id, 'phone' => $validated['whatsapp']]);
            $user->forceFill(['email_verification_required' => true, 'email_verified_at' => null])->save();
            TenantCatalogSeeder::seedForTenant($tenant->id);

            return $user;
        });
        $sent = true;
        try {
            app(MerchantVerification::class)->send($user);
        } catch (\Throwable $error) {
            report($error);
            $sent = false;
        }

        return response()->json(['success' => true, 'email_sent' => $sent, 'email' => $user->email,
            'message' => $sent ? 'Check your inbox to verify your email, then sign in.' : 'Your account was created, but the email could not be sent. Please retry using Resend link.'], 201);
    }

    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::withoutGlobalScopes()->where('email', $request->email)->where('type', 'admin')->where('email_verification_required', true)->whereNull('email_verified_at')->first();
        if ($user) {
            try {
                app(MerchantVerification::class)->send($user);
            } catch (\Throwable $error) {
                report($error);
                if (! $request->expectsJson()) {
                    return back()->withErrors(['email' => 'Unable to send verification email. Please try again later.'])->withInput($request->only('email'));
                }

                return response()->json(['message' => 'Unable to send verification email. Please try again later.'], 503);
            }
        }
        $message = 'If this account needs verification, a new link has been sent.';

        return $request->expectsJson() ? response()->json(['message' => $message]) : back()->with('success', $message);
    }

    public function verify(Request $request, $id, $token)
    {
        DB::transaction(function () use ($request, $id, $token) {
            $user = User::withoutGlobalScopes()->lockForUpdate()->findOrFail($id);
            abort_unless($user->email_verification_required && ! $user->email_verified_at && $user->email_verification_token_hash &&
                $user->email_verification_expires_at?->isFuture() &&
                hash_equals($user->email_verification_token_hash, hash('sha256', $token)) &&
                hash_equals(hash('sha256', $user->email), (string) $request->query('email_hash')), 403, 'This verification link is invalid or expired. Request a new link.');
            $user->forceFill(['email_verified_at' => now(), 'email_verification_token_hash' => null, 'email_verification_expires_at' => null])->save();
        });

        return redirect()->route('admin.common.login')->with('success', 'Email verified. Sign in using your password.');
    }
}
