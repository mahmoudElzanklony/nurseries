<?php


namespace App\Services\sellers;


use App\Enum\OrdersDeliveryCases;
use App\Models\articles;
use App\Models\custom_orders;
use App\Models\followers;
use App\Models\orders;
use App\Models\products;
use App\Repositories\FinancialReconciliationsRepository;

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
                $e->where('content','!=',OrdersDeliveryCases::$completed);
            })->withSum('payment','money')->get()->sum('payment_sum_money')
            +
            custom_orders::query()->with('accepted_seller')
                           ->where('status','!=','completed')
                           ->withSum('payment','money')->get()->sum('payment_sum_money');


        $total_sales = self::my_orders($user_id)->withSum('payment','money')->get()->sum('payment_sum_money')
            +
            custom_orders::query()->whereHas('reply',function($e) use ($user_id){
                $e->whereHas('custom_order_seller',function($q) use ($user_id){
                    $q->where('seller_id','=',$user_id);
                });
            })->withSum('payment','money')->get()->sum('payment_sum_money');


        dd(self::my_orders($user_id)->withSum('payment','money')->get()->sum('payment_sum_money'));
        $financil_repo = new FinancialReconciliationsRepository();
        $orders = $financil_repo->get_orders_to_be_financial(true);
        $active_money = $financil_repo->detect_total_money($orders['orders'],$orders['custom_orders']);

        $output = [
            'active_orders'=>$active_orders,
            'waiting_orders'=>$waiting,
            'pending_money'=>$pending_money,
            'active_money'=>$active_money,
            'total_sales'=>$total_sales,
            'products'=>products::query()->when($user_id != null , function($e) use ($user_id){
                $e->where('user_id','=',$user_id);
            })->count(),
            'my_clients'=>self::my_orders($user_id)->groupBy('user_id')->get()->count(),
            'followers'=>followers::query()->where('following_id','=',$user_id)->count(),
            'articles'=>articles::query()->where('user_id','=',$user_id)->count(),
            'shop-progress'=>($active_orders  * (50/100)) > 100 ? 100 : ($active_orders  * (50/100).'%'),
        ];
        return $output;
    }



}
