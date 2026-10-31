<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use \App\Models\Traits\BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'product_id',
        'name',
        'review',
        'rating',
        'status',
        'admin_reply',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
