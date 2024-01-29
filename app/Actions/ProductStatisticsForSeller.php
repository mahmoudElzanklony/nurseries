<?php


namespace App\Actions;


use App\Models\financial_reconciliations;
use App\Models\orders;
use App\Models\orders_items;
use App\Repositories\FinancialReconciliationsRepository;

class ProductStatisticsForSeller
{
    public static function get($id){
        $orders = orders_items::query()
            ->leftJoin('orders_items_features','orders_items_features.order_item_id','=','orders_items.id')
            ->selectRaw('(orders_items.quantity * orders_items.price) + orders_items_features.price as total_price')
            ->where('product_id','=',$id);
        $orders_no = sizeof($orders->get());


        $financil_repo = new FinancialReconciliationsRepository();
        $orders = orders::query()->whereHas('items',function ($e) use ($id){
            $e->where('product_id','=',$id);
        });
        $pending_money = $financil_repo->detect_total_money($orders,[]);
        $active_profit = financial_reconciliations::query()
            ->where('status','=','completed')
            ->where('seller_id','=',auth()->id())
            ->whereHas('orders',function ($e) use ($id){
                $e->whereHas('items',function($i) use ($id){
                    $i->where('product_id','=',$id);
                });
            })
            ->selectRaw('sum(total_money - ( total_money * admin_profit_percentage / 100 )) as total')->get();
        $active = 0;
        foreach ($active_profit as $value){
            $active += $value->total;
        }

        /*$profit_money = $orders->whereHas('order',function($q){
            $q->where('financial_reconciliation_id','!=',null);
        })->get()->sum('total_price');*/
        $statistics = [
            'orders'=>$orders_no,
            'profit_money'=>$active,
            'pending_money'=>$pending_money,
            'total_money' => $active + $pending_money
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
