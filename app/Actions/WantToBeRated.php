<?php


namespace App\Actions;


use App\Models\orders_items;
use App\Models\orders_items_rates;

class WantToBeRated
{
    public static function check($product_id){
        if(auth()->check()) {
            /*$order = orders_items_rates::query()
                ->where('user_id','=',auth()->id())
                ->whereHas('order_item',function($e) use ($product_id){
                $e->where('product_id','=',$product_id);
            })->first();*/
            $order = orders_items::query()->whereHas('order',function($e){
                $e->where('user_id','=',auth()->id());
            })->where('product_id','=',$product_id)
              ->whereDoesntHave('rate')->first();

            if($order != null){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
