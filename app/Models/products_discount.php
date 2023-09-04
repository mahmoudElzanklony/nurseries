<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products_discount extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','discount','start_date','end_date'];
}
