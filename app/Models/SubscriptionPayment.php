<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['paid_at' => 'datetime', 'attempted_at' => 'datetime'];

    public function subscription()
    {
        return $this->belongsTo(StoreSubscription::class, 'store_subscription_id');
    }
}
