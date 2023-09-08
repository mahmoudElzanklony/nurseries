<?php


namespace App\Actions;


use App\Models\orders;
use App\Models\orders_items;
use App\Models\User;

class OrdersWithAllData
{
    public static function get(){
        $user = User::query()->with('role')->find(auth()->id());
        $orders = orders::query()->with(['items'=>function($e){
            $e->with(['product','features']);
        }])
            ->addSelect([
                'total_items'=>orders_items::query()
                    ->selectRaw('sum(price) as total')
                    ->whereColumn('order_id','=','orders.id')
                    ->limit(1)
            ])
            ->when($user->role->name == 'client',function ($e){
                $e->with('seller')->where('user_id','=',auth()->id());
            })->when($user->role->name == 'seller',function ($q){
                $q->with('client')->where('seller_id','=',auth()->id());
            });
        return $orders;
    }
}
