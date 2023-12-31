<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products_features_prices extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','category_feature_id','price','note'];

    public function feature(){
        return $this->belongsTo(categories_features::class,'category_feature_id');
    }
}
