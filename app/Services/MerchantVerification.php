<?php

namespace App\Services;

use App\Mail\MerchantVerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class MerchantVerification
{
    public function send(User $user): void
    {
        if (app()->environment('production') && in_array(config('mail.default'), ['log', 'array', 'failover', 'roundrobin'], true)) {
            throw new \RuntimeException('A real email delivery provider must be configured for verification.');
        }
        $token = Str::random(64);
        $expires = now()->addHour();
        $user->forceFill(['email_verification_token_hash' => hash('sha256', $token), 'email_verification_expires_at' => $expires])->save();
        $path = URL::temporarySignedRoute('saas.verify', $expires, ['id' => $user->id, 'token' => $token, 'email_hash' => hash('sha256', $user->email)], absolute: false);
        Mail::to($user->email)->send(new MerchantVerificationMail($user->name, rtrim(config('app.url'), '/').$path));
    }
}
