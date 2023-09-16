<?php


namespace App\Actions;


use App\Models\areas;
use App\Models\products_delivery;

class DeliveryOfOrder
{
    public static function get($default_address,$product_id){


        $price = products_delivery::query()
            ->where('product_id','=',$product_id)
            ->where('location_id','=',$default_address->area_id)
            ->where('location_type','=','area')->first();
        if($price == null) {
            // check if city available
            $price = products_delivery::query()
                ->where('product_id','=',$product_id)
                ->where('location_id','=',$default_address->area->city->id)
                ->where('location_type','=','city')->first();
            if($price == null){
                $price = products_delivery::query()
                    ->where('product_id','=',$product_id)
                    ->where('location_id','=',$default_address->area->city->government->id)
                    ->where('location_type','=','government')->first();
            }
        }
        return $price;

    }
}
