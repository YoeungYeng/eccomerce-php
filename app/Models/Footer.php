<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    // table name
    protected $table = 'footers';
    // fillable attributes
    protected $fillable = [
        'name',
        'link',
        'icon',
        'copy_right',
    ];
}
