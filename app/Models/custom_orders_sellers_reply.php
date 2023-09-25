<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class custom_orders_sellers_reply extends Model
{
    use HasFactory;

    protected $fillable = ['custom_orders_seller_id','info','product_price','days_delivery','delivery_price','client_reply'];

    public function images(){
        return $this->morphMany(images::class,'imagable');
    }

    public function custom_order_seller(){
        return $this->belongsTo(custom_orders_sellers::class,'custom_orders_seller_id');
    }
}
