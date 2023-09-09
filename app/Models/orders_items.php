<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_items extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','product_id','quantity','price'];

    public function order(){
        return $this->belongsTo(orders::class,'order_id');
    }

    public function product(){
        return $this->belongsTo(products::class,'product_id');
    }

    public function features(){
        return $this->hasMany(orders_items_features::class,'order_item_id');
    }

    public function rate(){
        return $this->hasOne(orders_items_rates::class,'order_item_id');
    }

}
