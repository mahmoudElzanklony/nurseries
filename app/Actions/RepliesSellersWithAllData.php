<?php


namespace App\Actions;


use App\Models\custom_orders_sellers;

class RepliesSellersWithAllData
{
    public static function get(){
        return custom_orders_sellers::query()->with('order')
            ->when(auth()->user()->role->name == 'client' || auth()->user()->role->name == 'company' ,function($e){
                $e->whereHas('order',function($e){
                    $e->where('user_id','=',auth()->id());
                });
            })
            ->when(auth()->user()->role->name == 'seller',function($e){
                $e->where('seller_id','=',auth()->id());
            })
            ->orderBy('id','DESC');
    }
}
