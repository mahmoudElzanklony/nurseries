<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class coupons extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','ar_name','en_name','code','number','discount','using_once','end_date','status'];

    /*public function products(){
        return $this->hasMany(coupons_products::class,'coupon_id');
    }*/

    public function products(){
        return $this->belongsToMany(products::class,coupons_products::class,
            'coupon_id','product_id');
    }

    public function products_with_all_info(){
        return $this->belongsToMany(products::class,coupons_products::class,
            'coupon_id','product_id');
    }

    public function order_items(){
        return $this->hasManyThrough(orders_items::class,users_coupons::class,'coupon_id','id','id','couponable_id');
    }

    public function users(){
        return $this->hasMany(users_coupons::class,'coupon_id');
    }

}
