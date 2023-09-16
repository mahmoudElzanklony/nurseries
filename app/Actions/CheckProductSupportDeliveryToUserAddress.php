<?php


namespace App\Actions;


use App\Models\products_delivery;

class CheckProductSupportDeliveryToUserAddress
{
    public static function check($product_id,$user_default_address){
        $product_deliveries = products_delivery::query()->where('product_id','=',$product_id)->get();
        foreach($product_deliveries as $del){
            if($del->location_id == $user_default_address->area_id && $del->location_type == 'area'){
                return true;
            }else if($del->location_id == $user_default_address->area->city->id && $del->location_type == 'city'){
                return true;
            }else if($del->location_id == $user_default_address->area->city->government->id && $del->location_type == 'government'){
                return true;
            }
        }
        return false;
    }
}
