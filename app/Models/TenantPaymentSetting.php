<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantPaymentSetting extends Model
{
    use HasFactory, \App\Models\Traits\BelongsToTenant;

    protected $table = 'tenant_payment_settings';

    protected $fillable = [
        'tenant_id',
        'paytm_enabled', 'paytm_mid', 'paytm_merchant_key', 'paytm_environment', 'paytm_website_name',
        'phonepe_client_id', 'phonepe_client_version', 'phonepe_client_secret', 'phonepe_environment',
        'phonepe_webhook_username', 'phonepe_webhook_password',
        'cod_enabled',
        'whatsapp_checkout_enabled',
        'stripe_enabled',
        'stripe_key',
        'stripe_secret',
        'razorpay_enabled',
        'razorpay_key',
        'razorpay_secret',
        'razorpay_webhook_secret',
        'phonepe_enabled',
        'phonepe_merchant_id',
        'phonepe_salt_key',
        'phonepe_salt_index',
    ];

    protected $hidden = ['razorpay_secret', 'razorpay_webhook_secret', 'stripe_secret', 'phonepe_salt_key', 'paytm_merchant_key', 'phonepe_client_secret', 'phonepe_webhook_password'];

    protected $casts = [
        'paytm_enabled' => 'boolean', 'phonepe_client_version' => 'integer',
        'paytm_merchant_key' => 'encrypted', 'phonepe_client_secret' => 'encrypted', 'phonepe_webhook_password' => 'encrypted',
        'razorpay_secret' => 'encrypted',
        'razorpay_webhook_secret' => 'encrypted',
        'stripe_secret' => 'encrypted',
        'phonepe_salt_key' => 'encrypted',
        'cod_enabled' => 'boolean',
        'whatsapp_checkout_enabled' => 'boolean',
        'stripe_enabled' => 'boolean',
        'razorpay_enabled' => 'boolean',
        'phonepe_enabled' => 'boolean',
    ];
}
