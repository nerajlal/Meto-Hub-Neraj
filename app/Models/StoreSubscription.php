<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSubscription extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'paid_from' => 'datetime', 'paid_through' => 'datetime', 'current_start' => 'datetime', 'current_end' => 'datetime', 'next_charge_at' => 'datetime',
        'ended_at' => 'datetime', 'synced_at' => 'datetime', 'invoices_synced_at' => 'datetime', 'cancel_at_cycle_end' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }
}
