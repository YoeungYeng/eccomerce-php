<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Slide extends Model
{
    /** @use HasFactory<\Database\Factories\SlideFactory> */
    use HasFactory, HasApiTokens;
    protected $fillable = [
        'title',
        'subtitle',
        'image',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
