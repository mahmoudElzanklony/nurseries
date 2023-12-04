<?php


namespace App\Actions;


use App\Models\orders;

class SellerOrdersClientsStatistics
{
    public static function get($user_id = null, $time_type = null){
    $clients = orders::query()
        ->when($user_id != null , function ($e) use ($user_id){
            $e->where('seller_id','=',$user_id);
        })->when(auth()->check() && auth()->user()->role->name == 'seller' , function ($e) use ($user_id){
            $e->where('seller_id','=',auth()->id());
        });
    return $clients;
}
}
