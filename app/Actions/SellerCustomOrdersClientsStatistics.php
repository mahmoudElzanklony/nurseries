<?php

namespace App\Actions;

use App\Models\custom_orders;

class SellerCustomOrdersClientsStatistics
{
    public static function get($user_id = null, $time_type = null){
        $data = custom_orders::query()->whereHas('accepted_seller')
            ->join('payments','custom_orders.id','=','payments.paymentable_id')
            ->where('payments.paymentable_type','=','App\Models\custom_orders')
            ->selectRaw('custom_orders.* , payments.money');

        return $data;
    }
}
