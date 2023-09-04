<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class products extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['name','description','price','quantity'];

    public function images(){
        return $this->morphMany(images::class,'imageable');
    }

    public function wholesale_prices(){
        return $this->hasMany(products_wholesale_prices::class,'product_id');
    }

    public function discounts(){
        return $this->hasMany(products_discount::class,'product_id');
    }

    public function features(){
        return $this->hasMany(products_features_prices::class,'product_id');
    }
}
