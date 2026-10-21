<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerShipmentAlert extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['queued_at' => 'datetime', 'retry_at' => 'datetime', 'sent_at' => 'datetime', 'skipped_at' => 'datetime'];
}
