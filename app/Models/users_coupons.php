<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_coupons extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','coupon_id','couponable_id','couponable_type','total_price_before_apply','coupon_value'];

    public function couponable(){
        return $this->morphTo();
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function order(){
        return $this->belongsTo(orders::class,'couponable_id');
    }

    public function coupon(){
        return $this->belongsTo(coupons::class,'coupon_id');
    }

    public function products(){
        return $this->hasManyThrough(products::class,orders_items::class,'order_id','id','couponable_id','id');
    }
}
