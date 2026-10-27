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
        'cod_enabled',
        'whatsapp_checkout_enabled',
        'stripe_enabled',
        'stripe_key',
        'stripe_secret',
        'razorpay_enabled',
        'razorpay_key',
        'razorpay_secret',
        'phonepe_enabled',
        'phonepe_merchant_id',
        'phonepe_salt_key',
        'phonepe_salt_index',
    ];

    protected $casts = [
        'cod_enabled' => 'boolean',
        'whatsapp_checkout_enabled' => 'boolean',
        'stripe_enabled' => 'boolean',
        'razorpay_enabled' => 'boolean',
        'phonepe_enabled' => 'boolean',
    ];
}
