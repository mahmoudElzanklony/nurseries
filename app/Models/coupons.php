<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class coupons extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','ar_name','en_name','code','number','discount','using_once','end_date'];



    public function order_items(){
        return $this->hasManyThrough(orders_items::class,users_coupons::class,'coupon_id','id','id','couponable_id');
    }

    public function users(){
        return $this->hasMany(users_coupons::class,'coupon_id');
    }

}
