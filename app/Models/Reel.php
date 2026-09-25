<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    use HasFactory, \App\Models\Traits\BelongsToTenant;

    protected $fillable = [
        'instagram_url',
        'title',
        'thumbnail',
        'link_type',
        'product_id',
        'collection_id',
        'status',
        'order',
        'tenant_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Get the linked item (product or collection) based on link_type
     */
    public function getLinkedItemAttribute()
    {
        return $this->link_type === 'product' ? $this->product : $this->collection;
    }
}
