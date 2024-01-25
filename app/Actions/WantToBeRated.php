<?php


namespace App\Actions;


use App\Models\orders_items;

class WantToBeRated
{
    public static function check($product_id){
        if(auth()->check()) {
            $order = orders_items::query()->whereHas('order',function($e){
                $e->where('user_id','=',auth()->id());
            })->where('product_id','=',$product_id)
              ->whereDoesntHave('rate')->first();
            dd($order);
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
