<?php


namespace App\Services\sellers;


use App\Enum\OrdersDeliveryCases;
use App\Models\articles;
use App\Models\custom_orders;
use App\Models\followers;
use App\Models\orders;
use App\Models\products;

class StatisticsService
{
    public static function my_orders($user_id = null){
        return orders::query()->when($user_id != null , function($e) use ($user_id){
            $e->whereHas('seller',function($e) use ($user_id){
                $e->where('seller_id','=',$user_id);
            });
        });
    }

    public static function orders_money_products($user_id = null){
        $active_orders = self::my_orders($user_id)->wherehas('last_shipment_info',function($e){
            $e->where('content','=',OrdersDeliveryCases::$delivery);
        })->count();
        $waiting = self::my_orders($user_id)->wherehas('last_shipment_info',function($e){
            $e->where('content','!=',OrdersDeliveryCases::$delivery);
        })->count();


        $pending_money = self::my_orders($user_id)->wherehas('last_shipment_info',function($e){
                $e->where('content','=',OrdersDeliveryCases::$delivery);
            })->withSum('payment','money')->get()->sum('payment_sum_money')
            +
            custom_orders::query()->whereHas('reply',function($e) use ($user_id){
                $e->whereHas('custom_order_seller',function($q) use ($user_id){
                    $q->where('seller_id','=',$user_id);
                });
            })->withSum('payment','money')->get()->sum('payment_sum_money');

        $output = [
            'active_orders'=>$active_orders,
            'waiting_orders'=>$waiting,
            'pending_money'=>$pending_money,
            'active_money'=>self::my_orders($user_id)->whereRaw('financial_reconciliation_id is not null')->withSum('payment','money')->get()->sum('payment_sum_money'),
            'products'=>products::query()->when($user_id != null , function($e) use ($user_id){
                $e->where('user_id','=',$user_id);
            })->count(),
            'my_clients'=>self::my_orders($user_id)->groupBy('user_id')->get()->count(),
            'followers'=>followers::query()->where('following_id','=',$user_id)->count(),
            'articles'=>articles::query()->where('user_id','=',$user_id)->count(),
            'shop-progress'=>$active_orders * (50/100).'%'
        ];
        return $output;
    }



}
