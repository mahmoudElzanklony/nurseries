<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_products_cares extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','product_id'];

    public function cares(){
        return $this->hasMany(products_care::class,'product_id');
    }
}
