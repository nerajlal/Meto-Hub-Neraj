<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantOrderAlert extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['read_at' => 'datetime', 'emailed_at' => 'datetime', 'email_retry_at' => 'datetime', 'queued_at' => 'datetime'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function deliveries()
    {
        return $this->hasMany(MerchantPushDelivery::class);
    }
}
