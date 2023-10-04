<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_items_features extends Model
{
    use HasFactory;

    protected $casts = [
        'price' => 'double',
    ];

    protected $fillable = ['order_item_id','product_feature_id','price'];

    public function product_feature(){
        return $this->belongsTo(products_features_prices::class,'product_feature_id');
    }
}
