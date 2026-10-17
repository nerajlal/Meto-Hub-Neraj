<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreIntegration extends Model
{
    protected $guarded = ['id', 'verified_domain', 'domain_token', 'domain_verified_at'];

    protected $casts = ['ga4_enabled' => 'boolean', 'gtm_enabled' => 'boolean', 'meta_enabled' => 'boolean', 'gsc_enabled' => 'boolean', 'domain_verified_at' => 'datetime'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
