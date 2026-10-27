<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, \App\Models\Traits\BelongsToTenant;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'shipping_cost',
        'discount_amount',
        'total_amount',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'billing_address',
        'notes',
        'delivery_date',
        'delivery_time_slot',
        'placed_at',
        'tracking_number',
        'delivery_partner_id',
        'delivery_boy_id',
        'tenant_id',
        'tax_name',
        'tax_rate',
        'tax_amount',
        'custom_checkout_data',
        'gateway_payment_id',
        'gateway_order_id',
        'gateway_name',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'custom_checkout_data' => 'array',
        'placed_at' => 'datetime',
        'payment_recorded_at' => 'datetime',
        'delivery_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryPartner()
    {
        return $this->belongsTo(DeliveryPartner::class);
    }

    public function trackingUrl(): ?string
    {
        $partner = $this->deliveryPartner;
        if (!$this->tracking_number || !$partner || (int) $partner->tenant_id !== (int) $this->tenant_id) return null;
        $url = str_replace('{tracking_number}', rawurlencode($this->tracking_number), (string) $partner->tracking_url_template);
        return filter_var($url, FILTER_VALIDATE_URL) && in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)
            && !parse_url($url, PHP_URL_USER) && !parse_url($url, PHP_URL_PASS) ? $url : null;
    }

    public function deliveryBoy()
    {
        return $this->belongsTo(User::class, 'delivery_boy_id');
    }
}
