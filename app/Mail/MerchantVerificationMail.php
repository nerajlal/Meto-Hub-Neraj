<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class MerchantVerificationMail extends Mailable
{
    public function __construct(public string $merchantName, public string $verificationUrl) {}

    public function build(): static
    {
        return $this->subject('Verify your MetoHub email address')->view('emails.merchant_verification');
    }
}
