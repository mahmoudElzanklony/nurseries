<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_items extends Model
{
    use HasFactory;

    protected $casts = [
        'price' => 'float',
    ];

    protected $fillable = ['order_id','product_id','quantity','price'];

    public function order(){
        return $this->belongsTo(orders::class,'order_id');
    }

    public function product(){
        return $this->belongsTo(products::class,'product_id');
    }

    public function coupon()
    {
        return $this->morphOne(users_coupons::class,'couponable');
    }

    public function features(){
        return $this->hasMany(orders_items_features::class,'order_item_id');
    }

    public function rate(){
        return $this->hasOne(orders_items_rates::class,'order_item_id');
    }

    public function cancelled(){
        return $this->hasOne(cancelled_orders_items::class,'order_item_id')->where('type','=','order');
    }

}
