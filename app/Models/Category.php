<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Category extends Model
{
    //
    use HasApiTokens;
    protected $table = 'category'; // specify the table name
    protected $fillable = [
        'name',
        'status'
    ]; // specify the fillable fields
}
