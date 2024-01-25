<?php

namespace App\Actions;

use App\Models\custom_orders;

class SellerCustomOrdersClientsStatistics
{
    public static function get($user_id = null, $time_type = null){
        $data = custom_orders::query()->whereHas('reply',function($e) use ($user_id){
            $e->whereHas('custom_order_seller',function($q) use ($user_id){
                $q->where('seller_id','=',$user_id);
            });
        })
            ->join('payments','custom_orders.id','=','payments.paymentable_id')
            ->where('payments.paymentable_type','=','App\Models\custom_orders')
            ->selectRaw('custom_orders.* , payments.money');

        return $data;
    }
}
