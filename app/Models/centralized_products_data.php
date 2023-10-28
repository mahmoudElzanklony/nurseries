<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class centralized_products_data extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','ar_name','en_name','ar_description','en_description','data'];
}
