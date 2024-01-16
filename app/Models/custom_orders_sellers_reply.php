<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class custom_orders_sellers_reply extends Model
{
    use HasFactory;

    protected $table = 'custom_orders_sellers_replies';

    protected $fillable = ['custom_orders_seller_id','name','info','quantity','product_price','days_delivery','delivery_price'];

    public function images(){
        return $this->morphMany(images::class,'imageable');
    }

    public function custom_order_seller(){
        return $this->belongsTo(custom_orders_sellers::class,'custom_orders_seller_id');
    }
}
