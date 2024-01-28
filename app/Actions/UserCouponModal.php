<?php


namespace App\Actions;


use App\Models\users_coupons;

class UserCouponModal
{
    public static function make($coupon_id,$id,$coupon_value,$model_name = 'orders_items',$total_price_before_apply = 0){
        users_coupons::query()->create([
            'user_id'=>auth()->id(),
            'coupon_id'=>$coupon_id,
            'couponable_id'=>$id,
            'couponable_type'=>'App\Models\\'.$model_name,
            'coupon_value'=>$coupon_value,
            'total_price_before_apply'=>$total_price_before_apply,
        ]);
        return true;
    }
}
