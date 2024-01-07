<?php


namespace App\Actions;


use App\Models\custom_orders_sellers;

class RepliesSellersWithAllData
{
    public static function get(){
        return custom_orders_sellers::query()->with('order')
            ->whereHas('reply',function($r){
                $r->whereRaw('custom_orders_sellers_replies.client_reply = "pending" ');
            })

        ->orderBy('id','DESC');
    }
}
