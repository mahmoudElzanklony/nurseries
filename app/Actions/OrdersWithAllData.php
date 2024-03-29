<?php


namespace App\Actions;


use App\Models\orders;
use App\Models\orders_items;
use App\Models\User;

class OrdersWithAllData
{
    public static function get(){
        $user = User::query()->with('role')->find(auth()->id());
        $orders = orders::query()->with(['payment','shipments_info','address','items'=>function($e){
            $e->with('coupon.coupon')->with(['rate','product'=>function($e){
                $e->when(GetAuthenticatedUser::get_info() != null , function ($e){
                    $e->with('favourite');
                })
                    /*->when(GetAuthenticatedUser::get_info() != null && auth()->user()->role->name == 'seller' , function ($e){
                        $e->where('user_id','=',auth()->id());
                    })*/
                    ->withCount('likes')
                    ->with(['category','images','user','discounts'=>function($e){
                        $e->whereRaw('CURDATE() >= start_date and CURDATE() <= end_date');
                    }
                        ,'features.feature.image','answers.question.image']);
            },'features']);
        }])
            ->addSelect([
                'total_items'=>orders_items::query()
                    ->selectRaw('sum(price) as total')
                    ->whereColumn('order_id','=','orders.id')
                    ->limit(1)
            ])
            ->when($user->role->name == 'client' || $user->role->name == 'company',function ($e){
                $e->with('seller')->where('user_id','=',auth()->id());
            })->when($user->role->name == 'seller',function ($q){
                $q->with('client')->where('seller_id','=',auth()->id());
            })->when($user->role->name == 'admin',function ($q){
                $q->with('client')->with('seller');
            });
        return $orders;
    }
}
