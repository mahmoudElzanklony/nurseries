<?php


namespace App\Actions;


use App\Models\custom_orders;
use App\Models\custom_orders_sellers;
use App\Models\User;

class CustomOrdersWithAllData
{
    public static function get(){
        $user = User::query()->with('role')->find(auth()->id());
        if($user->role->name == 'seller'){
            return custom_orders_sellers::query()
                ->where('seller_id',auth()->id())->with(['order','reply.images'])->orderBy('id','DESC');
        }else{
            return custom_orders::query()->where('user_id','=',auth()->id())
                ->with(['images','sellers_alerts'=>function($e){
                $e->with(['seller','reply'=>function($e){
                    $e->with('images');
                }]);
            }])->orderBy('id','DESC');
        }
    }
}
