<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'plan',
        'theme',
        'about_title',
        'about_text',
        'contact_email',
        'contact_phone',
        'whatsapp_number',
        'contact_address',
        'shipping_policy',
        'return_policy',
        'terms_of_service',
        'mobile_grid_cols',
        'logo',
        'favicon',
        'primary_color',
        'dark_color',
        'accent_color',
        'currency',
        'tax_name',
        'tax_rate',
        'delivery_days',
        'delivery_info',
        'checkout_fields',
        'min_order_value',
        'domain',
        'razorpay_customer_id',
        'razorpay_subscription_id',
        'subscription_status',
    ];

    protected $casts = [
        'settings' => 'array',
        'checkout_fields' => 'array',
    ];

    public function getCurrencyAttribute($value)
    {
        $symbols = [
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'AUD' => 'A$',
            'CAD' => 'C$'
        ];
        return $symbols[$value] ?? $value;
    }

    public function admin()
    {
        return $this->hasOne(User::class, 'tenant_id')->where('type', 'admin');
    }
}
