<?php


namespace App\Http\traits\helpers_requests_api;


use App\Actions\OrdersWithAllData;
use App\Http\Resources\FollowerResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\custom_orders;
use App\Models\followers;
use App\Models\orders;
use App\Models\orders_items;
use App\Models\User;

trait ClientHelperApi
{
    public function about_client(){
        $user = User::query()->with(['client_visas'])->find(request('user_id'));
        $last_orders = OrdersWithAllData::get()->where('user_id','=',$user->id)->limit(3)->get();
        $total_money = 0;
        $orders = orders::query()
            ->where('user_id','=',request('user_id'))
            ->with('payment:paymentable_id,money')
            ->get();

        foreach($orders as $order){
            if($order->payment != null) {
                $total_money += $order->payment->money;
            }
        }
        $custom = custom_orders::query()
            ->where('user_id','=',request('user_id'))
            ->with('payment:paymentable_id,money')
            ->get();
        foreach($custom as $order){
            if($order->payment != null) {
                $total_money += $order->payment->money;
            }
        }
        $statistics = [
          'money'=>$total_money,
          'total_products'=>orders_items::query()->whereHas('order',function($e) use ($user){
              $e->where('user_id','=',$user->id);
          })->sum('quantity'),
          'following'=>followers::query()->where('user_id','=',$user->id)->count()
        ];
        $output = [
          'statistics'=>$statistics,
          'last_three_orders'=>OrderResource::collection($last_orders),
          'user_info'=>UserResource::make($user),
          'following_data'=>FollowerResource::collection(followers::query()->with('follower',function($e){
              $e->with('bank_info');
          })->where('user_id','=',$user->id)->get())
        ];
        return messages::success_output('',$output);
    }
}
