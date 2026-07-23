<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalProductImage extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'image_path', 'status'];
}
