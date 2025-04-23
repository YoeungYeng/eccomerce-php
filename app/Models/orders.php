<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    //
    public function item()
    {
        return $this->hasMany(order_items::class, 'order_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime: d M Y',
            'updated_at' => 'datetime: d M Y',
        ];
    }
}
