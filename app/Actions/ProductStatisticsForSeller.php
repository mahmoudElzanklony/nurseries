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
        $statistics = [
            'orders'=>sizeof($orders->get()),
            'profit_money'=>$orders->whereHas('order',function($q){
                $q->where('financial_reconciliation_id','!=',null);
            })->get()->sum('total_price'),
            'pending_money'=>$orders->whereHas('order',function($q){
                $q->whereNull('financial_reconciliation_id');
            })->get()->sum('total_price')
        ];
        return $statistics;
    }
}
