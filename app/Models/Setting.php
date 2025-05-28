<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $table = 'settings'; // specify the table name
    protected $fillable = [
        "title",
        "phone",
        "address",
        "link",
        "logo"
    ];
}
