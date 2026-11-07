<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Deliberately unscoped: every caller must select tenant/owner explicitly, including webhooks and CLI.
class CustomerPayment extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $hidden = ['credentials', 'snapshot', 'session_hash', 'checkout_data'];
    protected $casts = ['credentials' => 'encrypted:array', 'snapshot' => 'encrypted:array', 'checkout_data' => 'encrypted:array',
        'last_checked_at' => 'datetime', 'amount' => 'integer'];
}
