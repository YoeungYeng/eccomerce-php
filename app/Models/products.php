<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    //
   

    protected $fillable = [
        'title',
        'price',
        'quantity',
        'description',
        'short_description',
        'category_id',
        'brand_id',
        'status',
        'is_feature',
        'image'
    ];


    // Accessors to append to model's array and JSON form
    protected $appends = ['image_url'];

    /**
     * Accessor for the full image URL
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset("storage/{$this->image}") : null;
    }

    public function favoiteByUser()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * The brand this product belongs to
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id' );
    }
    
}
