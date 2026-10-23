<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant) {
            $base = rtrim(\Illuminate\Support\Str::limit(\Illuminate\Support\Str::slug($tenant->name), 120, ''), '-') ?: 'store';
            $slug = $base;
            while (static::where('slug', $slug)->exists()) {
                $slug = $base.'-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
            }
            $tenant->slug = $slug;
        });
    }

    public function storefrontUrl(array $parameters = []): string
    {
        return route('storefront.home', ['store_slug' => $this->slug] + $parameters);
    }

    protected $fillable = [
        'seo_title', 'seo_description', 'seo_image', 'search_indexable',
        'name',
        'plan',
        'theme',
        'hero_eyebrow',
        'hero_heading',
        'hero_description',
        'hero_button_label',
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
        'maintenance_mode',
        'maintenance_message',
        'deletion_requested_at',
        'deletion_reason',
    ];

    protected $casts = [
        'search_indexable' => 'boolean',
        'maintenance_mode' => 'boolean',
        'deletion_requested_at' => 'datetime',
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
            'CAD' => 'C$',
        ];

        return $symbols[$value] ?? $value;
    }

    public function subscriptions()
    {
        return $this->hasMany(StoreSubscription::class);
    }

    public function admin()
    {
        return $this->hasOne(User::class, 'tenant_id')->where('type', 'admin');
    }
}
