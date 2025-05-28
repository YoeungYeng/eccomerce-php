<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    //
    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'status',
        'transaction_id',
    ];
    

    public function order(){
        return $this->belongsTo(orders::class, 'order_id', 'id');
    }
}
