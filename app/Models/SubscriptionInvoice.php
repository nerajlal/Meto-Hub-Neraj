<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['billing_start' => 'datetime', 'billing_end' => 'datetime', 'paid_at' => 'datetime', 'issued_at' => 'datetime', 'due_at' => 'datetime'];

    public function subscription()
    {
        return $this->belongsTo(StoreSubscription::class, 'store_subscription_id');
    }
}
