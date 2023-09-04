<?php


namespace App\Actions;


use App\Models\notifications;
use App\Models\User;

class ShowNotifications
{
    public static function get_data(){
        $user = User::query()->with('role:id,name')->where('id',auth()->id())->first();
        $notifications = notifications::query()->with('sender');
        if($user->role->name == 'client' || $user->role->name == 'marketer'){
            $notifications->where('receiver_id','=',auth()->id());
        }else{
            $notifications->whereHas('receiver',function($q){
                $q->whereHas('role',function ($u){
                    $u->where('name','=','admin');
                });
            });
        }
        return $notifications->orderBy('id','DESC')->pagisnate(8);
    }
}
