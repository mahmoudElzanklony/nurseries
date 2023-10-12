<?php


namespace App\Actions;


use App\Models\custom_orders_sellers;

class RepliesSellersWithAllData
{
    public static function get(){
        return custom_orders_sellers::query()->with('order')->whereHas('order',function($e){
            $e->where('user_id','=',auth()->id());
        })->with(['seller','reply'=>function($e){
                $e->with('images');
            }])->orderBy('id','DESC');
    }
}
