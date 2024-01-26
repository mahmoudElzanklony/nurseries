<?php

namespace App\Actions;

use App\Models\custom_orders;

class CustomOrdersProfitAction
{
    public static function get($user_id = null, $time_type = null){
        $orders = custom_orders::query()->whereHas('accepted_seller')
            ->join('payments','custom_orders.id','=','payments.paymentable_id')
            ->where('payments.paymentable_type','=','App\Models\custom_orders')
            ->whereRaw('financial_reconciliation_id is not null')->selectRaw('custom_orders.* , payments.money');
        return $orders;
    }
}
