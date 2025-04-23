<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class order_items extends Model
{
    //
    protected $fillable = [
        'product_id',
        'name',
        'order_id',
        'price',
        'unit_price',
        'qty',
        
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime: d M Y',
            'updated_at' => 'datetime: d M Y',
        ];
    }

    // relationship with product
    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }
}
