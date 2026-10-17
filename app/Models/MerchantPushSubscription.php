<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantPushSubscription extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['endpoint', 'public_key', 'auth_token', 'endpoint_hash'];

    protected $casts = ['endpoint' => 'encrypted', 'public_key' => 'encrypted', 'auth_token' => 'encrypted', 'enabled' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
