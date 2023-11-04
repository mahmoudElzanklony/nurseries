<?php


namespace App\Actions;


use App\Models\orders;

class SellerOrdersAndCustomOrdersAction
{
    public static function get($user_id = null, $time_type = null){
        $orders = orders::query()
                  ->when($user_id != null , function ($e) use ($user_id){
                      $e->where('seller_id','=',$user_id);
                  })
                  ->join('payments','orders.id','=','payments.paymentable_id')
                  ->where('payments.paymentable_type','=','App\Models\orders')
                  ->whereRaw('financial_reconciliation_id is not null')->selectRaw('orders.* , payments.money');
        return $orders;
    }


}
