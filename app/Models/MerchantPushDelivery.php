<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantPushDelivery extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['sent_at' => 'datetime', 'failed_at' => 'datetime', 'retry_at' => 'datetime'];

    public function subscription()
    {
        return $this->belongsTo(MerchantPushSubscription::class, 'merchant_push_subscription_id');
    }
}
