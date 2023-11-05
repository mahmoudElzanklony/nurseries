<?php


namespace App\Actions;


use App\Models\users_coupons;

class UserCouponModal
{
    public static function make($coupon_id,$id,$coupon_value,$model_name = 'orders_items'){
        users_coupons::query()->create([
            'user_id'=>auth()->id(),
            'coupon_id'=>$coupon_id,
            'couponable_id'=>$id,
            'couponable_type'=>'App\Models\\'.$model_name,
            'coupon_value'=>$coupon_value,
        ]);
        return true;
    }
}
