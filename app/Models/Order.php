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
        'placed_at',
        'tracking_number',
        'delivery_partner_id',
        'tenant_id',
        'tax_name',
        'tax_rate',
        'tax_amount'
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'placed_at' => 'datetime',
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
}
