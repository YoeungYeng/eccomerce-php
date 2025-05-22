<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Brand extends Model
{
    //
    use  HasApiTokens;
    protected $table = 'brand'; // specify the table name
    protected $fillable = [
        'name',
        'status'
    ]; // specify the fillable fields
}
