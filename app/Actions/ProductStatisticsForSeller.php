<?php


namespace App\Actions;


use App\Models\orders_items;

class ProductStatisticsForSeller
{
    public static function get($id){
        $orders = orders_items::query()
            ->join('orders_items_features','orders_items_features.order_item_id','=','orders_items.id')
            ->selectRaw('(orders_items.quantity * orders_items.price) + orders_items_features.price as total_price')
            ->where('product_id','=',$id);
        $orders_no = sizeof($orders->get());


        $profit_money = $orders->whereHas('order',function($q){
            $q->where('financial_reconciliation_id','!=',null);
        })->get()->sum('total_price');
        $pending_money = $orders->whereHas('order',function($q){
            $q->whereNull('financial_reconciliation_id');
        })->get()->sum('total_price');
        $statistics = [
            'orders'=>$orders_no,
            'profit_money'=>$profit_money,
            'pending_money'=>$pending_money,
            'total_money' => $profit_money + $pending_money
        ];
        return $statistics;
    }

    public static function get_for_company($id){
        $orders = orders_items::query()
            ->where('product_id','=',$id);
        $all_orders  = $orders->count();
        $orders_per_client = $orders->whereHas('order',function($e){
            $e->where('user_id','=',auth()->id());
        })->count();


        $statistics = [
            'all_sales'=>$all_orders,
            'sales_per_user'=>$orders_per_client,
        ];
        return $statistics;
    }
}
